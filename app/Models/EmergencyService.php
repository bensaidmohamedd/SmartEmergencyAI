<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmergencyService extends Model
{
    protected $fillable = [
        'name', 'type', 'phone', 'address', 'latitude', 'longitude', 'zone',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function signalements(): HasMany
    {
        return $this->hasMany(Signalement::class, 'assigned_service_id');
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'pompiers' => 'Pompiers',
            'police' => 'Police',
            'samu' => 'SAMU / Ambulance',
            'gendarmerie' => 'Gendarmerie',
            default => ucfirst($this->type),
        };
    }

    public function typeIcon(): string
    {
        return match ($this->type) {
            'pompiers' => 'fire',
            'police' => 'shield-shaded',
            'samu' => 'heart-pulse-fill',
            'gendarmerie' => 'shield-fill',
            default => 'building',
        };
    }
}
