@extends('layouts.app')

@section('title', 'Historique — Smart Emergency AI')
@section('page-title', 'Historique des signalements')
@section('page-subtitle', count($signalements) . ' signalement(s) au total')

@section('content')

    {{-- Filtres --}}
    <div class="filter-bar mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="historySearch" class="form-label small fw-semibold">Rechercher</label>
                <input type="text" class="form-control" id="historySearch" placeholder="Catégorie, lieu...">
            </div>
            <div class="col-md-3">
                <label for="historyGravite" class="form-label small fw-semibold">Gravité</label>
                <select class="form-select" id="historyGravite">
                    <option value="all">Toutes</option>
                    <option value="critique">Critique</option>
                    <option value="elevee">Élevée</option>
                    <option value="moyenne">Moyenne</option>
                    <option value="faible">Faible</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="historyStatut" class="form-label small fw-semibold">Statut</label>
                <select class="form-select" id="historyStatut">
                    <option value="all">Tous</option>
                    <option value="en_cours">En cours</option>
                    <option value="termine">Terminé</option>
                </select>
            </div>
            <div class="col-md-2">
                <span class="text-muted small" id="historyCount">{{ count($signalements) }} résultat(s)</span>
            </div>
        </div>
    </div>

    {{-- Légende couleurs gravité --}}
    <div class="d-flex flex-wrap gap-3 mb-4 small">
        <span><span class="legend-dot gravite-critique"></span> Critique</span>
        <span><span class="legend-dot gravite-elevee"></span> Élevée</span>
        <span><span class="legend-dot gravite-moyenne"></span> Moyenne</span>
        <span><span class="legend-dot gravite-faible"></span> Faible</span>
    </div>

    {{-- Cartes signalements --}}
    <div class="row g-3" id="historyGrid">
        @foreach($signalements as $sig)
            <div class="col-md-6 col-xl-4 history-card-wrapper"
                 data-gravite="{{ $sig['gravite'] }}"
                 data-statut="{{ $sig['statut'] }}">
                <a href="{{ route('signalement.show', $sig['id']) }}" class="signalement-card text-decoration-none">
                    <div class="signalement-card-header gravite-border-{{ $sig['gravite'] }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="fw-bold mb-1">{{ $sig['categorie'] }}</h5>
                                <small class="text-muted">{{ $sig['date'] }}</small>
                            </div>
                            @include('partials.gravite-badge', ['gravite' => $sig['gravite']])
                        </div>
                    </div>
                    <div class="signalement-card-body">
                        <p class="text-muted small mb-3 text-truncate-2">{{ $sig['description'] }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bi bi-geo-alt me-1"></i>{{ Str::limit($sig['localisation'], 30) }}
                            </small>
                            @include('partials.statut-badge', ['statut' => $sig['statut']])
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    {{-- Message si aucun résultat --}}
    <div class="text-center py-5 d-none" id="historyEmpty">
        <i class="bi bi-inbox fs-1 text-muted"></i>
        <p class="text-muted mt-2">Aucun signalement ne correspond à vos filtres.</p>
    </div>

@endsection
