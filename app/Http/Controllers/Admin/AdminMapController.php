<?php

namespace App\Http\Controllers\Admin;

use App\Models\Signalement;

class AdminMapController extends AdminController
{
    public function index()
    {
        $signalements = Signalement::with(['category', 'user'])
            ->where('statut', 'en_cours')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderByDesc('reported_at')
            ->get();

        $markers = $signalements->map(fn (Signalement $s) => [
            'lat' => $s->latitude,
            'lng' => $s->longitude,
            'ref' => $s->reference,
            'cat' => $s->category->name,
            'url' => route('admin.signalements.show', $s->reference),
        ])->values();

        return view('admin.map.index', [
            'layout' => 'admin',
            'user' => $this->adminUser()->toViewArray(),
            'signalements' => $signalements,
            'markers' => $markers,
            'mapCenter' => ['lat' => 13.5127, 'lng' => 2.1128],
        ]);
    }
}
