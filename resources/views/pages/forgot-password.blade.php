@extends('layouts.app')

@section('title', 'Mot de passe oublié — Smart Emergency AI')

@section('content')
    <div class="auth-card">
        <div class="text-center mb-4">
            <span class="brand-logo brand-logo-lg mx-auto mb-3"><i class="bi bi-key"></i></span>
            <h2 class="fw-bold">Mot de passe oublié</h2>
            <p class="text-muted">Entrez votre email pour recevoir un lien de réinitialisation</p>
        </div>
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-4">
                <label for="email" class="form-label fw-medium">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                       id="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-lg mb-3">Envoyer le lien</button>
            <p class="text-center mb-0"><a href="{{ route('login') }}">Retour à la connexion</a></p>
        </form>
    </div>
@endsection
