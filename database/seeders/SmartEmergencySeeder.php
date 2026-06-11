<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\PlatformStat;
use App\Models\Signalement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SmartEmergencySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'ben.said@email.ne'],
            [
                'name' => 'Ben Saïd',
                'phone' => '+227 87 14 51 44',
                'password' => Hash::make('password'),
            ]
        );

        foreach ([
            'Incendie', 'Accident', 'Agression', 'Inondation',
            'Coupure électrique', 'Urgence médicale',
        ] as $name) {
            Category::firstOrCreate(['name' => $name]);
        }

        $categoryMap = Category::pluck('id', 'name');

        $signalementsData = [
            [
                'reference' => 'SIG-001',
                'category' => 'Incendie',
                'description' => 'Fumée dense au 3e étage du bâtiment B. Plusieurs personnes bloquées dans les escaliers.',
                'localisation' => 'Avenue de la République, Plateau — Niamey',
                'latitude' => 13.5127,
                'longitude' => 2.1128,
                'reported_at' => '2026-06-10 14:32:00',
                'gravite' => 'critique',
                'statut' => 'en_cours',
                'photo' => 'https://images.unsplash.com/photo-1547056979-94710663f979?w=600&h=400&fit=crop',
                'timeline' => [
                    ['label' => 'Signalement reçu', 'done' => true, 'time' => '2026-06-10 14:32:00'],
                    ['label' => 'Analyse IA terminée', 'done' => true, 'time' => '2026-06-10 14:33:00'],
                    ['label' => 'Intervention en cours', 'done' => true, 'time' => '2026-06-10 14:40:00'],
                    ['label' => 'Intervention clôturée', 'done' => false, 'time' => null],
                ],
            ],
            [
                'reference' => 'SIG-002',
                'category' => 'Accident',
                'description' => 'Collision entre deux véhicules à l\'intersection. Blessés légers signalés.',
                'localisation' => 'Boulevard Mali Bero, Niamey',
                'reported_at' => '2026-06-09 09:15:00',
                'gravite' => 'elevee',
                'statut' => 'en_cours',
                'photo' => 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=600&h=400&fit=crop',
                'timeline' => [
                    ['label' => 'Signalement reçu', 'done' => true, 'time' => '2026-06-09 09:15:00'],
                    ['label' => 'Analyse IA terminée', 'done' => true, 'time' => '2026-06-09 09:16:00'],
                    ['label' => 'Intervention en cours', 'done' => true, 'time' => '2026-06-09 09:25:00'],
                    ['label' => 'Intervention clôturée', 'done' => false, 'time' => null],
                ],
            ],
            [
                'reference' => 'SIG-003',
                'category' => 'Urgence médicale',
                'description' => 'Personne âgée inconsciente dans le parc. Pulsations faibles détectées.',
                'localisation' => 'Parc Wadata, Niamey',
                'reported_at' => '2026-06-08 18:45:00',
                'gravite' => 'critique',
                'statut' => 'termine',
                'photo' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=600&h=400&fit=crop',
                'timeline' => [
                    ['label' => 'Signalement reçu', 'done' => true, 'time' => '2026-06-08 18:45:00'],
                    ['label' => 'Analyse IA terminée', 'done' => true, 'time' => '2026-06-08 18:46:00'],
                    ['label' => 'Intervention en cours', 'done' => true, 'time' => '2026-06-08 18:50:00'],
                    ['label' => 'Intervention clôturée', 'done' => true, 'time' => '2026-06-08 19:30:00'],
                ],
            ],
            [
                'reference' => 'SIG-004',
                'category' => 'Inondation',
                'description' => 'Sous-sol inondé suite à une rupture de canalisation. Risque pour les installations électriques.',
                'localisation' => 'Quartier Lazaret, Niamey',
                'reported_at' => '2026-06-07 11:20:00',
                'gravite' => 'moyenne',
                'statut' => 'termine',
                'photo' => 'https://images.unsplash.com/photo-1547036967-23d11aacaee0?w=600&h=400&fit=crop',
                'timeline' => [
                    ['label' => 'Signalement reçu', 'done' => true, 'time' => '2026-06-07 11:20:00'],
                    ['label' => 'Analyse IA terminée', 'done' => true, 'time' => '2026-06-07 11:21:00'],
                    ['label' => 'Intervention en cours', 'done' => true, 'time' => '2026-06-07 11:35:00'],
                    ['label' => 'Intervention clôturée', 'done' => true, 'time' => '2026-06-07 14:00:00'],
                ],
            ],
            [
                'reference' => 'SIG-005',
                'category' => 'Agression',
                'description' => 'Altercation violente devant la gare. Témoins sur place.',
                'localisation' => 'Gare routière, Niamey',
                'reported_at' => '2026-06-06 22:08:00',
                'gravite' => 'elevee',
                'statut' => 'termine',
                'photo' => 'https://images.unsplash.com/photo-1521791136064-7986c2920216?w=600&h=400&fit=crop',
                'timeline' => [
                    ['label' => 'Signalement reçu', 'done' => true, 'time' => '2026-06-06 22:08:00'],
                    ['label' => 'Analyse IA terminée', 'done' => true, 'time' => '2026-06-06 22:09:00'],
                    ['label' => 'Intervention en cours', 'done' => true, 'time' => '2026-06-06 22:15:00'],
                    ['label' => 'Intervention clôturée', 'done' => true, 'time' => '2026-06-06 23:00:00'],
                ],
            ],
            [
                'reference' => 'SIG-006',
                'category' => 'Coupure électrique',
                'description' => 'Panne générale dans le quartier. Ascenseurs bloqués.',
                'localisation' => 'Grand Marché, Niamey',
                'reported_at' => '2026-06-05 16:50:00',
                'gravite' => 'faible',
                'statut' => 'termine',
                'photo' => 'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?w=600&h=400&fit=crop',
                'timeline' => [
                    ['label' => 'Signalement reçu', 'done' => true, 'time' => '2026-06-05 16:50:00'],
                    ['label' => 'Analyse IA terminée', 'done' => true, 'time' => '2026-06-05 16:51:00'],
                    ['label' => 'Intervention en cours', 'done' => true, 'time' => '2026-06-05 17:00:00'],
                    ['label' => 'Intervention clôturée', 'done' => true, 'time' => '2026-06-05 18:30:00'],
                ],
            ],
        ];

        foreach ($signalementsData as $index => $data) {
            $timeline = $data['timeline'];
            $categoryName = $data['category'];
            unset($data['timeline'], $data['category']);

            $signalement = Signalement::updateOrCreate(
                ['reference' => $data['reference']],
                [
                    'user_id' => $user->id,
                    'category_id' => $categoryMap[$categoryName],
                    'description' => $data['description'],
                    'localisation' => $data['localisation'],
                    'latitude' => $data['latitude'] ?? null,
                    'longitude' => $data['longitude'] ?? null,
                    'gravite' => $data['gravite'],
                    'statut' => $data['statut'],
                    'photo' => $data['photo'],
                    'reported_at' => Carbon::parse($data['reported_at']),
                ]
            );

            $signalement->timelineSteps()->delete();

            foreach ($timeline as $order => $step) {
                $signalement->timelineSteps()->create([
                    'label' => $step['label'],
                    'done' => $step['done'],
                    'occurred_at' => $step['time'] ? Carbon::parse($step['time']) : null,
                    'sort_order' => $order + 1,
                ]);
            }
        }

        $platformStats = [
            ['key' => 'urgences_traitees', 'value' => '120', 'label' => 'Urgences traitées'],
            ['key' => 'taux_succes', 'value' => '95%', 'label' => 'Taux de succès'],
            ['key' => 'temps_moyen', 'value' => '12 min', 'label' => 'Temps moyen d\'intervention'],
        ];

        foreach ($platformStats as $stat) {
            PlatformStat::updateOrCreate(['key' => $stat['key']], $stat);
        }
    }
}
