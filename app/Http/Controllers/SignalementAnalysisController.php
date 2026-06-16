<?php

namespace App\Http\Controllers;

use App\Services\EmergencyAnalysisService;
use Illuminate\Http\Request;

class SignalementAnalysisController extends Controller
{
    public function preview(Request $request, EmergencyAnalysisService $analyzer)
    {
        $validated = $request->validate([
            'category' => ['required', 'string'],
            'description' => ['required', 'string', 'min:5'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'has_media' => ['nullable', 'boolean'],
            'fire_people_trapped' => ['nullable', 'boolean'],
            'fire_smoke_level' => ['nullable', 'in:faible,modere,dense'],
            'fire_building_type' => ['nullable', 'string'],
        ]);

        $analysis = $analyzer->analyze(
            $validated['category'],
            $validated['description'],
            isset($validated['fire_people_trapped']) ? (bool) $validated['fire_people_trapped'] : null,
            $validated['fire_smoke_level'] ?? null,
            $validated['fire_building_type'] ?? null,
            $request->filled('latitude') ? (float) $request->latitude : null,
            $request->filled('longitude') ? (float) $request->longitude : null,
            (bool) ($validated['has_media'] ?? false),
            $request->user(),
        );

        return response()->json($analysis);
    }
}
