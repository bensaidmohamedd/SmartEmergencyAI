@extends('layouts.app')

@section('title', 'Inscription — Smart Emergency AI')

@section('content')

    <div class="auth-card auth-card-wide">
        <div class="text-center mb-4">
            <span class="brand-logo brand-logo-lg mx-auto mb-3">
                <i class="bi bi-shield-exclamation"></i>
            </span>
            <h2 class="fw-bold">Créer un compte</h2>
            <p class="text-muted">Rejoignez Smart Emergency AI</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="row g-3">
                <div class="col-12">
                    <label for="name" class="form-label fw-medium">Nom complet</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="name" name="name" placeholder="Ben Saïd"
                           value="{{ old('name') }}" required>
                    @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label fw-medium">Téléphone</label>
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                           id="phone" name="phone" placeholder="+227 87 14 51 44"
                           value="{{ old('phone') }}" required>
                    @error('phone')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label fw-medium">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                           id="email" name="email" placeholder="votre@email.ne"
                           value="{{ old('email') }}" required>
                    @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="password" class="form-label fw-medium">Mot de passe</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           id="password" name="password" placeholder="••••••••" required>
                    @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label fw-medium">Confirmation mot de passe</label>
                    <input type="password" class="form-control" id="password_confirmation"
                           name="password_confirmation" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 btn-lg mt-4 mb-3">
                <i class="bi bi-person-plus me-2"></i> Créer mon compte
            </button>

            <p class="text-center text-muted mb-0">
                Déjà inscrit ?
                <a href="{{ route('login') }}" class="fw-semibold">Se connecter</a>
            </p>
        </form>
    </div>

@endsection
