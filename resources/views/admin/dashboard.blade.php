@extends('layouts.app')

@section('title', 'Administration — Smart Emergency AI')
@section('page-title', 'Tableau de bord administrateur')
@section('page-subtitle', 'Vue d\'ensemble de la plateforme')

@section('content')

    <div class="admin-hero mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold mb-1">Bonjour, {{ $user['name'] }}</h4>
                <p class="mb-0 opacity-75">Supervisez les urgences, les citoyens et les interventions en temps réel.</p>
            </div>
            @if($stats['urgences_actives'] > 0)
                <div class="admin-alert-pill">
                    <i class="bi bi-exclamation-octagon-fill me-2"></i>
                    {{ $stats['urgences_actives'] }} urgence(s) critique(s) active(s)
                </div>
            @elseif($stats['pending_ai_review'] > 0)
                <a href="{{ route('admin.ai-review.index') }}" class="admin-alert-pill text-decoration-none" style="background:#f39c12;">
                    <i class="bi bi-robot me-2"></i>
                    {{ $stats['pending_ai_review'] }} signalement(s) à vérifier (IA)
                </a>
            @endif
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="dash-card admin-stat-card">
                <div class="dash-card-icon bg-primary-subtle text-primary"><i class="bi bi-file-earmark-medical-fill"></i></div>
                <div class="dash-card-value">{{ $stats['total'] }}</div>
                <div class="dash-card-label">Signalements totaux</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="dash-card admin-stat-card">
                <div class="dash-card-icon bg-warning-subtle text-warning"><i class="bi bi-hourglass-split"></i></div>
                <div class="dash-card-value">{{ $stats['en_cours'] }}</div>
                <div class="dash-card-label">En cours</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="dash-card admin-stat-card">
                <div class="dash-card-icon bg-success-subtle text-success"><i class="bi bi-check-circle-fill"></i></div>
                <div class="dash-card-value">{{ $stats['termines'] }}</div>
                <div class="dash-card-label">Terminés</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="dash-card admin-stat-card">
                <div class="dash-card-icon bg-danger-subtle text-danger"><i class="bi bi-exclamation-octagon-fill"></i></div>
                <div class="dash-card-value">{{ $stats['critiques'] }}</div>
                <div class="dash-card-label">Critiques</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="dash-card admin-stat-card">
                <div class="dash-card-icon bg-info-subtle text-info"><i class="bi bi-people-fill"></i></div>
                <div class="dash-card-value">{{ $stats['users'] }}</div>
                <div class="dash-card-label">Citoyens inscrits</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dash-card admin-stat-card">
                <div class="dash-card-icon bg-secondary-subtle text-secondary"><i class="bi bi-tags-fill"></i></div>
                <div class="dash-card-value">{{ $stats['categories'] }}</div>
                <div class="dash-card-label">Catégories</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dash-card admin-stat-card">
                <div class="dash-card-icon bg-danger-subtle text-danger"><i class="bi bi-lightning-charge-fill"></i></div>
                <div class="dash-card-value">{{ $stats['urgences_actives'] }}</div>
                <div class="dash-card-label">Critiques actives</div>
            </div>
        </div>
    </div>

    @if($pendingReview->count() > 0)
    <div class="sea-card p-0 overflow-hidden mb-4 border-warning">
        <div class="p-3 bg-warning text-dark fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-robot me-2"></i>File de vérification IA</span>
            <a href="{{ route('admin.ai-review.index') }}" class="btn btn-sm btn-dark">Voir tout</a>
        </div>
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead><tr><th>Réf.</th><th>Citoyen</th><th>Catégorie</th><th>Crédibilité</th><th>Priorité</th><th></th></tr></thead>
                <tbody>
                    @foreach($pendingReview as $sig)
                        <tr>
                            <td class="fw-semibold">{{ $sig->reference }}</td>
                            <td>{{ $sig->user->name }}</td>
                            <td>{{ $sig->category->name }}</td>
                            <td>{{ $sig->ai_credibility_score }}/100</td>
                            <td>{{ $sig->ai_priority_rank }}/100</td>
                            <td>
                                <a href="{{ route('admin.signalements.show', $sig->reference) }}" class="btn btn-sm btn-warning">Examiner</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="sea-card p-4 h-100">
                <h5 class="fw-semibold mb-4">Signalements par catégorie</h5>
                @foreach($byCategory as $item)
                    <div class="admin-chart-row mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>{{ $item['name'] }}</span>
                            <strong>{{ $item['count'] }}</strong>
                        </div>
                        <div class="admin-chart-bar">
                            <div class="admin-chart-fill" style="width: {{ $maxCategory > 0 ? ($item['count'] / $maxCategory * 100) : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-lg-6">
            <div class="sea-card p-4 h-100">
                <h5 class="fw-semibold mb-4">Répartition par gravité</h5>
                @php
                    $graviteLabels = ['critique' => 'Critique', 'elevee' => 'Élevée', 'moyenne' => 'Moyenne', 'faible' => 'Faible'];
                @endphp
                @foreach($byGravite as $gravite => $count)
                    <div class="admin-chart-row mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>{{ $graviteLabels[$gravite] }}</span>
                            <strong>{{ $count }}</strong>
                        </div>
                        <div class="admin-chart-bar">
                            <div class="admin-chart-fill gravite-fill-{{ $gravite }}" style="width: {{ $maxGravite > 0 ? ($count / $maxGravite * 100) : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="sea-card p-0 overflow-hidden">
        <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
            <h5 class="fw-semibold mb-0">Signalements récents</h5>
            <a href="{{ route('admin.signalements.index') }}" class="btn btn-sm btn-outline-primary">Tout voir</a>
        </div>
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th>Réf.</th>
                        <th>Catégorie</th>
                        <th>Citoyen</th>
                        <th>Localisation</th>
                        <th>Gravité</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent as $sig)
                        <tr>
                            <td class="fw-semibold">{{ $sig->reference }}</td>
                            <td>{{ $sig->category->name }}</td>
                            <td>{{ $sig->user->name }}</td>
                            <td>{{ Str::limit($sig->localisation, 35) }}</td>
                            <td>@include('partials.gravite-badge', ['gravite' => $sig->gravite])</td>
                            <td>@include('partials.statut-badge', ['statut' => $sig->statut])</td>
                            <td>{{ $sig->reported_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.signalements.show', $sig->reference) }}" class="btn btn-sm btn-light">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Aucun signalement.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
