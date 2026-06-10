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

        <form id="registerForm" action="{{ route('dashboard') }}">
            <div class="row g-3">
                <div class="col-12">
                    <label for="fullname" class="form-label fw-medium">Nom complet</label>
                    <input type="text" class="form-control" id="fullname" placeholder="Ben Saïd" required>
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label fw-medium">Téléphone</label>
                    <input type="tel" class="form-control" id="phone" placeholder="+227 87 14 51 44" required>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label fw-medium">Email</label>
                    <input type="email" class="form-control" id="email" placeholder="votre@email.ne" required>
                </div>
                <div class="col-md-6">
                    <label for="password" class="form-label fw-medium">Mot de passe</label>
                    <input type="password" class="form-control" id="password" placeholder="••••••••" required>
                </div>
                <div class="col-md-6">
                    <label for="passwordConfirm" class="form-label fw-medium">Confirmation mot de passe</label>
                    <input type="password" class="form-control" id="passwordConfirm" placeholder="••••••••" required>
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
