@extends('layouts.app')

@section('title', 'Tableau de bord — Smart Emergency AI')
@section('page-title', 'Tableau de bord citoyen')
@section('page-subtitle', 'Bienvenue ' . $user['name'])

@section('content')

    {{-- Message de bienvenue --}}
    <div class="welcome-banner mb-4">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ $user['avatar'] }}" alt="{{ $user['name'] }}" class="welcome-avatar">
            <div>
                <h4 class="fw-bold mb-1">Bienvenue {{ $user['name'] }}</h4>
                <p class="text-muted mb-0 small">Gérez vos signalements et suivez les interventions en cours.</p>
            </div>
        </div>
    </div>

    {{-- Cartes statistiques --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="dash-card">
                <div class="dash-card-icon bg-primary-subtle text-primary">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </div>
                <div class="dash-card-value">{{ $stats['total'] }}</div>
                <div class="dash-card-label">Mes signalements</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="dash-card">
                <div class="dash-card-icon bg-warning-subtle text-warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="dash-card-value">{{ $stats['en_cours'] }}</div>
                <div class="dash-card-label">En cours</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="dash-card">
                <div class="dash-card-icon bg-success-subtle text-success">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="dash-card-value">{{ $stats['termines'] }}</div>
                <div class="dash-card-label">Terminés</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="dash-card">
                <div class="dash-card-icon bg-danger-subtle text-danger">
                    <i class="bi bi-exclamation-octagon-fill"></i>
                </div>
                <div class="dash-card-value">{{ $stats['critiques'] }}</div>
                <div class="dash-card-label">Urgences critiques</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Actions rapides --}}
        <div class="col-lg-4">
            <div class="sea-card p-4 mb-4">
                <h5 class="fw-semibold mb-3">Actions rapides</h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('report.quick') }}" class="btn btn-danger">
                        <i class="bi bi-lightning-charge-fill me-2"></i> Urgence rapide
                    </a>
                    <a href="{{ route('report') }}" class="btn btn-primary">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Signalement complet
                    </a>
                    <a href="{{ route('services.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-telephone-fill me-2"></i> Annuaire secours
                    </a>
                    <a href="{{ route('history') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-clock-history me-2"></i> Voir l'historique
                    </a>
                </div>
            </div>

            <div class="sea-card p-4">
                <h6 class="fw-semibold mb-3"><i class="bi bi-building me-2"></i>Secours à Niamey</h6>
                @foreach($emergencyServices as $svc)
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <div class="small">
                            <div class="fw-semibold">{{ $svc->name }}</div>
                            <span class="text-muted">{{ $svc->typeLabel() }}</span>
                        </div>
                        <a href="tel:{{ $svc->phone }}" class="btn btn-sm btn-outline-primary">{{ $svc->phone }}</a>
                    </div>
                @endforeach
                <a href="{{ route('services.index') }}" class="small">Voir tout l'annuaire →</a>
            </div>
        </div>

        {{-- Signalements récents --}}
        <div class="col-lg-8">
            <div class="sea-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-semibold mb-0">Signalements récents</h5>
                    <a href="{{ route('history') }}" class="btn btn-sm btn-link">Tout voir</a>
                </div>

                @foreach($recent as $sig)
                    <a href="{{ route('signalement.show', $sig['id']) }}" class="recent-item text-decoration-none">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold">{{ $sig['categorie'] }}</div>
                                <small class="text-muted">{{ $sig['date'] }} — {{ $sig['localisation'] }}</small>
                            </div>
                            <div class="d-flex flex-column align-items-end gap-1">
                                @include('partials.gravite-badge', ['gravite' => $sig['gravite']])
                                @include('partials.statut-badge', ['statut' => $sig['statut']])
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

@endsection
