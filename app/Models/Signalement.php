<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Signalement extends Model
{
    protected $fillable = [
        'reference',
        'user_id',
        'category_id',
        'description',
        'localisation',
        'latitude',
        'longitude',
        'gravite',
        'ai_score',
        'ai_credibility_score',
        'ai_verdict',
        'ai_rejection_reasons',
        'ai_priority_rank',
        'ai_summary',
        'ai_services',
        'estimated_response_min',
        'fire_people_trapped',
        'fire_smoke_level',
        'fire_building_type',
        'assigned_service_id',
        'statut',
        'photo',
        'video',
        'reported_at',
    ];

    protected function casts(): array
    {
        return [
            'reported_at' => 'datetime',
            'latitude' => 'float',
            'longitude' => 'float',
            'ai_services' => 'array',
            'ai_rejection_reasons' => 'array',
            'fire_people_trapped' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function assignedService(): BelongsTo
    {
        return $this->belongsTo(EmergencyService::class, 'assigned_service_id');
    }

    public function timelineSteps(): HasMany
    {
        return $this->hasMany(SignalementTimelineStep::class)->orderBy('sort_order');
    }

    public function priorityLabel(): string
    {
        $rank = $this->ai_priority_rank ?? $this->ai_score ?? 0;

        return match (true) {
            $rank >= 75 => 'P1 — Critique',
            $rank >= 55 => 'P2 — Urgent',
            $rank >= 35 => 'P3 — Modéré',
            default => 'P4 — Faible',
        };
    }

    public function verdictLabel(): string
    {
        return match ($this->ai_verdict) {
            'review' => 'Vérification requise',
            'rejected' => 'Rejeté par l\'IA',
            default => 'Validé par l\'IA',
        };
    }

    /** Format attendu par les vues Blade */
    public function toViewArray(): array
    {
        return [
            'id' => $this->reference,
            'categorie' => $this->category->name,
            'description' => $this->description,
            'localisation' => $this->localisation,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'maps_url' => $this->latitude && $this->longitude
                ? "https://www.google.com/maps?q={$this->latitude},{$this->longitude}"
                : null,
            'whatsapp_url' => $this->latitude && $this->longitude
                ? 'https://wa.me/?text='.urlencode("🚨 URGENCE {$this->reference} — {$this->category->name}\n📍 {$this->localisation}\nhttps://maps.google.com/?q={$this->latitude},{$this->longitude}")
                : null,
            'date' => $this->reported_at->format('d/m/Y'),
            'heure' => $this->reported_at->format('H:i'),
            'gravite' => $this->gravite,
            'statut' => $this->statut,
            'photo' => $this->photo,
            'video' => $this->video,
            'ai_score' => $this->ai_score,
            'ai_credibility_score' => $this->ai_credibility_score,
            'ai_verdict' => $this->ai_verdict,
            'ai_rejection_reasons' => $this->ai_rejection_reasons ?? [],
            'ai_priority_rank' => $this->ai_priority_rank,
            'ai_summary' => $this->ai_summary,
            'ai_services' => $this->ai_services ?? [],
            'estimated_response_min' => $this->estimated_response_min,
            'fire_people_trapped' => $this->fire_people_trapped,
            'fire_smoke_level' => $this->fire_smoke_level,
            'fire_building_type' => $this->fire_building_type,
            'priority_label' => ($this->ai_priority_rank || $this->ai_score) ? $this->priorityLabel() : null,
            'verdict_label' => $this->ai_verdict ? $this->verdictLabel() : null,
            'assigned_service' => $this->assignedService?->name,
            'timeline' => $this->timelineSteps->map(fn ($step) => [
                'label' => $step->label,
                'done' => $step->done,
                'time' => $step->occurred_at?->format('d/m/Y H:i'),
            ])->all(),
        ];
    }
}
