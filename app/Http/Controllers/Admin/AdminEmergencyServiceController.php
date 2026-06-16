<?php

namespace App\Http\Controllers\Admin;

use App\Models\EmergencyService;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class AdminEmergencyServiceController extends AdminController
{
    public function index()
    {
        $services = EmergencyService::withCount([
            'signalements' => fn ($q) => $q->where('statut', 'en_cours'),
        ])->orderBy('type')->orderBy('name')->get();

        return view('admin.emergency-services.index', [
            'layout' => 'admin',
            'user' => $this->adminUser()->toViewArray(),
            'services' => $services,
            'types' => ['pompiers', 'police', 'samu', 'gendarmerie'],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:pompiers,police,samu,gendarmerie'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:500'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'zone' => ['nullable', 'string', 'max:100'],
        ]);

        $service = EmergencyService::create($validated);
        AuditLogger::log('emergency_service.create', $service, $validated);

        return back()->with('success', 'Service de secours ajouté.');
    }

    public function update(Request $request, EmergencyService $service)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:pompiers,police,samu,gendarmerie'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:500'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'zone' => ['nullable', 'string', 'max:100'],
        ]);

        $service->update($validated);
        AuditLogger::log('emergency_service.update', $service, $validated);

        return back()->with('success', 'Service mis à jour.');
    }

    public function destroy(EmergencyService $service)
    {
        if ($service->signalements()->exists()) {
            return back()->withErrors(['delete' => 'Impossible de supprimer un service assigné à des signalements.']);
        }

        AuditLogger::log('emergency_service.delete', $service);
        $service->delete();

        return back()->with('success', 'Service supprimé.');
    }
}
