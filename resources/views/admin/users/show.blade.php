@extends('layouts.app')

@section('title', $profile->name . ' — Administration')
@section('page-title', $profile->name)
@section('page-subtitle', $profile->email)

@section('content')

    <div class="mb-3">
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i> Retour aux utilisateurs
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="sea-card p-4">
                <div class="text-center mb-4">
                    <img src="{{ $profile->toViewArray()['avatar'] }}" alt="" class="welcome-avatar mx-auto mb-3">
                    <h5 class="fw-bold mb-1">{{ $profile->name }}</h5>
                    <p class="text-muted small mb-0">Inscrit le {{ $profile->created_at->format('d/m/Y') }}</p>
                </div>

                <form action="{{ route('admin.users.update', $profile) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nom</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $profile->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $profile->email) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Téléphone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $profile->phone) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Rôle</label>
                        <select name="role" class="form-select">
                            <option value="citoyen" @selected(old('role', $profile->role) === 'citoyen')>Citoyen</option>
                            <option value="admin" @selected(old('role', $profile->role) === 'admin')>Administrateur</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nouveau mot de passe</label>
                        <input type="password" name="password" class="form-control" placeholder="Laisser vide pour ne pas changer">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-check-lg me-1"></i> Enregistrer
                    </button>
                </form>

                @if($profile->id !== auth()->id() && !$profile->signalements()->exists())
                    <form action="{{ route('admin.users.destroy', $profile) }}" method="POST"
                          onsubmit="return confirm('Supprimer cet utilisateur ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-trash me-1"></i> Supprimer
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="col-lg-8">
            <div class="sea-card p-4">
                <h5 class="fw-semibold mb-3">Signalements ({{ count($signalements) }})</h5>
                @forelse($signalements as $sig)
                    <a href="{{ route('admin.signalements.show', $sig['id']) }}" class="recent-item text-decoration-none d-block">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold">{{ $sig['categorie'] }} — {{ $sig['id'] }}</div>
                                <small class="text-muted">{{ $sig['date'] }} · {{ Str::limit($sig['localisation'], 40) }}</small>
                            </div>
                            <div class="d-flex gap-1">
                                @include('partials.gravite-badge', ['gravite' => $sig['gravite']])
                                @include('partials.statut-badge', ['statut' => $sig['statut']])
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="text-muted mb-0">Aucun signalement pour cet utilisateur.</p>
                @endforelse
            </div>
        </div>
    </div>

@endsection
