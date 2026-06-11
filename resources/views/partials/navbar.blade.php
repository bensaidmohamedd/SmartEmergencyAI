{{-- Navbar — variantes : guest, auth, app --}}

@if(($variant ?? 'guest') === 'guest')

    <nav class="navbar navbar-expand-lg sea-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <span class="brand-logo"><i class="bi bi-shield-exclamation"></i></span>
                <span class="brand-text">Smart Emergency AI</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#guestNav"
                    aria-controls="guestNav" aria-expanded="false" aria-label="Menu">
                <i class="bi bi-list fs-4"></i>
            </button>

            <div class="collapse navbar-collapse" id="guestNav">
                <ul class="navbar-nav mx-auto gap-lg-1">
                    <li class="nav-item">
                        <a class="nav-link" href="#fonctionnalites">
                            <i class="bi bi-grid d-lg-none me-2"></i>Fonctionnalités
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#comment-ca-marche">
                            <i class="bi bi-diagram-3 d-lg-none me-2"></i>Comment ça marche
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">
                            <i class="bi bi-envelope d-lg-none me-2"></i>Contact
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                    @include('partials.theme-toggle')

                    @guest
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Se connecter
                        </a>
                    @endguest

                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-grid me-1"></i> Mon espace
                        </a>
                    @endauth

                    <a href="{{ route('report') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Signaler
                    </a>
                </div>
            </div>
        </div>
    </nav>

@elseif($variant === 'auth')

    <nav class="sea-navbar-auth">
        <div class="container d-flex align-items-center justify-content-between py-3">
            <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="{{ route('home') }}">
                <span class="brand-logo"><i class="bi bi-shield-exclamation"></i></span>
                <span class="brand-text">Smart Emergency AI</span>
            </a>
            @include('partials.theme-toggle')
        </div>
    </nav>

@else

    <header class="app-navbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-link p-0 d-lg-none theme-icon-btn" id="sidebarToggle" aria-label="Menu">
                <i class="bi bi-list fs-4"></i>
            </button>
            <div>
                <h2 class="h6 mb-0 fw-semibold">@yield('page-title', 'Tableau de bord')</h2>
                <small class="text-muted app-navbar-subtitle">@yield('page-subtitle', '')</small>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            @include('partials.theme-toggle')
            <a href="{{ route('report') }}" class="btn btn-primary btn-sm d-none d-md-inline-flex">
                <i class="bi bi-plus-circle me-1"></i> Signaler
            </a>
            <div class="dropdown">
                <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2"
                        data-bs-toggle="dropdown">
                    <img src="{{ $user['avatar'] ?? '' }}" alt="" class="navbar-avatar">
                    <span class="d-none d-md-inline">{{ $user['name'] ?? 'Citoyen' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="bi bi-grid me-2"></i> Dashboard</a></li>
                    <li><a class="dropdown-item" href="{{ route('history') }}"><i class="bi bi-clock-history me-2"></i> Historique</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="bi bi-box-arrow-right me-2"></i> Déconnexion
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

@endif
