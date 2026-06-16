{{-- Sidebar navigation citoyen --}}
<aside class="app-sidebar" id="appSidebar">
    <div class="sidebar-brand d-flex align-items-center justify-content-between">
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
            <span class="brand-logo"><i class="bi bi-shield-exclamation"></i></span>
            <div>
                <div class="brand-text fw-bold" style="font-size:0.9rem;">Smart Emergency</div>
                <small class="text-muted" style="font-size:0.7rem;">Espace citoyen</small>
            </div>
        </a>
        <button class="btn btn-link p-0 d-lg-none theme-icon-btn" id="sidebarClose" aria-label="Fermer">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <nav class="sidebar-nav py-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-grid-fill"></i> Tableau de bord
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('report*') ? 'active' : '' }}" href="{{ route('report') }}">
                    <i class="bi bi-exclamation-triangle-fill"></i> Signaler une urgence
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('report.quick') ? 'active' : '' }}" href="{{ route('report.quick') }}">
                    <i class="bi bi-lightning-charge-fill"></i> Urgence rapide
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">
                    <i class="bi bi-telephone-fill"></i> Annuaire secours
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('history') || request()->routeIs('signalement.show') ? 'active' : '' }}" href="{{ route('history') }}">
                    <i class="bi bi-clock-history"></i> Historique
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
                    <i class="bi bi-bell-fill"></i> Notifications
                    @php $unread = auth()->user()->unreadNotifications->count(); @endphp
                    @if($unread > 0)<span class="badge bg-danger ms-1">{{ $unread }}</span>@endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}" href="{{ route('profile') }}">
                    <i class="bi bi-person-fill"></i> Mon profil
                </a>
            </li>
        </ul>

        <div class="px-3 mt-4">
            <a href="{{ route('report.quick') }}" class="btn btn-danger w-100">
                <i class="bi bi-lightning-charge-fill me-1"></i> Urgence rapide
            </a>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-admin-accent w-100 mt-2">
                        <i class="bi bi-shield-check me-1"></i> Administration
                    </a>
                @endif
            @endauth
        </div>
    </nav>

    <div class="sidebar-footer px-3 py-3">
        <a href="{{ route('home') }}" class="nav-link small">
            <i class="bi bi-arrow-left me-2"></i> Retour à l'accueil
        </a>
    </div>
</aside>
