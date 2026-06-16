@extends('layouts.app')

@section('title', $signalement['categorie'] . ' — Détails signalement')
@section('page-title', 'Détails du signalement')
@section('page-subtitle', $signalement['id'] . ' — ' . $signalement['date'])

@section('content')

    <div class="mb-3 d-flex gap-2 flex-wrap">
        <a href="{{ route('history') }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i> Retour à l'historique
        </a>
        <a href="{{ route('signalement.attestation', $signalement['id']) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-file-earmark-text me-1"></i> Attestation / Imprimer
        </a>
        <a href="{{ route('signalement.attestation.download', $signalement['id']) }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-download me-1"></i> Télécharger
        </a>
        @if($signalement['whatsapp_url'])
            <a href="{{ $signalement['whatsapp_url'] }}" target="_blank" class="btn btn-sm btn-success">
                <i class="bi bi-whatsapp me-1"></i> Partager position
            </a>
        @endif
        @if($signalement['statut'] === 'en_cours')
            <form action="{{ route('signalement.cancel', $signalement['id']) }}" method="POST"
                  onsubmit="return confirm('Annuler ce signalement ?')">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-x-circle me-1"></i> Annuler le signalement
                </button>
            </form>
        @endif
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
                    <p class="mb-2">
                        <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                        {{ $signalement['localisation'] }}
                    </p>
                    @if(!empty($signalement['latitude']) && !empty($signalement['longitude']))
                        <p class="text-muted small mb-2">
                            GPS : {{ number_format($signalement['latitude'], 5) }}, {{ number_format($signalement['longitude'], 5) }}
                        </p>
                        <a href="{{ $signalement['maps_url'] }}" target="_blank" rel="noopener"
                           class="btn btn-sm btn-outline-primary mb-3">
                            <i class="bi bi-map me-1"></i> Ouvrir dans Google Maps
                        </a>
                        <div class="geo-map-detail rounded-3 overflow-hidden">
                            <iframe
                                title="Carte de localisation"
                                src="https://maps.google.com/maps?q={{ $signalement['latitude'] }},{{ $signalement['longitude'] }}&z=16&output=embed"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    @endif
                </div>

                @if(!empty($signalement['photo']))
                    <div class="detail-section mb-4">
                        <h6 class="fw-semibold text-muted text-uppercase small mb-2">Photo</h6>
                        <img src="{{ $signalement['photo'] }}" alt="Photo du signalement"
                             class="detail-photo rounded-3 w-100">
                    </div>
                @endif

                @if(!empty($signalement['video']))
                    <div class="detail-section">
                        <h6 class="fw-semibold text-muted text-uppercase small mb-2">Vidéo</h6>
                        <video src="{{ $signalement['video'] }}" controls class="w-100 rounded-3"></video>
                    </div>
                @endif
            </div>
        </div>

        {{-- Timeline + IA --}}
        <div class="col-lg-5">
            @if($signalement['ai_score'])
                <div class="sea-card p-4 mb-4 ai-result-card">
                    <h5 class="fw-semibold mb-3"><i class="bi bi-robot text-primary me-2"></i>Analyse IA</h5>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="ai-score-ring ai-score-ring-sm"><span>{{ $signalement['ai_score'] }}</span></div>
                        <div>
                            <div class="fw-bold">{{ $signalement['priority_label'] }}</div>
                            @include('partials.gravite-badge', ['gravite' => $signalement['gravite']])
                        </div>
                    </div>
                    <p class="small mb-3">{{ $signalement['ai_summary'] }}</p>
                    @if($signalement['ai_services'])
                        <div class="d-flex flex-wrap gap-1 mb-2">
                            @foreach($signalement['ai_services'] as $svc)
                                <span class="badge bg-danger-subtle text-danger">{{ $svc }}</span>
                            @endforeach
                        </div>
                    @endif
                    @if($signalement['estimated_response_min'])
                        <small class="text-muted"><i class="bi bi-clock me-1"></i> ETA : ~{{ $signalement['estimated_response_min'] }} min</small>
                    @endif
                    @if($signalement['assigned_service'])
                        <div class="mt-2 small"><i class="bi bi-truck me-1"></i> Unité dispatchée : <strong>{{ $signalement['assigned_service'] }}</strong></div>
                    @endif
                </div>
            @endif

            @if(!empty($nearestServices))
                <div class="sea-card p-4 mb-4">
                    <h6 class="fw-semibold mb-3"><i class="bi bi-geo-alt me-2"></i>Services les plus proches</h6>
                    @foreach($nearestServices as $item)
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <div>
                                <div class="fw-semibold small">{{ $item['service']->name }}</div>
                                <small class="text-muted">{{ $item['service']->typeLabel() }} — {{ $item['distance_km'] }} km</small>
                            </div>
                            <a href="tel:{{ $item['service']->phone }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-telephone"></i> {{ $item['service']->phone }}
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif

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
