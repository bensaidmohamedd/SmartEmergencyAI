@extends('layouts.app')

@section('title', 'Annuaire des secours — Smart Emergency AI')
@section('page-title', 'Annuaire des secours')
@section('page-subtitle', 'Services d\'urgence au Niger — appelez directement')

@section('content')

    <div class="row g-3 mb-4">
        @foreach([
            ['num' => '18', 'label' => 'Pompiers', 'icon' => 'fire', 'color' => 'danger'],
            ['num' => '17', 'label' => 'Police / Gendarmerie', 'icon' => 'shield-fill', 'color' => 'primary'],
            ['num' => '15', 'label' => 'SAMU / Ambulance', 'icon' => 'heart-pulse-fill', 'color' => 'success'],
        ] as $hotline)
            <div class="col-md-4">
                <a href="tel:{{ $hotline['num'] }}" class="sea-card p-4 d-block text-decoration-none hotline-card text-center">
                    <i class="bi bi-{{ $hotline['icon'] }} fs-1 text-{{ $hotline['color'] }} mb-2 d-block"></i>
                    <div class="fw-bold fs-3">{{ $hotline['num'] }}</div>
                    <div class="text-muted">{{ $hotline['label'] }}</div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="filter-bar mb-4">
        <form method="GET" action="{{ route('services.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <select name="type" class="form-select">
                    <option value="all">Tous les types</option>
                    @foreach(['pompiers' => 'Pompiers', 'police' => 'Police', 'samu' => 'SAMU', 'gendarmerie' => 'Gendarmerie'] as $val => $label)
                        <option value="{{ $val }}" @selected(($filters['type'] ?? 'all') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="q" class="form-control" value="{{ $filters['q'] ?? '' }}" placeholder="Rechercher par nom ou zone...">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i> Filtrer</button>
            </div>
        </form>
    </div>

    <div class="row g-3">
        @forelse($services as $service)
            <div class="col-md-6 col-lg-4">
                <div class="sea-card p-4 h-100 service-card">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="service-icon bg-{{ match($service->type) { 'pompiers' => 'danger', 'samu' => 'success', 'police' => 'primary', default => 'secondary' } }}-subtle">
                            <i class="bi bi-{{ $service->typeIcon() }}"></i>
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-0">{{ $service->name }}</h6>
                            <small class="text-muted">{{ $service->typeLabel() }} @if($service->zone)— {{ $service->zone }}@endif</small>
                        </div>
                    </div>
                    <p class="small text-muted mb-3"><i class="bi bi-geo-alt me-1"></i>{{ $service->address }}</p>
                    <div class="d-flex gap-2">
                        <a href="tel:{{ $service->phone }}" class="btn btn-primary btn-sm flex-grow-1">
                            <i class="bi bi-telephone-fill me-1"></i> {{ $service->phone }}
                        </a>
                        @if($service->latitude && $service->longitude)
                            <a href="https://www.google.com/maps?q={{ $service->latitude }},{{ $service->longitude }}"
                               target="_blank" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-map"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="sea-card p-5 text-center text-muted">Aucun service trouvé.</div>
            </div>
        @endforelse
    </div>

@endsection
