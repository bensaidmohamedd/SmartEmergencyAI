{{-- Sidebar navigation admin --}}
<aside class="app-sidebar admin-sidebar" id="appSidebar">
    <div class="sidebar-brand d-flex align-items-center justify-content-between">
        <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
            <span class="brand-logo admin-brand-logo"><i class="bi bi-shield-check"></i></span>
            <div>
                <div class="brand-text fw-bold" style="font-size:0.9rem;">Smart Emergency</div>
                <small class="text-muted" style="font-size:0.7rem;">Espace administrateur</small>
            </div>
        </a>
        <button class="btn btn-link p-0 d-lg-none theme-icon-btn" id="sidebarClose" aria-label="Fermer">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <nav class="sidebar-nav py-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Tableau de bord
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.operations*') ? 'active' : '' }}" href="{{ route('admin.operations') }}">
                    <i class="bi bi-broadcast"></i> Centre opérationnel
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.ai-review.*') ? 'active' : '' }}" href="{{ route('admin.ai-review.index') }}">
                    <i class="bi bi-robot"></i> Vérification IA
                    @php $pendingAi = \App\Models\Signalement::where('ai_verdict', 'review')->where('statut', 'en_cours')->count(); @endphp
                    @if($pendingAi > 0)<span class="badge bg-warning text-dark ms-1">{{ $pendingAi }}</span>@endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.emergency-services.*') ? 'active' : '' }}" href="{{ route('admin.emergency-services.index') }}">
                    <i class="bi bi-building"></i> Services de secours
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.signalements.*') ? 'active' : '' }}" href="{{ route('admin.signalements.index') }}">
                    <i class="bi bi-exclamation-triangle-fill"></i> Signalements
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people-fill"></i> Utilisateurs
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                    <i class="bi bi-tags-fill"></i> Catégories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.platform-stats.*') ? 'active' : '' }}" href="{{ route('admin.platform-stats.index') }}">
                    <i class="bi bi-bar-chart-fill"></i> Statistiques publiques
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.map') ? 'active' : '' }}" href="{{ route('admin.map') }}">
                    <i class="bi bi-map-fill"></i> Carte des urgences
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}" href="{{ route('admin.audit.index') }}">
                    <i class="bi bi-journal-text"></i> Journal d'audit
                </a>
            </li>
        </ul>

        <div class="px-3 mt-4">
            <a href="{{ route('admin.signalements.index', ['statut' => 'en_cours', 'gravite' => 'critique']) }}"
               class="btn btn-admin-accent w-100">
                <i class="bi bi-lightning-charge-fill me-1"></i> Urgences critiques
            </a>
        </div>
    </nav>

    <div class="sidebar-footer px-3 py-3">
        <a href="{{ route('home') }}" class="nav-link small">
            <i class="bi bi-house me-2"></i> Site public
        </a>
        <a href="{{ route('dashboard') }}" class="nav-link small">
            <i class="bi bi-person me-2"></i> Espace citoyen
        </a>
    </div>
</aside>
