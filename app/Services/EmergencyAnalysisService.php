<?php

namespace App\Services;

use App\Models\Signalement;
use App\Models\User;

class EmergencyAnalysisService
{
    public const VERDICT_APPROVED = 'approved';

    public const VERDICT_REVIEW = 'review';

    public const VERDICT_REJECTED = 'rejected';

    /** @var array<string, array{weight: int, label: string}> */
    private const URGENCY_KEYWORDS = [
        'feu' => ['weight' => 15, 'label' => 'feu détecté'],
        'incendie' => ['weight' => 20, 'label' => 'incendie confirmé'],
        'fumee' => ['weight' => 12, 'label' => 'fumée signalée'],
        'fumée' => ['weight' => 12, 'label' => 'fumée signalée'],
        'flamme' => ['weight' => 15, 'label' => 'flames visibles'],
        'explosion' => ['weight' => 25, 'label' => 'explosion'],
        'gaz' => ['weight' => 18, 'label' => 'risque gaz'],
        'bloque' => ['weight' => 22, 'label' => 'personnes bloquées'],
        'coince' => ['weight' => 22, 'label' => 'personnes coincées'],
        'enfant' => ['weight' => 18, 'label' => 'enfants impliqués'],
        'etage' => ['weight' => 10, 'label' => 'étage élevé'],
        'immeuble' => ['weight' => 12, 'label' => 'immeuble'],
        'foret' => ['weight' => 14, 'label' => 'feu de brousse/forêt'],
        'brousse' => ['weight' => 14, 'label' => 'feu de brousse'],
        'vehicule' => ['weight' => 16, 'label' => 'véhicule en feu'],
        'electricite' => ['weight' => 10, 'label' => 'risque électrique'],
        'electrique' => ['weight' => 10, 'label' => 'risque électrique'],
        'accident' => ['weight' => 14, 'label' => 'accident'],
        'collision' => ['weight' => 14, 'label' => 'collision'],
        'blesse' => ['weight' => 18, 'label' => 'blessé'],
        'agression' => ['weight' => 16, 'label' => 'agression'],
        'inondation' => ['weight' => 12, 'label' => 'inondation'],
        'urgence' => ['weight' => 8, 'label' => 'urgence'],
        'sang' => ['weight' => 16, 'label' => 'saignement'],
        'inconscient' => ['weight' => 20, 'label' => 'personne inconsciente'],
    ];

    /** Mots-clés obligatoires par catégorie (au moins un requis pour crédibilité) */
    private const CATEGORY_KEYWORDS = [
        'Incendie' => ['feu', 'incendie', 'fumee', 'fumée', 'flamme', 'brule', 'brûle', 'explosion', 'gaz', 'chaud', 'carbonise'],
        'Accident' => ['accident', 'collision', 'voiture', 'vehicule', 'véhicule', 'moto', 'route', 'choc', 'renverse', 'bless'],
        'Agression' => ['agression', 'agress', 'violence', 'coups', 'couteau', 'arme', 'vol', 'attaque', 'menace'],
        'Inondation' => ['inond', 'eau', 'crue', 'submers', 'noie', 'pluie', 'canalisation'],
        'Coupure électrique' => ['electric', 'électri', 'courant', 'panne', 'blackout', 'cable', 'câble', 'transformateur'],
        'Urgence médicale' => ['medic', 'médic', 'malade', 'inconscient', 'sang', 'douleur', 'crise', 'respir', 'coeur', 'accouche'],
    ];

    /** Indicateurs de fausse déclaration */
    private const FAKE_INDICATORS = [
        'test' => 'mot « test » détecté',
        'blague' => 'mot « blague » détecté',
        'fake' => 'mot « fake » détecté',
        'faux' => 'mot « faux » détecté',
        'lol' => 'contenu non sérieux',
        'haha' => 'contenu non sérieux',
        'essai' => 'signalement d\'essai',
        'simulation' => 'simulation déclarée',
        'pipo' => 'terme suspect « pipo »',
        'arnaque' => 'terme suspect',
        'mdr' => 'contenu non sérieux',
        'ptdr' => 'contenu non sérieux',
        'je teste' => 'test explicite',
        'cest un test' => 'test explicite',
        "c'est un test" => 'test explicite',
        'pas grave' => 'minimisation incompatible avec urgence',
        'rien du tout' => 'description incohérente',
        'ya rien' => 'description incohérente',
        'y a rien' => 'description incohérente',
    ];

    private const CATEGORY_BASE = [
        'Incendie' => 55,
        'Urgence médicale' => 50,
        'Accident' => 45,
        'Agression' => 42,
        'Inondation' => 35,
        'Coupure électrique' => 20,
    ];

    private const CATEGORY_SERVICES = [
        'Incendie' => ['pompiers', 'police'],
        'Urgence médicale' => ['samu', 'police'],
        'Accident' => ['samu', 'police', 'pompiers'],
        'Agression' => ['police', 'gendarmerie', 'samu'],
        'Inondation' => ['pompiers', 'police'],
        'Coupure électrique' => ['pompiers'],
    ];

