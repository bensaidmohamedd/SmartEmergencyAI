<?php

namespace App\Http\Controllers;

/**
 * Contrôleur frontend MVP — données fictives (aucune logique backend).
 */
class PageController extends Controller
{
    private function user(): array
    {
        return [
            'name' => 'Ben Saïd',
            'email' => 'ben.said@email.ne',
            'phone' => '+227 87 14 51 44',
            'avatar' => 'https://ui-avatars.com/api/?name=Ben+Said&background=1877F2&color=fff&size=128',
        ];
    }

    private function categories(): array
    {
        return [
            'Incendie',
            'Accident',
            'Agression',
            'Inondation',
            'Coupure électrique',
            'Urgence médicale',
        ];
    }

    /** Signalements fictifs pour historique + détail */
    private function signalements(): array
    {
        return [
            [
                'id' => 'SIG-001',
                'categorie' => 'Incendie',
                'description' => 'Fumée dense au 3e étage du bâtiment B. Plusieurs personnes bloquées dans les escaliers.',
                'localisation' => 'Avenue de la République, Plateau — Niamey',
                'date' => '10/06/2026',
                'heure' => '14:32',
                'gravite' => 'critique',
                'statut' => 'en_cours',
                'photo' => 'https://images.unsplash.com/photo-1547056979-94710663f979?w=600&h=400&fit=crop',
                'timeline' => [
                    ['label' => 'Signalement reçu', 'done' => true, 'time' => '10/06/2026 14:32'],
                    ['label' => 'Analyse IA terminée', 'done' => true, 'time' => '10/06/2026 14:33'],
                    ['label' => 'Intervention en cours', 'done' => true, 'time' => '10/06/2026 14:40'],
                    ['label' => 'Intervention clôturée', 'done' => false, 'time' => null],
                ],
            ],
            [
                'id' => 'SIG-002',
                'categorie' => 'Accident',
                'description' => 'Collision entre deux véhicules à l\'intersection. Blessés légers signalés.',
                'localisation' => 'Boulevard Mali Bero, Niamey',
                'date' => '09/06/2026',
                'heure' => '09:15',
                'gravite' => 'elevee',
                'statut' => 'en_cours',
                'photo' => 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=600&h=400&fit=crop',
                'timeline' => [
                    ['label' => 'Signalement reçu', 'done' => true, 'time' => '09/06/2026 09:15'],
                    ['label' => 'Analyse IA terminée', 'done' => true, 'time' => '09/06/2026 09:16'],
                    ['label' => 'Intervention en cours', 'done' => true, 'time' => '09/06/2026 09:25'],
                    ['label' => 'Intervention clôturée', 'done' => false, 'time' => null],
                ],
            ],
            [
                'id' => 'SIG-003',
                'categorie' => 'Urgence médicale',
                'description' => 'Personne âgée inconsciente dans le parc. Pulsations faibles détectées.',
                'localisation' => 'Parc Wadata, Niamey',
                'date' => '08/06/2026',
                'heure' => '18:45',
                'gravite' => 'critique',
                'statut' => 'termine',
                'photo' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=600&h=400&fit=crop',
                'timeline' => [
                    ['label' => 'Signalement reçu', 'done' => true, 'time' => '08/06/2026 18:45'],
                    ['label' => 'Analyse IA terminée', 'done' => true, 'time' => '08/06/2026 18:46'],
                    ['label' => 'Intervention en cours', 'done' => true, 'time' => '08/06/2026 18:50'],
                    ['label' => 'Intervention clôturée', 'done' => true, 'time' => '08/06/2026 19:30'],
                ],
            ],
            [
                'id' => 'SIG-004',
                'categorie' => 'Inondation',
                'description' => 'Sous-sol inondé suite à une rupture de canalisation. Risque pour les installations électriques.',
                'localisation' => 'Quartier Lazaret, Niamey',
                'date' => '07/06/2026',
                'heure' => '11:20',
                'gravite' => 'moyenne',
                'statut' => 'termine',
                'photo' => 'https://images.unsplash.com/photo-1547036967-23d11aacaee0?w=600&h=400&fit=crop',
                'timeline' => [
                    ['label' => 'Signalement reçu', 'done' => true, 'time' => '07/06/2026 11:20'],
                    ['label' => 'Analyse IA terminée', 'done' => true, 'time' => '07/06/2026 11:21'],
                    ['label' => 'Intervention en cours', 'done' => true, 'time' => '07/06/2026 11:35'],
                    ['label' => 'Intervention clôturée', 'done' => true, 'time' => '07/06/2026 14:00'],
                ],
            ],
            [
                'id' => 'SIG-005',
                'categorie' => 'Agression',
                'description' => 'Altercation violente devant la gare. Témoins sur place.',
                'localisation' => 'Gare routière, Niamey',
                'date' => '06/06/2026',
                'heure' => '22:08',
                'gravite' => 'elevee',
                'statut' => 'termine',
                'photo' => 'https://images.unsplash.com/photo-1521791136064-7986c2920216?w=600&h=400&fit=crop',
                'timeline' => [
                    ['label' => 'Signalement reçu', 'done' => true, 'time' => '06/06/2026 22:08'],
                    ['label' => 'Analyse IA terminée', 'done' => true, 'time' => '06/06/2026 22:09'],
                    ['label' => 'Intervention en cours', 'done' => true, 'time' => '06/06/2026 22:15'],
                    ['label' => 'Intervention clôturée', 'done' => true, 'time' => '06/06/2026 23:00'],
                ],
            ],
            [
                'id' => 'SIG-006',
                'categorie' => 'Coupure électrique',
                'description' => 'Panne générale dans le quartier. Ascenseurs bloqués.',
                'localisation' => 'Grand Marché, Niamey',
                'date' => '05/06/2026',
                'heure' => '16:50',
                'gravite' => 'faible',
                'statut' => 'termine',
                'photo' => 'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?w=600&h=400&fit=crop',
                'timeline' => [
                    ['label' => 'Signalement reçu', 'done' => true, 'time' => '05/06/2026 16:50'],
                    ['label' => 'Analyse IA terminée', 'done' => true, 'time' => '05/06/2026 16:51'],
                    ['label' => 'Intervention en cours', 'done' => true, 'time' => '05/06/2026 17:00'],
                    ['label' => 'Intervention clôturée', 'done' => true, 'time' => '05/06/2026 18:30'],
                ],
            ],
        ];
    }

