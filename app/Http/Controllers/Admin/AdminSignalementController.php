<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\NotifySignalementUpdateJob;
use App\Models\Category;
use App\Models\EmergencyService;
use App\Models\Signalement;
use App\Models\SignalementTimelineStep;
use App\Services\AuditLogger;
use App\Services\PlatformStatsCalculator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminSignalementController extends AdminController
{
    public function index(Request $request)
    {
        $query = $this->filteredQuery($request);

        $signalements = $query->paginate(12)->withQueryString();

        return view('admin.signalements.index', [
            'layout' => 'admin',
            'user' => $this->adminUser()->toViewArray(),
            'signalements' => $signalements,
            'categories' => Category::orderBy('name')->get(),
            'filters' => $request->only(['q', 'gravite', 'statut', 'category_id', 'ai_verdict']),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $signalements = $this->filteredQuery($request)->get();

        AuditLogger::log('signalements.export', null, ['count' => $signalements->count()]);

        return response()->streamDownload(function () use ($signalements) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Référence', 'Catégorie', 'Citoyen', 'Email', 'Description', 'Localisation', 'Gravité', 'Statut', 'Date'], ';');

            foreach ($signalements as $sig) {
                fputcsv($handle, [
                    $sig->reference,
                    $sig->category->name,
                    $sig->user->name,
                    $sig->user->email,
                    $sig->description,
                    $sig->localisation,
                    $sig->gravite,
                    $sig->statut,
                    $sig->reported_at->format('d/m/Y H:i'),
                ], ';');
            }

            fclose($handle);
        }, 'signalements-'.date('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function show(string $reference)
    {
        $signalement = Signalement::with(['category', 'user', 'timelineSteps'])
            ->where('reference', $reference)
            ->firstOrFail();

        return view('admin.signalements.show', [
            'layout' => 'admin',
            'user' => $this->adminUser()->toViewArray(),
            'signalement' => $signalement,
            'data' => $signalement->toViewArray(),
            'emergencyServices' => EmergencyService::orderBy('type')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, string $reference)
    {
        $signalement = Signalement::where('reference', $reference)->firstOrFail();
        $oldStatut = $signalement->statut;

        $validated = $request->validate([
            'statut' => ['required', 'in:en_cours,termine,annule'],
            'gravite' => ['required', 'in:critique,elevee,moyenne,faible'],
        ]);

        $signalement->update($validated);

        if ($validated['statut'] === 'termine') {
            $signalement->timelineSteps()->update(['done' => true]);
            $signalement->timelineSteps()
                ->whereNull('occurred_at')
                ->update(['occurred_at' => Carbon::now()]);
        }

        AuditLogger::log('signalement.update', $signalement, $validated);

        if ($oldStatut !== $validated['statut']) {
            $message = match ($validated['statut']) {
                'termine' => 'Votre signalement a été clôturé par les services d\'urgence.',
                'annule' => 'Votre signalement a été annulé par l\'administration.',
                default => 'Le statut de votre signalement a été mis à jour : en cours d\'intervention.',
            };
            NotifySignalementUpdateJob::dispatch($signalement->id, $message, 'statut');
        }

        PlatformStatsCalculator::sync();

        return back()->with('success', 'Signalement mis à jour avec succès.');
    }

    public function updateTimeline(Request $request, string $reference, SignalementTimelineStep $step)
    {
        $signalement = Signalement::where('reference', $reference)->firstOrFail();

        if ($step->signalement_id !== $signalement->id) {
            abort(404);
        }

        $validated = $request->validate(['done' => ['required']]);
        $done = filter_var($validated['done'], FILTER_VALIDATE_BOOLEAN);

        $step->update([
            'done' => $done,
            'occurred_at' => $done ? ($step->occurred_at ?? Carbon::now()) : null,
        ]);

        $allDone = $signalement->timelineSteps()->where('done', false)->doesntExist();
        if ($allDone) {
            $signalement->update(['statut' => 'termine']);
        } elseif ($signalement->statut === 'termine') {
            $signalement->update(['statut' => 'en_cours']);
        }

        AuditLogger::log('signalement.timeline', $signalement, ['step' => $step->label, 'done' => $done]);

        NotifySignalementUpdateJob::dispatch(
            $signalement->id,
            'Étape mise à jour : '.$step->label.($done ? ' (terminée)' : ' (en attente)'),
            'timeline'
        );

        PlatformStatsCalculator::sync();

        return back()->with('success', 'Étape de la timeline mise à jour.');
    }

    public function destroy(string $reference)
    {
        $signalement = Signalement::where('reference', $reference)->firstOrFail();
        AuditLogger::log('signalement.delete', $signalement);
        $signalement->delete();
        PlatformStatsCalculator::sync();

        return redirect()->route('admin.signalements.index')->with('success', 'Signalement supprimé.');
    }

    private function filteredQuery(Request $request)
    {
        $query = Signalement::with(['category', 'user'])
            ->orderByDesc('ai_priority_rank')
            ->orderByDesc('ai_score')
            ->orderByDesc('reported_at');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('localisation', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('gravite') && $request->gravite !== 'all') {
            $query->where('gravite', $request->gravite);
        }

        if ($request->filled('statut') && $request->statut !== 'all') {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('category_id') && $request->category_id !== 'all') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('ai_verdict') && $request->ai_verdict !== 'all') {
            $query->where('ai_verdict', $request->ai_verdict);
        }

        return $query;
    }
}
