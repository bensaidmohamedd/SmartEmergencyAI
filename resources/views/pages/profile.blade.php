@extends('layouts.app')

@section('title', 'Mon profil — Smart Emergency AI')
@section('page-title', 'Mon profil')
@section('page-subtitle', 'Gérez vos informations personnelles')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="sea-card p-4">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="text-center mb-4">
                        <img src="{{ $user['avatar'] }}" alt="" class="welcome-avatar mb-3">
                        <h5 class="fw-bold">{{ $profile->name }}</h5>
                        <p class="text-muted small">Membre depuis {{ $profile->created_at->format('d/m/Y') }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nom complet</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $profile->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $profile->email) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Téléphone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $profile->phone) }}">
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-semibold mb-3">Changer le mot de passe</h6>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mot de passe actuel</label>
                        <input type="password" name="current_password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nouveau mot de passe</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Enregistrer
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection
