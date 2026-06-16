@extends('layouts.app')

@section('title', 'Utilisateurs — Administration')
@section('page-title', 'Gestion des utilisateurs')
@section('page-subtitle', $users->total() . ' utilisateur(s)')

@section('content')

    <div class="filter-bar mb-4">
        <form method="GET" action="{{ route('admin.users.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold">Rechercher</label>
                    <input type="text" class="form-control" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Nom, email, téléphone...">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Rôle</label>
                    <select class="form-select" name="role">
                        <option value="all">Tous</option>
                        <option value="citoyen" @selected(($filters['role'] ?? '') === 'citoyen')>Citoyen</option>
                        <option value="admin" @selected(($filters['role'] ?? '') === 'admin')>Administrateur</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> Filtrer</button>
                </div>
            </div>
        </form>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="sea-card p-4 mb-4">
                <h5 class="fw-semibold mb-3">Créer un utilisateur</h5>
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="Nom" required>
                    </div>
                    <div class="mb-2">
                        <input type="email" name="email" class="form-control form-control-sm" placeholder="Email" required>
                    </div>
                    <div class="mb-2">
                        <input type="text" name="phone" class="form-control form-control-sm" placeholder="Téléphone">
                    </div>
                    <div class="mb-2">
                        <input type="password" name="password" class="form-control form-control-sm" placeholder="Mot de passe" required>
                    </div>
                    <div class="mb-2">
                        <select name="role" class="form-select form-select-sm">
                            <option value="citoyen">Citoyen</option>
                            <option value="admin">Administrateur</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Créer</button>
                </form>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="sea-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Rôle</th>
                        <th>Signalements</th>
                        <th>Inscription</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $u->toViewArray()['avatar'] }}" alt="" class="navbar-avatar">
                                    <span class="fw-semibold">{{ $u->name }}</span>
                                </div>
                            </td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->phone ?? '—' }}</td>
                            <td>
                                @if($u->isAdmin())
                                    <span class="badge bg-dark">Admin</span>
                                @else
                                    <span class="badge bg-secondary">Citoyen</span>
                                @endif
                            </td>
                            <td>{{ $u->signalements_count }}</td>
                            <td>{{ $u->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('admin.users.show', $u) }}" class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-5">Aucun utilisateur.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="p-3 border-top">{{ $users->links() }}</div>
        @endif
            </div>
        </div>
    </div>

@endsection
