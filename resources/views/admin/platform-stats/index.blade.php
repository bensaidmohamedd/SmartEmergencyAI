@extends('layouts.app')

@section('title', 'Statistiques publiques — Administration')
@section('page-title', 'Statistiques de la page d\'accueil')
@section('page-subtitle', 'Modifiez les chiffres affichés sur le site public')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="sea-card p-4">
                <form action="{{ route('admin.platform-stats.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    @foreach($stats as $index => $stat)
                        <input type="hidden" name="stats[{{ $index }}][id]" value="{{ $stat->id }}">
                        <div class="row g-3 mb-4 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="col-12">
                                <small class="text-muted text-uppercase fw-semibold">Clé : {{ $stat->key }}</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Libellé affiché</label>
                                <input type="text" name="stats[{{ $index }}][label]" class="form-control"
                                       value="{{ old('stats.'.$index.'.label', $stat->label) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Valeur</label>
                                <input type="text" name="stats[{{ $index }}][value]" class="form-control"
                                       value="{{ old('stats.'.$index.'.value', $stat->value) }}" required>
                            </div>
                        </div>
                    @endforeach

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Enregistrer les statistiques
                    </button>
                </form>
                <form action="{{ route('admin.platform-stats.sync') }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-repeat me-1"></i> Recalculer depuis les données réelles
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection
