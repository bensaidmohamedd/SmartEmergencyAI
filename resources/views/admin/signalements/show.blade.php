@extends('layouts.app')

@section('title', $signalement->reference . ' — Administration')
@section('page-title', 'Signalement ' . $signalement->reference)
@section('page-subtitle', $data['categorie'] . ' — ' . $data['date'])

@section('content')

    <div class="mb-3">
        <a href="{{ route('admin.signalements.index') }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i> Retour à la liste
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="sea-card p-4 mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
                    <div>
                        <h3 class="fw-bold mb-1">{{ $data['categorie'] }}</h3>
                        <small class="text-muted"><i class="bi bi-calendar me-1"></i>{{ $data['date'] }} à {{ $data['heure'] }}</small>
                    </div>
                    <div class="d-flex gap-2">
                        @include('partials.gravite-badge', ['gravite' => $data['gravite']])
                        @include('partials.statut-badge', ['statut' => $data['statut']])
                    </div>
                </div>

                <div class="detail-section mb-4">
                    <h6 class="fw-semibold text-muted text-uppercase small mb-2">Description</h6>
                    <p class="mb-0">{{ $data['description'] }}</p>
                </div>

                <div class="detail-section mb-4">
                    <h6 class="fw-semibold text-muted text-uppercase small mb-2">Localisation</h6>
                    <p class="mb-2"><i class="bi bi-geo-alt-fill text-primary me-2"></i>{{ $data['localisation'] }}</p>
                    @if($data['latitude'] && $data['longitude'])
                        <a href="{{ $data['maps_url'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary mb-3">
                            <i class="bi bi-map me-1"></i> Google Maps
                        </a>
                        <div class="geo-map-detail rounded-3 overflow-hidden">
                            <iframe title="Carte" src="https://maps.google.com/maps?q={{ $data['latitude'] }},{{ $data['longitude'] }}&z=16&output=embed" loading="lazy"></iframe>
                        </div>
                    @endif
                </div>

                @if($data['photo'])
                    <div class="detail-section mb-4">
                        <h6 class="fw-semibold text-muted text-uppercase small mb-2">Photo</h6>
                        <img src="{{ $data['photo'] }}" alt="Photo" class="detail-photo rounded-3 w-100">
                    </div>
                @endif

                @if($data['video'])
                    <div class="detail-section">
                        <h6 class="fw-semibold text-muted text-uppercase small mb-2">Vidéo</h6>
                        <video src="{{ $data['video'] }}" controls class="w-100 rounded-3"></video>
                    </div>
                @endif
            </div>

            <div class="sea-card p-4">
                <h5 class="fw-semibold mb-4">Citoyen signalant</h5>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ $signalement->user->toViewArray()['avatar'] }}" alt="" class="welcome-avatar" style="width:48px;height:48px;">
                    <div>
                        <div class="fw-semibold">{{ $signalement->user->name }}</div>
                        <small class="text-muted">{{ $signalement->user->email }} · {{ $signalement->user->phone }}</small>
                    </div>
                </div>
                <a href="{{ route('admin.users.show', $signalement->user) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-person me-1"></i> Voir le profil
                </a>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="sea-card p-4 mb-4">
                <h5 class="fw-semibold mb-3">Dispatch unité de secours</h5>
                <form action="{{ route('admin.signalements.dispatch', $signalement->reference) }}" method="POST" class="mb-2">
                    @csrf
                    <div class="input-group">
                        <select name="assigned_service_id" class="form-select" required>
                            <option value="">Choisir une unité...</option>
                            @foreach($emergencyServices as $svc)
                                <option value="{{ $svc->id }}" @selected($signalement->assigned_service_id === $svc->id)>
                                    {{ $svc->name }} ({{ $svc->typeLabel() }}) — {{ $svc->phone }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">Assigner</button>
                    </div>
                </form>
                <form action="{{ route('admin.signalements.auto-dispatch', $signalement->reference) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                        <i class="bi bi-lightning-charge me-1"></i> Dispatch automatique (plus proche)
                    </button>
                </form>
            </div>

            <div class="sea-card p-4 mb-4">
                <h5 class="fw-semibold mb-3">Modifier l'intervention</h5>
                <form action="{{ route('admin.signalements.update', $signalement->reference) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Statut</label>
                        <select name="statut" class="form-select">
                            <option value="en_cours" @selected($signalement->statut === 'en_cours')>En cours</option>
                            <option value="termine" @selected($signalement->statut === 'termine')>Terminé</option>
                            <option value="annule" @selected($signalement->statut === 'annule')>Annulé</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Gravité</label>
                        <select name="gravite" class="form-select">
                            @foreach(['critique','elevee','moyenne','faible'] as $g)
                                <option value="{{ $g }}" @selected($signalement->gravite === $g)>{{ ucfirst($g) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-check-lg me-1"></i> Enregistrer
                    </button>
                </form>
                <form action="{{ route('admin.signalements.destroy', $signalement->reference) }}" method="POST"
                      onsubmit="return confirm('Supprimer définitivement ce signalement ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-trash me-1"></i> Supprimer
                    </button>
                </form>
            </div>

            <div class="sea-card p-4">
                <h5 class="fw-semibold mb-4">Timeline d'intervention</h5>
                <div class="timeline">
                    @foreach($signalement->timelineSteps as $step)
                        <div class="timeline-item {{ $step->done ? 'done' : '' }}">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="fw-semibold">{{ $step->label }}</div>
                                        @if($step->occurred_at)
                                            <small class="text-muted">{{ $step->occurred_at->format('d/m/Y H:i') }}</small>
                                        @endif
                                    </div>
                                    <form action="{{ route('admin.signalements.timeline', [$signalement->reference, $step]) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="done" value="{{ $step->done ? '0' : '1' }}">
                                        <button type="submit" class="btn btn-sm {{ $step->done ? 'btn-success' : 'btn-outline-secondary' }}">
                                            <i class="bi bi-{{ $step->done ? 'check-lg' : 'circle' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

@endsection
