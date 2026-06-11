<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignalementTimelineStep extends Model
{
    protected $fillable = [
        'signalement_id',
        'label',
        'done',
        'occurred_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'done' => 'boolean',
            'occurred_at' => 'datetime',
        ];
    }

    public function signalement(): BelongsTo
    {
        return $this->belongsTo(Signalement::class);
    }
}
