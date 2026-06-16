@extends('layouts.app')

@section('title', 'Impact — Smart Emergency AI Niger')

@section('content')

<section class="landing-hero py-5">
    <div class="container text-center">
        <div class="hero-badge mb-3"><span>Rapport d'impact — Concours</span></div>
        <h1 class="display-5 fw-bold mb-3">Smart Emergency AI en chiffres</h1>
        <p class="lead text-muted mx-auto" style="max-width:700px;">
            Plateforme intelligente de déclaration d'incendies et d'urgences pour le Niger.
            Données en temps réel issues de la base de données.
        </p>
    </div>
</section>

<section class="container pb-5">
    <div class="row g-4 mb-5">
        <div class="col-6 col-md-4 col-lg-2">
            <div class="dash-card text-center"><div class="dash-card-value">{{ $stats['total'] }}</div><div class="dash-card-label">Signalements</div></div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="dash-card text-center"><div class="dash-card-value text-danger">{{ $stats['incendies'] }}</div><div class="dash-card-label">Incendies</div></div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="dash-card text-center"><div class="dash-card-value">{{ $stats['critiques'] }}</div><div class="dash-card-label">Critiques</div></div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="dash-card text-center"><div class="dash-card-value text-success">{{ $stats['termines'] }}</div><div class="dash-card-label">Résolus</div></div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="dash-card text-center"><div class="dash-card-value">{{ $stats['avg_score'] ?: '—' }}</div><div class="dash-card-label">Score IA moyen</div></div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="dash-card text-center"><div class="dash-card-value">{{ $stats['services'] }}</div><div class="dash-card-label">Services secours</div></div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="sea-card p-4">
                <h5 class="fw-semibold mb-4"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Innovations clés</h5>
                <ul class="list-unstyled">
                    <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i><strong>Analyse IA</strong> — scoring automatique 0-100 et dispatch intelligent</li>
                    <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i><strong>Géolocalisation GPS</strong> — routage vers la caserne la plus proche</li>
                    <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i><strong>Formulaire incendie</strong> — personnes piégées, fumée, type de bâtiment</li>
                    <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i><strong>Centre opérationnel</strong> — dispatch en temps réel des secours</li>
                    <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i><strong>Attestation PDF</strong> — preuve officielle pour le citoyen</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i><strong>Notifications</strong> — alertes citoyens et services compétents</li>
                </ul>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="sea-card p-4">
                <h5 class="fw-semibold mb-4"><i class="bi bi-fire text-danger me-2"></i>Derniers incendies signalés</h5>
                @forelse($recentIncendies as $sig)
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                        <div>
                            <strong>{{ $sig->reference }}</strong>
                            <small class="text-muted d-block">{{ Str::limit($sig->localisation, 40) }}</small>
                        </div>
                        <div class="text-end">
                            @if($sig->ai_score)<span class="badge bg-danger">{{ $sig->ai_score }}/100</span>@endif
                            <small class="text-muted d-block">{{ $sig->reported_at->format('d/m/Y') }}</small>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">Aucun incendie enregistré.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <a href="{{ route('report') }}" class="btn btn-primary btn-lg me-2"><i class="bi bi-fire me-2"></i>Déclarer un incendie</a>
        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg">Accès administration</a>
    </div>
</section>

@endsection
