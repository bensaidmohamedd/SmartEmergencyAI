@extends('layouts.app')

@section('title', 'Historique — Smart Emergency AI')
@section('page-title', 'Historique des signalements')
@section('page-subtitle', $signalements->total() . ' signalement(s) au total')

@section('content')

    <div class="filter-bar mb-4">
        <form method="GET" action="{{ route('history') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Rechercher</label>
                    <input type="text" class="form-control" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Réf., lieu...">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Gravité</label>
                    <select class="form-select" name="gravite">
                        <option value="all">Toutes</option>
                        @foreach(['critique','elevee','moyenne','faible'] as $g)
                            <option value="{{ $g }}" @selected(($filters['gravite'] ?? 'all') === $g)>{{ ucfirst($g) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Statut</label>
                    <select class="form-select" name="statut">
                        <option value="all">Tous</option>
                        <option value="en_cours" @selected(($filters['statut'] ?? '') === 'en_cours')>En cours</option>
                        <option value="termine" @selected(($filters['statut'] ?? '') === 'termine')>Terminé</option>
                        <option value="annule" @selected(($filters['statut'] ?? '') === 'annule')>Annulé</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </form>
    </div>

    <div class="row g-3">
        @forelse($signalements as $sig)
            @php $data = $sig->toViewArray(); @endphp
            <div class="col-md-6 col-xl-4">
                <a href="{{ route('signalement.show', $data['id']) }}" class="signalement-card text-decoration-none">
                    <div class="signalement-card-header gravite-border-{{ $data['gravite'] }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="fw-bold mb-1">{{ $data['categorie'] }}</h5>
                                <small class="text-muted">{{ $data['id'] }} — {{ $data['date'] }}</small>
                            </div>
                            @include('partials.gravite-badge', ['gravite' => $data['gravite']])
                        </div>
                    </div>
                    <div class="signalement-card-body">
                        <p class="text-muted small mb-3 text-truncate-2">{{ $data['description'] }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ Str::limit($data['localisation'], 30) }}</small>
                            @include('partials.statut-badge', ['statut' => $data['statut']])
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <p class="text-muted mt-2">Aucun signalement trouvé.</p>
            </div>
        @endforelse
    </div>

    @if($signalements->hasPages())
        <div class="mt-4">{{ $signalements->links() }}</div>
    @endif

@endsection
