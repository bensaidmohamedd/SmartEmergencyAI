<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\PlatformStat;
use App\Models\Signalement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    private function currentUser(): User
    {
        return Auth::user();
    }

    private function signalementsQuery()
    {
        return Signalement::with(['category', 'timelineSteps'])
            ->orderByDesc('reported_at');
    }

    private function dashboardStats(User $user): array
    {
        $query = Signalement::where('user_id', $user->id);

        return [
            'total' => (clone $query)->count(),
            'en_cours' => (clone $query)->where('statut', 'en_cours')->count(),
            'termines' => (clone $query)->where('statut', 'termine')->count(),
            'critiques' => (clone $query)->where('gravite', 'critique')->count(),
        ];
    }

    private function features(): array
    {
        return [
            ['icon' => 'lightning-charge-fill', 'title' => 'Signalement express', 'description' => 'Déclarez une urgence en moins de 30 secondes depuis votre mobile, partout au Niger.'],
            ['icon' => 'cpu-fill', 'title' => 'Analyse IA instantanée', 'description' => 'L\'IA évalue la gravité et oriente les secours nigériens (pompiers, police, SAMU) automatiquement.'],
            ['icon' => 'geo-alt-fill', 'title' => 'Géolocalisation précise', 'description' => 'Votre position à Niamey ou ailleurs au Niger est transmise aux équipes de secours.'],
            ['icon' => 'camera-fill', 'title' => 'Preuves multimédia', 'description' => 'Ajoutez photos et vidéos pour aider les intervenants à mieux comprendre la situation.'],
            ['icon' => 'bell-fill', 'title' => 'Alertes en temps réel', 'description' => 'Recevez des notifications à chaque étape : analyse, intervention, résolution.'],
            ['icon' => 'shield-lock-fill', 'title' => 'Données sécurisées', 'description' => 'Vos informations sont chiffrées et protégées selon les normes nigériennes en vigueur.'],
        ];
    }

    private function steps(): array
    {
        return [
            ['icon' => 'pencil-square', 'number' => '01', 'title' => 'Signalez', 'description' => 'Remplissez le formulaire, choisissez la catégorie et décrivez la situation.'],
            ['icon' => 'robot', 'number' => '02', 'title' => 'Analyse IA', 'description' => 'Notre IA analyse votre signalement et détermine le niveau de priorité.'],
            ['icon' => 'send-fill', 'number' => '03', 'title' => 'Alerte envoyée', 'description' => 'Les services compétents sont notifiés immédiatement avec votre localisation.'],
            ['icon' => 'check-circle-fill', 'number' => '04', 'title' => 'Suivi & résolution', 'description' => 'Suivez l\'intervention en direct jusqu\'à la clôture de l\'urgence.'],
        ];
    }

    public function home()
    {
        return view('pages.home', [
            'layout' => 'guest',
            'features' => $this->features(),
            'steps' => $this->steps(),
            'platformStats' => PlatformStat::pluck('value', 'key'),
        ]);
    }

    public function dashboard()
    {
        $user = $this->currentUser();
        $recent = $this->signalementsQuery()
            ->where('user_id', $user->id)
            ->limit(3)
            ->get()
            ->map->toViewArray();

        return view('pages.dashboard', [
            'layout' => 'app',
            'user' => $user->toViewArray(),
            'stats' => $this->dashboardStats($user),
            'recent' => $recent,
        ]);
    }

    public function report()
    {
        $user = $this->currentUser();

        return view('pages.report', [
            'layout' => 'app',
            'user' => $user->toViewArray(),
            'categories' => Category::orderBy('name')->pluck('name'),
        ]);
    }

    public function history()
    {
        $user = $this->currentUser();
        $signalements = $this->signalementsQuery()
            ->where('user_id', $user->id)
            ->get()
            ->map->toViewArray();

        return view('pages.history', [
            'layout' => 'app',
            'user' => $user->toViewArray(),
            'signalements' => $signalements,
        ]);
    }

    public function show(string $id)
    {
        $user = $this->currentUser();
        $signalement = Signalement::with(['category', 'timelineSteps'])
            ->where('reference', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return view('pages.show', [
            'layout' => 'app',
            'user' => $user->toViewArray(),
            'signalement' => $signalement->toViewArray(),
        ]);
    }
}
