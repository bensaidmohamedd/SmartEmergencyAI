<?php

namespace App\Http\Controllers\Admin;

use App\Models\EmergencyService;
use App\Models\Signalement;
use App\Services\AuditLogger;
use App\Services\NearestServiceFinder;
use Illuminate\Http\Request;

class AdminOperationsController extends AdminController
{
    public function index()
    {
        $critical = Signalement::with(['category', 'user', 'assignedService'])
            ->where('statut', 'en_cours')
            ->where(function ($q) {
                $q->where('gravite', 'critique')
                    ->orWhere('ai_priority_rank', '>=', 75)
                    ->orWhere('ai_score', '>=', 80);
            })
            ->orderByDesc('ai_priority_rank')
            ->orderByDesc('ai_score')
            ->orderByDesc('reported_at')
            ->get();

        $active = Signalement::with(['category', 'user'])
            ->where('statut', 'en_cours')
            ->orderByDesc('ai_priority_rank')
            ->orderByDesc('ai_score')
            ->orderByDesc('reported_at')
            ->limit(20)
            ->get();

        $services = EmergencyService::withCount([
            'signalements' => fn ($q) => $q->where('statut', 'en_cours'),
        ])->get();

        return view('admin.operations.index', [
            'layout' => 'admin',
            'user' => $this->adminUser()->toViewArray(),
            'critical' => $critical,
            'active' => $active,
            'services' => $services,
            'stats' => $this->globalStats(),
        ]);
    }

    public function assign(Request $request, string $reference)
    {
        $signalement = Signalement::where('reference', $reference)->firstOrFail();

        $validated = $request->validate([
            'assigned_service_id' => ['required', 'exists:emergency_services,id'],
        ]);

        $signalement->update($validated);
        AuditLogger::log('signalement.dispatch', $signalement, $validated);

        return back()->with('success', 'Unité de secours assignée au signalement.');
    }

    public function suggest(string $reference)
    {
        $signalement = Signalement::where('reference', $reference)->firstOrFail();

        if (! $signalement->latitude || ! $signalement->longitude) {
            return back()->withErrors(['gps' => 'Pas de coordonnées GPS pour ce signalement.']);
        }

        $primaryType = match ($signalement->category->name) {
            'Incendie', 'Inondation', 'Coupure électrique' => 'pompiers',
            'Urgence médicale', 'Accident' => 'samu',
            'Agression' => 'police',
            default => 'pompiers',
        };

        $nearest = NearestServiceFinder::find(
            $signalement->latitude,
            $signalement->longitude,
            $primaryType,
            1
        );

        if (empty($nearest)) {
            return back()->withErrors(['service' => 'Aucun service trouvé à proximité.']);
        }

        $signalement->update(['assigned_service_id' => $nearest[0]['service']->id]);
        AuditLogger::log('signalement.auto_dispatch', $signalement, [
            'service' => $nearest[0]['service']->name,
            'distance_km' => $nearest[0]['distance_km'],
        ]);

        return back()->with('success', "Dispatch automatique : {$nearest[0]['service']->name} ({$nearest[0]['distance_km']} km).");
    }
}
