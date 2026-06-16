<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\NotifySignalementUpdateJob;
use App\Models\Signalement;
use App\Services\AuditLogger;
use App\Services\EmergencyAnalysisService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminAiReviewController extends AdminController
{
    public function index(Request $request)
    {
        $query = Signalement::with(['category', 'user'])
            ->where('ai_verdict', EmergencyAnalysisService::VERDICT_REVIEW)
            ->where('statut', 'en_cours')
            ->orderByDesc('ai_priority_rank')
            ->orderByDesc('reported_at');

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        return view('admin.ai-review.index', [
            'layout' => 'admin',
            'user' => $this->adminUser()->toViewArray(),
            'signalements' => $query->paginate(10)->withQueryString(),
            'pendingCount' => Signalement::where('ai_verdict', EmergencyAnalysisService::VERDICT_REVIEW)
                ->where('statut', 'en_cours')->count(),
            'filters' => $request->only(['q']),
        ]);
    }

    public function approve(string $reference)
    {
        $signalement = Signalement::where('reference', $reference)->firstOrFail();

        $signalement->update(['ai_verdict' => EmergencyAnalysisService::VERDICT_APPROVED]);

        $signalement->timelineSteps()->create([
            'label' => 'Validation manuelle — signalement approuvé par l\'administration',
            'done' => true,
            'occurred_at' => Carbon::now(),
            'sort_order' => ($signalement->timelineSteps()->max('sort_order') ?? 0) + 1,
        ]);

        AuditLogger::log('signalement.ai_approved', $signalement);

        NotifySignalementUpdateJob::dispatchSync(
            $signalement->id,
            'Votre signalement a été validé par un opérateur après vérification IA.',
            'validation'
        );

        return back()->with('success', "Signalement {$reference} approuvé.");
    }

    public function reject(string $reference)
    {
        $signalement = Signalement::where('reference', $reference)->firstOrFail();

        $signalement->update([
            'ai_verdict' => EmergencyAnalysisService::VERDICT_REJECTED,
            'statut' => 'annule',
        ]);

        $signalement->timelineSteps()->create([
            'label' => 'Rejeté après vérification IA — fausse urgence confirmée',
            'done' => true,
            'occurred_at' => Carbon::now(),
            'sort_order' => ($signalement->timelineSteps()->max('sort_order') ?? 0) + 1,
        ]);

        AuditLogger::log('signalement.ai_rejected', $signalement);

        NotifySignalementUpdateJob::dispatchSync(
            $signalement->id,
            'Votre signalement a été rejeté après vérification par l\'administration.',
            'rejet'
        );

        return back()->with('success', "Signalement {$reference} rejeté.");
    }
}
