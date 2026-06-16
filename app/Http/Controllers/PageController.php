<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\EmergencyService;
use App\Models\PlatformStat;
use App\Models\Signalement;
use App\Models\User;
use App\Services\NearestServiceFinder;
use Illuminate\Http\Request;
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
            'emergencyServices' => EmergencyService::orderBy('type')->limit(4)->get(),
            'categories' => Category::orderBy('name')->pluck('name'),
        ]);
    }

    public function report(Request $request)
    {
        $user = $this->currentUser();

        return view('pages.report', [
            'layout' => 'app',
            'user' => $user->toViewArray(),
            'categories' => Category::orderBy('name')->pluck('name'),
            'prefillCategory' => $request->query('category'),
        ]);
    }

    public function quickReport(Request $request)
    {
        $user = $this->currentUser();

        return view('pages.quick-report', [
            'layout' => 'app',
            'user' => $user->toViewArray(),
            'categories' => Category::orderBy('name')->pluck('name'),
            'prefillCategory' => $request->query('category'),
        ]);
    }

    public function services(Request $request)
    {
        $query = EmergencyService::query()->orderBy('type')->orderBy('name');

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('zone', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        return view('pages.services', [
            'layout' => 'app',
            'user' => $this->currentUser()->toViewArray(),
            'services' => $query->get(),
            'filters' => $request->only(['type', 'q']),
        ]);
    }

    public function history(Request $request)
    {
        $user = $this->currentUser();
        $query = $this->signalementsQuery()->where('user_id', $user->id);

        if ($request->filled('gravite') && $request->gravite !== 'all') {
            $query->where('gravite', $request->gravite);
        }
        if ($request->filled('statut') && $request->statut !== 'all') {
            $query->where('statut', $request->statut);
        }
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('localisation', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $signalements = $query->paginate(9)->withQueryString();

        return view('pages.history', [
            'layout' => 'app',
            'user' => $user->toViewArray(),
            'signalements' => $signalements,
            'filters' => $request->only(['q', 'gravite', 'statut']),
        ]);
    }

    public function impact()
    {
        $stats = [
            'total' => Signalement::count(),
            'incendies' => Signalement::whereHas('category', fn ($q) => $q->where('name', 'Incendie'))->count(),
            'critiques' => Signalement::where('gravite', 'critique')->count(),
            'termines' => Signalement::where('statut', 'termine')->count(),
            'avg_score' => (int) Signalement::whereNotNull('ai_score')->avg('ai_score'),
            'avg_response' => (int) Signalement::whereNotNull('estimated_response_min')->avg('estimated_response_min'),
            'services' => EmergencyService::count(),
        ];

        return view('pages.impact', [
            'layout' => 'guest',
            'stats' => $stats,
            'platformStats' => PlatformStat::pluck('value', 'key'),
            'recentIncendies' => Signalement::with('category')
                ->whereHas('category', fn ($q) => $q->where('name', 'Incendie'))
                ->orderByDesc('reported_at')
                ->limit(5)
                ->get(),
        ]);
    }

    public function show(string $id)
    {
        $user = $this->currentUser();
        $signalement = Signalement::with(['category', 'timelineSteps', 'assignedService'])
            ->where('reference', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $nearestServices = ($signalement->latitude && $signalement->longitude)
            ? NearestServiceFinder::find($signalement->latitude, $signalement->longitude, null, 3)
            : [];

        return view('pages.show', [
            'layout' => 'app',
            'user' => $user->toViewArray(),
            'signalement' => $signalement->toViewArray(),
            'nearestServices' => $nearestServices,
        ]);
    }
}
