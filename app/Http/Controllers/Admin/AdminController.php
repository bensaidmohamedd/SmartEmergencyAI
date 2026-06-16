<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Signalement;
use App\Models\User;
use App\Services\EmergencyAnalysisService;
use Illuminate\Support\Facades\Auth;

abstract class AdminController extends Controller
{
    protected function adminUser(): User
    {
        return Auth::user();
    }

    protected function globalStats(): array
    {
        $query = Signalement::query();

        return [
            'total' => (clone $query)->count(),
            'en_cours' => (clone $query)->where('statut', 'en_cours')->count(),
            'termines' => (clone $query)->where('statut', 'termine')->count(),
            'critiques' => (clone $query)->where('gravite', 'critique')->count(),
            'urgences_actives' => (clone $query)->where('statut', 'en_cours')->where('gravite', 'critique')->count(),
            'users' => User::where('role', User::ROLE_CITOYEN)->count(),
            'categories' => Category::count(),
            'pending_ai_review' => Signalement::where('ai_verdict', EmergencyAnalysisService::VERDICT_REVIEW)
                ->where('statut', 'en_cours')->count(),
        ];
    }

    protected function signalementsByCategory(): array
    {
        return Category::withCount('signalements')
            ->orderByDesc('signalements_count')
            ->get()
            ->map(fn (Category $category) => [
                'name' => $category->name,
                'count' => $category->signalements_count,
            ])
            ->all();
    }

    protected function signalementsByGravite(): array
    {
        $counts = Signalement::selectRaw('gravite, COUNT(*) as total')
            ->groupBy('gravite')
            ->pluck('total', 'gravite');

        return collect(['critique', 'elevee', 'moyenne', 'faible'])
            ->mapWithKeys(fn (string $gravite) => [$gravite => (int) ($counts[$gravite] ?? 0)])
            ->all();
    }
}
