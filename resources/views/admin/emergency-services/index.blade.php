@extends('layouts.app')

@section('title', 'Services de secours — Administration')
@section('page-title', 'Services de secours')
@section('page-subtitle', $services->count() . ' unité(s) enregistrée(s)')

@section('content')

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="sea-card p-4">
                <h5 class="fw-semibold mb-3"><i class="bi bi-plus-circle me-2"></i>Ajouter une unité</h5>
                <form action="{{ route('admin.emergency-services.store') }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Nom</label>
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="Caserne Pompiers..." required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Type</label>
                        <select name="type" class="form-select form-select-sm" required>
                            @foreach($types as $type)
                                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Téléphone</label>
                        <input type="text" name="phone" class="form-control form-control-sm" placeholder="18, 17, 15..." required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Adresse</label>
                        <input type="text" name="address" class="form-control form-control-sm" required>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Latitude</label>
                            <input type="number" step="any" name="latitude" class="form-control form-control-sm" placeholder="13.51" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Longitude</label>
                            <input type="number" step="any" name="longitude" class="form-control form-control-sm" placeholder="2.11" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Zone / Quartier</label>
                        <input type="text" name="zone" class="form-control form-control-sm" placeholder="Plateau, Yantala...">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-sm">
                        <i class="bi bi-plus-lg me-1"></i> Enregistrer
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="sea-card p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table admin-table mb-0">
                        <thead>
                            <tr>
                                <th>Unité</th>
                                <th>Type</th>
                                <th>Contact</th>
                                <th>Zone</th>
                                <th>Actifs</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($services as $service)
                                <tr>
                                    <td>
                                        <form action="{{ route('admin.emergency-services.update', $service) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="name" class="form-control form-control-sm mb-1" value="{{ $service->name }}" required>
                                            <input type="text" name="address" class="form-control form-control-sm" value="{{ $service->address }}" required>
                                            <div class="row g-1 mt-1">
                                                <div class="col-4">
                                                    <select name="type" class="form-select form-select-sm">
                                                        @foreach($types as $type)
                                                            <option value="{{ $type }}" @selected($service->type === $type)>{{ ucfirst($type) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-4">
                                                    <input type="text" name="phone" class="form-control form-control-sm" value="{{ $service->phone }}" required>
                                                </div>
                                                <div class="col-4">
                                                    <input type="text" name="zone" class="form-control form-control-sm" value="{{ $service->zone }}" placeholder="Zone">
                                                </div>
                                            </div>
                                            <div class="row g-1 mt-1">
                                                <div class="col-6">
                                                    <input type="number" step="any" name="latitude" class="form-control form-control-sm" value="{{ $service->latitude }}" required>
                                                </div>
                                                <div class="col-6">
                                                    <input type="number" step="any" name="longitude" class="form-control form-control-sm" value="{{ $service->longitude }}" required>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-primary mt-2"><i class="bi bi-check-lg me-1"></i> Sauvegarder</button>
                                        </form>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge bg-secondary">{{ $service->typeLabel() }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <a href="tel:{{ $service->phone }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-telephone"></i> {{ $service->phone }}
                                        </a>
                                    </td>
                                    <td class="align-middle small">{{ $service->zone ?? '—' }}</td>
                                    <td class="align-middle">
                                        <span class="badge {{ $service->signalements_count > 0 ? 'bg-warning text-dark' : 'bg-light text-muted' }}">
                                            {{ $service->signalements_count }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        @if($service->signalements_count === 0)
                                            <form action="{{ route('admin.emergency-services.destroy', $service) }}" method="POST"
                                                  onsubmit="return confirm('Supprimer ce service ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-5">Aucun service enregistré.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