    /** Bornes approximatives Niger */
    private const NIGER_BOUNDS = ['lat_min' => 11.5, 'lat_max' => 24.0, 'lng_min' => 0.2, 'lng_max' => 16.0];

    public function analyze(
        string $category,
        string $description,
        ?bool $peopleTrapped = null,
        ?string $smokeLevel = null,
        ?string $buildingType = null,
        ?float $latitude = null,
        ?float $longitude = null,
        bool $hasMedia = false,
        ?User $user = null,
    ): array {
        $normalized = $this->normalize($description);
        $credibility = $this->assessCredibility(
            $category, $description, $normalized, $latitude, $longitude, $hasMedia, $user
        );

        $score = self::CATEGORY_BASE[$category] ?? 30;
        $signals = [];

        foreach (self::URGENCY_KEYWORDS as $keyword => $meta) {
            if (str_contains($normalized, $this->normalize($keyword))) {
                $score += $meta['weight'];
                $signals[] = $meta['label'];
            }
        }

        if ($peopleTrapped === true) {
            $score += 25;
            $signals[] = 'personnes piégées (déclaré)';
        }

        if ($smokeLevel === 'dense') {
            $score += 20;
            $signals[] = 'fumée dense';
        } elseif ($smokeLevel === 'modere') {
            $score += 10;
            $signals[] = 'fumée modérée';
        }

        if (in_array($buildingType, ['ecole', 'hopital'], true)) {
            $score += 15;
            $signals[] = 'bâtiment sensible : '.$buildingType;
        } elseif ($buildingType === 'habitation') {
            $score += 5;
        }

        $score = min(100, max(5, $score));

        $verdict = $this->determineVerdict($credibility);
        $gravite = match (true) {
            $score >= 80 => 'critique',
            $score >= 60 => 'elevee',
            $score >= 40 => 'moyenne',
            default => 'faible',
        };

        // Faible crédibilité → réduire la priorité même si mots-clés présents
        if ($credibility['score'] < 50) {
            $score = (int) round($score * 0.6);
            $gravite = match (true) {
                $score >= 70 => 'elevee',
                $score >= 45 => 'moyenne',
                default => 'faible',
            };
        }

        $priorityRank = (int) round($score * ($credibility['score'] / 100));

        $services = self::CATEGORY_SERVICES[$category] ?? ['police'];
        if ($peopleTrapped || $score >= 70) {
            if (! in_array('samu', $services)) {
                $services[] = 'samu';
            }
        }

        $serviceLabels = array_map(fn ($t) => match ($t) {
            'pompiers' => 'Sapeurs-pompiers (18)',
            'police' => 'Police nationale (17)',
            'samu' => 'SAMU / Ambulance (15)',
            'gendarmerie' => 'Gendarmerie (17)',
            default => $t,
        }, array_unique($services));

        $eta = match ($gravite) {
            'critique' => rand(4, 7),
            'elevee' => rand(7, 12),
            'moyenne' => rand(12, 20),
            default => rand(20, 35),
        };

        $priorityLabel = match (true) {
            $priorityRank >= 75 => 'P1 — Intervention immédiate',
            $priorityRank >= 55 => 'P2 — Urgent',
            $priorityRank >= 35 => 'P3 — Modéré',
            default => 'P4 — Faible',
        };

        $summary = $this->buildSummary($category, $score, $gravite, $signals, $serviceLabels, $eta, $credibility, $verdict);

        return [
            'score' => $score,
            'credibility_score' => $credibility['score'],
            'credibility_flags' => $credibility['positive'],
            'rejection_reasons' => $credibility['negative'],
            'verdict' => $verdict,
            'verdict_label' => $this->verdictLabel($verdict),
            'can_submit' => $verdict !== self::VERDICT_REJECTED,
            'gravite' => $gravite,
            'priority_rank' => $priorityRank,
            'priority_label' => $priorityLabel,
            'summary' => $summary,
            'signals' => array_unique($signals),
            'services' => $serviceLabels,
            'service_types' => array_values(array_unique($services)),
            'estimated_response_min' => $eta,
        ];
    }

