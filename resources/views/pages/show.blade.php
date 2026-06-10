@extends('layouts.app')

@section('title', $signalement['categorie'] . ' — Détails signalement')
@section('page-title', 'Détails du signalement')
@section('page-subtitle', $signalement['id'] . ' — ' . $signalement['date'])

@section('content')

    <div class="mb-3">
        <a href="{{ route('history') }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i> Retour à l'historique
        </a>
    </div>

    <div class="row g-4">
        {{-- Informations principales --}}
        <div class="col-lg-7">
            <div class="sea-card p-4 mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
                    <div>
                        <h3 class="fw-bold mb-1">{{ $signalement['categorie'] }}</h3>
                        <small class="text-muted">
                            <i class="bi bi-calendar me-1"></i>{{ $signalement['date'] }} à {{ $signalement['heure'] }}
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        @include('partials.gravite-badge', ['gravite' => $signalement['gravite']])
                        @include('partials.statut-badge', ['statut' => $signalement['statut']])
                    </div>
                </div>

                <div class="detail-section mb-4">
                    <h6 class="fw-semibold text-muted text-uppercase small mb-2">Description</h6>
                    <p class="mb-0">{{ $signalement['description'] }}</p>
                </div>

                <div class="detail-section mb-4">
                    <h6 class="fw-semibold text-muted text-uppercase small mb-2">Localisation</h6>
                    <p class="mb-0">
                        <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                        {{ $signalement['localisation'] }}
                    </p>
                </div>

                <div class="detail-section">
                    <h6 class="fw-semibold text-muted text-uppercase small mb-2">Photo</h6>
                    <img src="{{ $signalement['photo'] }}" alt="Photo du signalement"
                        class="detail-photo rounded-3 w-100">
                </div>
            </div>
        </div>

        {{-- Timeline --}}
        <div class="col-lg-5">
            <div class="sea-card p-4">
                <h5 class="fw-semibold mb-4">
                    <i class="bi bi-clock-history text-primary me-2"></i>
                    Suivi de l'intervention
                </h5>

                <div class="timeline">
                    @foreach($signalement['timeline'] as $step)
                        <div class="timeline-item {{ $step['done'] ? 'done' : 'pending' }}">
                            <div class="timeline-marker">
                                @if($step['done'])
                                    <i class="bi bi-check-lg"></i>
                                @else
                                    <span class="timeline-circle"></span>
                                @endif
                            </div>
                            <div class="timeline-content">
                                <div class="fw-semibold">{{ $step['label'] }}</div>
                                @if($step['time'])
                                    <small class="text-muted">{{ $step['time'] }}</small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

@endsection
