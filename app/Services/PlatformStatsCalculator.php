<?php

namespace App\Services;

use App\Models\PlatformStat;
use App\Models\Signalement;
use Carbon\Carbon;

class PlatformStatsCalculator
{
    public static function sync(): void
    {
        $total = Signalement::where('statut', 'termine')->count();
        $all = Signalement::count();
        $successRate = $all > 0 ? round(($total / $all) * 100).'%' : '0%';

        $avgMinutes = Signalement::where('statut', 'termine')
            ->with('timelineSteps')
            ->get()
            ->map(function (Signalement $sig) {
                $start = $sig->reported_at;
                $end = $sig->timelineSteps->where('label', 'Intervention clôturée')->first()?->occurred_at;

                return ($start && $end) ? $start->diffInMinutes($end) : null;
            })
            ->filter()
            ->avg();

        $avgLabel = $avgMinutes ? round($avgMinutes).' min' : '—';

        PlatformStat::updateOrCreate(['key' => 'urgences_traitees'], [
            'value' => (string) $total,
            'label' => 'Urgences traitées',
        ]);

        PlatformStat::updateOrCreate(['key' => 'taux_succes'], [
            'value' => $successRate,
            'label' => 'Taux de succès',
        ]);

        PlatformStat::updateOrCreate(['key' => 'temps_moyen'], [
            'value' => $avgLabel,
            'label' => 'Temps moyen d\'intervention',
        ]);
    }
}
