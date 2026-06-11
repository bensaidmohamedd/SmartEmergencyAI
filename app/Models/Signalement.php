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

    public function timelineSteps(): HasMany
    {
        return $this->hasMany(SignalementTimelineStep::class)->orderBy('sort_order');
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
            'date' => $this->reported_at->format('d/m/Y'),
            'heure' => $this->reported_at->format('H:i'),
            'gravite' => $this->gravite,
            'statut' => $this->statut,
            'photo' => $this->photo,
            'video' => $this->video,
            'timeline' => $this->timelineSteps->map(fn ($step) => [
                'label' => $step->label,
                'done' => $step->done,
                'time' => $step->occurred_at?->format('d/m/Y H:i'),
            ])->all(),
        ];
    }
}