    private function assessCredibility(
        string $category,
        string $description,
        string $normalized,
        ?float $latitude,
        ?float $longitude,
        bool $hasMedia,
        ?User $user,
    ): array {
        $score = 70;
        $positive = [];
        $negative = [];

        // --- Détection fausse déclaration ---
        foreach (self::FAKE_INDICATORS as $pattern => $reason) {
            if (str_contains($normalized, $this->normalize($pattern))) {
                $score -= 35;
                $negative[] = $reason;
            }
        }

        if (mb_strlen(trim($description)) < 15) {
            $score -= 25;
            $negative[] = 'description trop courte';
        }

        if (preg_match('/(.)\1{5,}/u', $normalized)) {
            $score -= 20;
            $negative[] = 'texte répétitif suspect';
        }

        if (preg_match('/^[a-z\s!?.,]{0,20}$/u', $normalized) && mb_strlen($description) < 20) {
            $score -= 15;
            $negative[] = 'description vague';
        }

        // Cohérence catégorie / description
        $categoryKeywords = self::CATEGORY_KEYWORDS[$category] ?? [];
        $categoryMatch = false;
        foreach ($categoryKeywords as $kw) {
            if (str_contains($normalized, $this->normalize($kw))) {
                $categoryMatch = true;
                break;
            }
        }

        if (! $categoryMatch) {
            $score -= 30;
            $negative[] = "description incohérente avec la catégorie « {$category} »";
        } else {
            $score += 10;
            $positive[] = 'cohérence catégorie confirmée';
        }

        // GPS Niger
        if ($latitude !== null && $longitude !== null) {
            $inNiger = $latitude >= self::NIGER_BOUNDS['lat_min']
                && $latitude <= self::NIGER_BOUNDS['lat_max']
                && $longitude >= self::NIGER_BOUNDS['lng_min']
                && $longitude <= self::NIGER_BOUNDS['lng_max'];
            if ($inNiger) {
                $score += 10;
                $positive[] = 'GPS localisé au Niger';
            } else {
                $score -= 40;
                $negative[] = 'position GPS hors du Niger';
            }
        } else {
            $score -= 15;
            $negative[] = 'GPS non fourni';
        }

        if ($hasMedia) {
            $score += 15;
            $positive[] = 'preuve photo/vidéo jointe';
        }

        if (mb_strlen(trim($description)) >= 40) {
            $score += 8;
            $positive[] = 'description détaillée';
        }

        // Doublon récent
        if ($user) {
            $recentDuplicate = Signalement::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subHour())
                ->where('description', $description)
                ->exists();
            if ($recentDuplicate) {
                $score -= 50;
                $negative[] = 'signalement identique déjà soumis récemment';
            }

            $recentCount = Signalement::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subMinutes(30))
                ->count();
            if ($recentCount >= 3) {
                $score -= 30;
                $negative[] = 'trop de signalements en peu de temps (suspicion spam)';
            }
        }

        $score = min(100, max(0, $score));

        return [
            'score' => $score,
            'positive' => $positive,
            'negative' => array_unique($negative),
        ];
    }

    private function determineVerdict(array $credibility): string
    {
        $score = $credibility['score'];
        $negative = $credibility['negative'];

        // Rejet automatique
        $criticalReasons = ['test explicite', 'simulation déclarée', 'signalement identique déjà soumis récemment',
            'position GPS hors du Niger', 'trop de signalements en peu de temps (suspicion spam)'];

        foreach ($negative as $reason) {
            if (in_array($reason, $criticalReasons, true)) {
                return self::VERDICT_REJECTED;
            }
        }

        if ($score < 35) {
            return self::VERDICT_REJECTED;
        }

        if ($score < 55 || count($negative) >= 2) {
            return self::VERDICT_REVIEW;
        }

        return self::VERDICT_APPROVED;
    }

    private function verdictLabel(string $verdict): string
    {
        return match ($verdict) {
            self::VERDICT_APPROVED => 'Signalement validé par l\'IA',
            self::VERDICT_REVIEW => 'Signalement suspect — vérification requise',
            self::VERDICT_REJECTED => 'Signalement rejeté — fausse urgence détectée',
        };
    }

    private function buildSummary(
        string $category,
        int $score,
        string $gravite,
        array $signals,
        array $services,
        int $eta,
        array $credibility,
        string $verdict,
    ): string {
        $graviteLabel = match ($gravite) {
            'critique' => 'CRITIQUE',
            'elevee' => 'ÉLEVÉE',
            'moyenne' => 'MOYENNE',
            default => 'FAIBLE',
        };

        $parts = [
            "Analyse IA — {$category}.",
            "Crédibilité : {$credibility['score']}/100.",
            "Priorité : {$score}/100 ({$graviteLabel}).",
            $this->verdictLabel($verdict),
        ];

        if ($credibility['negative']) {
            $parts[] = 'Alertes : '.implode(', ', array_slice($credibility['negative'], 0, 3)).'.';
        }

        if ($signals) {
            $parts[] = 'Indicateurs : '.implode(', ', array_slice($signals, 0, 4)).'.';
        }

        if ($verdict !== self::VERDICT_REJECTED) {
            $parts[] = 'Services : '.implode(', ', $services).'.';
            $parts[] = "ETA ~{$eta} min.";
        }

        return implode(' ', $parts);
    }

    private function normalize(string $text): string
    {
        $text = mb_strtolower($text);
        $text = str_replace(['é', 'è', 'ê', 'ë'], 'e', $text);
        $text = str_replace(['à', 'â'], 'a', $text);
        $text = str_replace(['ù', 'û'], 'u', $text);
        $text = str_replace(['î', 'ï'], 'i', $text);
        $text = str_replace('ô', 'o', $text);
        $text = str_replace('ç', 'c', $text);

        return $text;
    }
}
