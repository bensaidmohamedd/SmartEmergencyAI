@extends('layouts.app')

@section('title', 'Smart Emergency AI — Signalez une urgence')

@section('content')

    {{-- ================================================================
         HERO
         ================================================================ --}}
    <section class="landing-hero">
        <div class="hero-bg-shape hero-bg-shape-1"></div>
        <div class="hero-bg-shape hero-bg-shape-2"></div>

        <div class="container position-relative">
            <div class="row align-items-center min-vh-75 py-5">
                <div class="col-lg-7 text-center text-lg-start">
                    <div class="hero-badge mb-4">
                        <span>Plateforme nigérienne propulsée par l'IA</span>
                    </div>

                    <div class="d-flex align-items-center gap-3 mb-4 justify-content-center justify-content-lg-start">
                        <span class="brand-logo brand-logo-xl">
                            <i class="bi bi-shield-exclamation"></i>
                        </span>
                        <div class="text-start">
                            <h1 class="display-5 fw-bold mb-0">Smart Emergency AI</h1>
                        </div>
                    </div>

                    <p class="hero-slogan mb-4">
                        Signalez une urgence en quelques secondes.
                    </p>

                    <p class="hero-description text-muted mb-4 pe-lg-4">
                        Une plateforme nigérienne moderne qui connecte les citoyens aux services de secours
                        à Niamey et dans tout le Niger, grâce à l'IA et la géolocalisation en temps réel.
                    </p>

                    <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                        <a href="{{ route('report') }}" class="btn btn-primary btn-lg px-4 shadow-sm">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Signaler une urgence
                        </a>
                    </div>

                    <div class="hero-trust mt-5 d-flex flex-wrap gap-4 justify-content-center justify-content-lg-start">
                        <div class="trust-item">
                            <i class="bi bi-shield-check"></i>
                            <span>Fiabilité</span>
                        </div>
                        <div class="trust-item">
                            <i class="bi bi-arrow-repeat"></i>
                            <span>Efficacité</span>
                        </div>
                        <div class="trust-item">
                            <i class="bi bi-clock-fill"></i>
                            <span>Réponse &lt; 3 sec</span>
                        </div>
                    </div>
                </div>

                {{-- Panneau statistiques --}}
                <div class="col-lg-5 mt-5 mt-lg-0">
                    <div class="hero-stats-card">
                        <div class="hero-stats-header">
                            <i class="bi bi-graph-up-arrow"></i>
                            <span>Impact de la plateforme</span>
                        </div>
                        <div class="hero-stat-main">
                            <div class="hero-stat-icon">
                                <i class="bi bi-heart-pulse-fill"></i>
                            </div>
                            <div class="hero-stat-number">120</div>
                            <div class="hero-stat-label">Urgences traitées</div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-6">
                                <div class="hero-stat-sub">
                                    <i class="bi bi-patch-check-fill text-success"></i>
                                    <div>
                                        <strong>95%</strong>
                                        <small>Taux de succès</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="hero-stat-sub">
                                    <i class="bi bi-stopwatch-fill text-primary"></i>
                                    <div>
                                        <strong>12 min</strong>
                                        <small>Temps moyen</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ================================================================
         FONCTIONNALITÉS
         ================================================================ --}}
    <section class="landing-section" id="fonctionnalites">
        <div class="container">
            <div class="section-header text-center">
                <span class="section-tag">
                    <i class="bi bi-grid-fill"></i> Fonctionnalités
                </span>
                <h2 class="section-title">Tout pour agir vite et bien</h2>
                <p class="section-subtitle mx-auto">
                    Des outils pensés pour les citoyens nigériens : simples, rapides et fiables
                    pour faire face à toute urgence au Niger.
                </p>
            </div>

            <div class="row g-4">
                @foreach($features as $feature)
                    <div class="col-md-6 col-lg-4">
                        <div class="feature-card-v2">
                            <div class="feature-icon-box">
                                <i class="bi bi-{{ $feature['icon'] }}"></i>
                            </div>
                            <h5 class="fw-bold mb-2">{{ $feature['title'] }}</h5>
                            <p class="text-muted mb-0">{{ $feature['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>


    {{-- ================================================================
         COMMENT ÇA MARCHE
         ================================================================ --}}
    <section class="landing-section landing-section-alt" id="comment-ca-marche">
        <div class="container">
            <div class="section-header text-center">
                <span class="section-tag">
                    <i class="bi bi-diagram-3-fill"></i> Processus
                </span>
                <h2 class="section-title">Comment ça marche ?</h2>
                <p class="section-subtitle mx-auto">
                    Quatre étapes simples, de votre signalement à la résolution de l'urgence.
                </p>
            </div>

            <div class="steps-timeline">
                <div class="row g-4">
                    @foreach($steps as $index => $step)
                        <div class="col-md-6 col-lg-3">
                            <div class="step-card-v2">
                                @if($index < count($steps) - 1)
                                    <div class="step-connector d-none d-lg-block"></div>
                                @endif
                                <div class="step-number-badge">{{ $step['number'] }}</div>
                                <div class="step-icon-circle">
                                    <i class="bi bi-{{ $step['icon'] }}"></i>
                                </div>
                                <h5 class="fw-bold mb-2">{{ $step['title'] }}</h5>
                                <p class="text-muted small mb-0">{{ $step['description'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="text-center mt-5">
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-person-plus-fill me-2"></i>
                    Signalez une urgence
                </a>
            </div>
        </div>
    </section>


    {{-- ================================================================
        CTA FINAL
        ================================================================ --}}
    <section class="landing-cta">
        <div class="container">
            <div class="cta-card">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8 text-center text-lg-start">
                        <h2 class="fw-bold mb-2">Une urgence ? Chaque seconde compte.</h2>
                        <p class="mb-0 opacity-75">
                            Ne perdez pas de temps. Signalez maintenant et laissez l'IA coordonner les secours.
                        </p>
                    </div>
                    <div class="col-lg-4 text-center text-lg-end">
                        <a href="{{ route('report') }}" class="btn btn-light btn-lg px-4 fw-semibold text-black">
                            <i class="bi bi-lightning-charge-fill me-2 text-primary"></i>
                            Signaler maintenant
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