    private function dashboardStats(): array
    {
        return [
            'total' => 12,
            'en_cours' => 3,
            'termines' => 9,
            'critiques' => 2,
        ];
    }

    /** Fonctionnalités — page d'accueil */
    private function features(): array
    {
        return [
            [
                'icon' => 'lightning-charge-fill',
                'title' => 'Signalement express',
                'description' => 'Déclarez une urgence en moins de 30 secondes depuis votre mobile, partout au Niger.',
            ],
            [
                'icon' => 'cpu-fill',
                'title' => 'Analyse IA instantanée',
                'description' => 'L\'IA évalue la gravité et oriente les secours nigériens (pompiers, police, SAMU) automatiquement.',
            ],
            [
                'icon' => 'geo-alt-fill',
                'title' => 'Géolocalisation précise',
                'description' => 'Votre position à Niamey ou ailleurs au Niger est transmise aux équipes de secours.',
            ],
            [
                'icon' => 'camera-fill',
                'title' => 'Preuves multimédia',
                'description' => 'Ajoutez photos et vidéos pour aider les intervenants à mieux comprendre la situation.',
            ],
            [
                'icon' => 'bell-fill',
                'title' => 'Alertes en temps réel',
                'description' => 'Recevez des notifications à chaque étape : analyse, intervention, résolution.',
            ],
            [
                'icon' => 'shield-lock-fill',
                'title' => 'Données sécurisées',
                'description' => 'Vos informations sont chiffrées et protégées selon les normes nigériennes en vigueur.',
            ],
        ];
    }

    /** Étapes "Comment ça marche" */
    private function steps(): array
    {
        return [
            [
                'icon' => 'pencil-square',
                'number' => '01',
                'title' => 'Signalez',
                'description' => 'Remplissez le formulaire, choisissez la catégorie et décrivez la situation.',
            ],
            [
                'icon' => 'robot',
                'number' => '02',
                'title' => 'Analyse IA',
                'description' => 'Notre IA analyse votre signalement et détermine le niveau de priorité.',
            ],
            [
                'icon' => 'send-fill',
                'number' => '03',
                'title' => 'Alerte envoyée',
                'description' => 'Les services compétents sont notifiés immédiatement avec votre localisation.',
            ],
            [
                'icon' => 'check-circle-fill',
                'number' => '04',
                'title' => 'Suivi & résolution',
                'description' => 'Suivez l\'intervention en direct jusqu\'à la clôture de l\'urgence.',
            ],
        ];
    }

    public function home()
    {
        return view('pages.home', [
            'layout' => 'guest',
            'features' => $this->features(),
            'steps' => $this->steps(),
        ]);
    }

    public function login()
    {
        return view('pages.login', ['layout' => 'auth']);
    }

    public function register()
    {
        return view('pages.register', ['layout' => 'auth']);
    }

    public function dashboard()
    {
        return view('pages.dashboard', [
            'layout' => 'app',
            'user' => $this->user(),
            'stats' => $this->dashboardStats(),
            'recent' => array_slice($this->signalements(), 0, 3),
        ]);
    }

    public function report()
    {
        return view('pages.report', [
            'layout' => 'app',
            'user' => $this->user(),
            'categories' => $this->categories(),
        ]);
    }

    public function history()
    {
        return view('pages.history', [
            'layout' => 'app',
            'user' => $this->user(),
            'signalements' => $this->signalements(),
        ]);
    }

    public function show(string $id)
    {
        $signalements = $this->signalements();
        $signalement = collect($signalements)->firstWhere('id', $id);

        if (! $signalement) {
            abort(404);
        }

        return view('pages.show', [
            'layout' => 'app',
            'user' => $this->user(),
            'signalement' => $signalement,
        ]);
    }
}
