@extends('layouts.app')

@section('title', 'Réinitialiser le mot de passe — Smart Emergency AI')

@section('content')
    <div class="auth-card">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Nouveau mot de passe</h2>
        </div>
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="mb-3">
                <label class="form-label fw-medium">Email</label>
                <input type="email" class="form-control" name="email" value="{{ old('email', $email) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Nouveau mot de passe</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-medium">Confirmer</label>
                <input type="password" class="form-control" name="password_confirmation" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-lg">Réinitialiser</button>
        </form>
    </div>
@endsection
