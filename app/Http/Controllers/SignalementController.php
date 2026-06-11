<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Signalement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SignalementController extends Controller
{
    public function resolveAddress(Request $request)
    {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        return response()->json([
            'address' => $this->reverseGeocode(
                (float) $validated['latitude'],
                (float) $validated['longitude']
            ),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'category' => ['required', 'string', 'exists:categories,name'],
            'description' => ['required', 'string', 'min:10'],
            'localisation' => ['nullable', 'string', 'max:500'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'video' => ['nullable', 'mimes:mp4,mov,avi,webm', 'max:20480'],
        ], [
            'category.required' => 'Veuillez choisir une catégorie.',
            'description.min' => 'La description doit contenir au moins 10 caractères.',
            'latitude.required' => 'La géolocalisation est obligatoire. Activez le GPS et cliquez sur « Obtenir ma position ».',
            'longitude.required' => 'La géolocalisation est obligatoire.',
            'photo.max' => 'La photo ne doit pas dépasser 5 Mo.',
            'video.max' => 'La vidéo ne doit pas dépasser 20 Mo.',
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
        ]);

        $localisation = $validated['localisation']
            ?? $this->reverseGeocode($validated['latitude'], $validated['longitude']);

        $category = Category::where('name', $validated['category'])->firstOrFail();
        $gravite = $this->determineGravite($category->name);
        $now = Carbon::now();

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = Storage::url($request->file('photo')->store('signalements/photos', 'public'));
        }

        $videoPath = null;
        if ($request->hasFile('video')) {
            $videoPath = Storage::url($request->file('video')->store('signalements/videos', 'public'));
        }

        $signalement = Signalement::create([
            'reference' => $this->generateReference(),
            'user_id' => $user->id,
            'category_id' => $category->id,
            'description' => $validated['description'],
            'localisation' => $localisation,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'gravite' => $gravite,
            'statut' => 'en_cours',
            'photo' => $photoPath,
            'video' => $videoPath,
            'reported_at' => $now,
        ]);

        $signalement->timelineSteps()->createMany([
            [
                'label' => 'Signalement reçu',
                'done' => true,
                'occurred_at' => $now,
                'sort_order' => 1,
            ],
            [
                'label' => 'Analyse IA terminée',
                'done' => true,
                'occurred_at' => $now->copy()->addMinute(),
                'sort_order' => 2,
            ],
            [
                'label' => 'Intervention en cours',
                'done' => true,
                'occurred_at' => $now->copy()->addMinutes(2),
                'sort_order' => 3,
            ],
            [
                'label' => 'Intervention clôturée',
                'done' => false,
                'occurred_at' => null,
                'sort_order' => 4,
            ],
        ]);

        return redirect()
            ->route('signalement.show', $signalement->reference)
            ->with('success', 'Votre urgence a été signalée avec succès.');
    }

    private function generateReference(): string
    {
        $next = (Signalement::max('id') ?? 0) + 1;

        return 'SIG-'.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }

    private function determineGravite(string $category): string
    {
        return match ($category) {
            'Incendie', 'Urgence médicale' => 'critique',
            'Accident', 'Agression', 'Inondation' => 'elevee',
            'Coupure électrique' => 'moyenne',
            default => 'moyenne',
        };
    }

    private function reverseGeocode(float $lat, float $lng): string
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders(['User-Agent' => 'SmartEmergencyAI-Niger/1.0'])
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'lat' => $lat,
                    'lon' => $lng,
                    'format' => 'json',
                    'accept-language' => 'fr',
                ]);

            if ($response->successful() && $response->json('display_name')) {
                return $response->json('display_name');
            }
        } catch (\Throwable) {
            // Adresse texte de secours
        }

        return sprintf('Position GPS (%.5f, %.5f) — Niger', $lat, $lng);
    }
}
