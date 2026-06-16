@extends('layouts.app')

@section('title', 'Connexion — Smart Emergency AI')

@section('content')

    <div class="auth-card">
        <div class="text-center mb-4">
            <span class="brand-logo brand-logo-lg mx-auto mb-3">
                <i class="bi bi-shield-exclamation"></i>
            </span>
            <h2 class="fw-bold">Connexion</h2>
            <p class="text-muted">Accédez à votre espace citoyen</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label fw-medium">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                           id="email" name="email" placeholder="votre@email.ne"
                           value="{{ old('email', 'ben.said@email.ne') }}" required autofocus>
                </div>
                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-medium">Mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           id="password" name="password" required>
                </div>
            </div>

            <div class="mb-3 d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Se souvenir de moi</label>
                </div>
                <a href="{{ route('password.request') }}" class="small">Mot de passe oublié ?</a>
            </div>

            <button type="submit" class="btn btn-primary w-100 btn-lg mb-3">
                <i class="bi bi-box-arrow-in-right me-2"></i> Connexion
            </button>

            <p class="text-center text-muted mb-0">
                Pas encore de compte ?
                <a href="{{ route('register') }}" class="fw-semibold">Créer un compte</a>
            </p>
        </form>
    </div>

@endsection
