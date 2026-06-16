<?php

namespace App\Http\Controllers\Admin;

use App\Models\Signalement;
use App\Services\EmergencyAnalysisService;

class AdminDashboardController extends AdminController
{
    public function index()
    {
        $recent = Signalement::with(['category', 'user'])
            ->orderByDesc('ai_priority_rank')
            ->orderByDesc('reported_at')
            ->limit(8)
            ->get();

        $byCategory = $this->signalementsByCategory();
        $maxCategory = max(array_column($byCategory, 'count') ?: [1]);

        $byGravite = $this->signalementsByGravite();
        $maxGravite = max($byGravite ?: [1]);

        return view('admin.dashboard', [
            'layout' => 'admin',
            'user' => $this->adminUser()->toViewArray(),
            'stats' => $this->globalStats(),
            'recent' => $recent,
            'pendingReview' => Signalement::with(['category', 'user'])
                ->where('ai_verdict', EmergencyAnalysisService::VERDICT_REVIEW)
                ->where('statut', 'en_cours')
                ->orderByDesc('ai_priority_rank')
                ->limit(5)
                ->get(),
            'byCategory' => $byCategory,
            'maxCategory' => $maxCategory,
            'byGravite' => $byGravite,
            'maxGravite' => $maxGravite,
        ]);
    }
}
