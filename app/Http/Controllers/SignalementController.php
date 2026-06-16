<?php

namespace App\Http\Controllers;

use App\Jobs\NotifySignalementUpdateJob;
use App\Models\Category;
use App\Models\Signalement;
use App\Models\User;
use App\Notifications\NewSignalementAdminNotification;
use App\Services\AuditLogger;
use App\Services\EmergencyAnalysisService;
use App\Services\NearestServiceFinder;
use App\Services\PlatformStatsCalculator;
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

    public function store(Request $request, EmergencyAnalysisService $analyzer)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'category' => ['required', 'string', 'exists:categories,name'],
            'description' => ['required', 'string', 'min:10'],
            'localisation' => ['nullable', 'string', 'max:500'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'fire_people_trapped' => ['nullable', 'boolean'],
            'fire_smoke_level' => ['nullable', 'in:faible,modere,dense'],
            'fire_building_type' => ['nullable', 'in:habitation,commerce,ecole,hopital,industrie,autre'],
            'photo' => ['nullable', 'file', 'mimes:jpeg,jpg,png,gif,webp,bmp,heic,heif', 'max:5120'],
            'video' => ['nullable', 'file', 'mimes:mp4,mov,avi,webm,3gp,mkv,m4v', 'max:20480'],
        ], [
            'category.required' => 'Veuillez choisir une catégorie.',
            'description.min' => 'La description doit contenir au moins 10 caractères.',
            'latitude.required' => 'La géolocalisation est obligatoire. Activez le GPS et cliquez sur « Obtenir ma position ».',
            'longitude.required' => 'La géolocalisation est obligatoire.',
            'photo.max' => 'La photo ne doit pas dépasser 5 Mo.',
            'video.max' => 'La vidéo ne doit pas dépasser 20 Mo.',
        ]);

        $peopleTrapped = $request->boolean('fire_people_trapped');
        $hasMedia = $request->hasFile('photo') || $request->hasFile('video');

        $analysis = $analyzer->analyze(
            $validated['category'],
            $validated['description'],
            $validated['category'] === 'Incendie' ? $peopleTrapped : null,
            $validated['fire_smoke_level'] ?? null,
            $validated['fire_building_type'] ?? null,
            (float) $validated['latitude'],
            (float) $validated['longitude'],
            $hasMedia,
            Auth::user(),
        );

        if ($analysis['verdict'] === EmergencyAnalysisService::VERDICT_REJECTED) {
            AuditLogger::log('signalement.rejected_by_ai', null, [
                'category' => $validated['category'],
                'reasons' => $analysis['rejection_reasons'],
                'credibility' => $analysis['credibility_score'],
                'user_id' => Auth::id(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'description' => 'Signalement rejeté par l\'IA : '.implode(' · ', $analysis['rejection_reasons']),
                ])
                ->with('ai_rejected', $analysis);
        }

        $user = Auth::user();
        $user->update(['name' => $validated['name'], 'phone' => $validated['phone']]);

        $localisation = $validated['localisation']
            ?? $this->reverseGeocode($validated['latitude'], $validated['longitude']);

        $category = Category::where('name', $validated['category'])->firstOrFail();
        $now = Carbon::now();

        $photoPath = $request->hasFile('photo')
            ? Storage::url($request->file('photo')->store('signalements/photos', 'public'))
            : null;
        $videoPath = $request->hasFile('video')
            ? Storage::url($request->file('video')->store('signalements/videos', 'public'))
            : null;

        $primaryType = $analysis['service_types'][0] ?? 'pompiers';
        $nearest = NearestServiceFinder::find($validated['latitude'], $validated['longitude'], $primaryType, 1);
        $assignedServiceId = $nearest[0]['service']->id ?? null;

        $signalement = Signalement::create([
            'reference' => $this->generateReference(),
            'user_id' => $user->id,
            'category_id' => $category->id,
            'description' => $validated['description'],
            'localisation' => $localisation,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'gravite' => $analysis['gravite'],
            'ai_score' => $analysis['score'],
            'ai_credibility_score' => $analysis['credibility_score'],
            'ai_verdict' => $analysis['verdict'],
            'ai_rejection_reasons' => $analysis['rejection_reasons'] ?: null,
            'ai_priority_rank' => $analysis['priority_rank'],
            'ai_summary' => $analysis['summary'],
            'ai_services' => $analysis['services'],
            'estimated_response_min' => $analysis['estimated_response_min'],
            'fire_people_trapped' => $validated['category'] === 'Incendie' ? $peopleTrapped : null,
            'fire_smoke_level' => $validated['fire_smoke_level'] ?? null,
            'fire_building_type' => $validated['fire_building_type'] ?? null,
            'assigned_service_id' => $assignedServiceId,
            'statut' => 'en_cours',
            'photo' => $photoPath,
            'video' => $videoPath,
            'reported_at' => $now,
        ]);

        $servicesLabel = implode(', ', $analysis['services']);
        $signalement->timelineSteps()->createMany([
            ['label' => 'Signalement reçu', 'done' => true, 'occurred_at' => $now, 'sort_order' => 1],
            ['label' => 'Analyse IA — crédibilité '.$analysis['credibility_score'].'/100', 'done' => true, 'occurred_at' => $now->copy()->addSeconds(15), 'sort_order' => 2],
            ['label' => 'Validation IA : '.$analysis['verdict_label'], 'done' => true, 'occurred_at' => $now->copy()->addSeconds(30), 'sort_order' => 3],
            ['label' => 'Priorité '.$analysis['priority_label'].' (score '.$analysis['priority_rank'].')', 'done' => true, 'occurred_at' => $now->copy()->addSeconds(45), 'sort_order' => 4],
            ['label' => 'Services alertés : '.$servicesLabel, 'done' => true, 'occurred_at' => $now->copy()->addMinute(), 'sort_order' => 5],
            ['label' => 'Unité dispatchée'.($nearest ? ' — '.$nearest[0]['service']->name : ''), 'done' => (bool) $assignedServiceId, 'occurred_at' => $assignedServiceId ? $now->copy()->addMinutes(2) : null, 'sort_order' => 6],
            ['label' => 'Intervention en cours', 'done' => false, 'occurred_at' => null, 'sort_order' => 7],
            ['label' => 'Intervention clôturée', 'done' => false, 'occurred_at' => null, 'sort_order' => 8],
        ]);

        $successMsg = $analysis['verdict'] === EmergencyAnalysisService::VERDICT_REVIEW
            ? 'Signalement enregistré sous vérification IA (crédibilité '.$analysis['credibility_score'].'/100).'
            : 'Urgence validée ! Priorité '.$analysis['priority_label'].' — score '.$analysis['priority_rank'].'/100.';

        PlatformStatsCalculator::sync();

        NotifySignalementUpdateJob::dispatchSync(
            $signalement->id,
            'Votre signalement '.$signalement->reference.' a été enregistré et transmis aux services compétents.',
            'creation'
        );

        User::where('role', User::ROLE_ADMIN)->each(function (User $admin) use ($signalement) {
            $admin->notify(new NewSignalementAdminNotification($signalement));
        });

        return redirect()
            ->route('signalement.show', $signalement->reference)
            ->with('success', $successMsg);
    }

    public function cancel(string $id)
    {
        $signalement = Signalement::where('reference', $id)
            ->where('user_id', Auth::id())
            ->where('statut', 'en_cours')
            ->firstOrFail();

        $signalement->update(['statut' => 'annule']);

        return redirect()->route('history')->with('success', 'Signalement annulé avec succès.');
    }

    private function generateReference(): string
    {
        $next = (Signalement::max('id') ?? 0) + 1;

        return 'SIG-'.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
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
