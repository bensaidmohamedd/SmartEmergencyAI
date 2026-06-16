@extends('layouts.app')

@section('title', 'Signalements — Administration')
@section('page-title', 'Gestion des signalements')
@section('page-subtitle', $signalements->total() . ' signalement(s)')

@section('content')

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.signalements.export', request()->query()) }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-download me-1"></i> Exporter CSV
        </a>
    </div>

    <div class="filter-bar mb-4">
        <form method="GET" action="{{ route('admin.signalements.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Rechercher</label>
                    <input type="text" class="form-control" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Réf., lieu, citoyen...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Gravité</label>
                    <select class="form-select" name="gravite">
                        <option value="all">Toutes</option>
                        @foreach(['critique','elevee','moyenne','faible'] as $g)
                            <option value="{{ $g }}" @selected(($filters['gravite'] ?? 'all') === $g)>{{ ucfirst($g) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Statut</label>
                    <select class="form-select" name="statut">
                        <option value="all">Tous</option>
                        <option value="en_cours" @selected(($filters['statut'] ?? '') === 'en_cours')>En cours</option>
                        <option value="termine" @selected(($filters['statut'] ?? '') === 'termine')>Terminé</option>
                        <option value="annule" @selected(($filters['statut'] ?? '') === 'annule')>Annulé</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Catégorie</label>
                    <select class="form-select" name="category_id">
                        <option value="all">Toutes</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(($filters['category_id'] ?? '') == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Verdict IA</label>
                    <select class="form-select" name="ai_verdict">
                        <option value="all">Tous</option>
                        <option value="approved" @selected(($filters['ai_verdict'] ?? '') === 'approved')>Validé</option>
                        <option value="review" @selected(($filters['ai_verdict'] ?? '') === 'review')>À vérifier</option>
                        <option value="rejected" @selected(($filters['ai_verdict'] ?? '') === 'rejected')>Rejeté</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> Filtrer</button>
                </div>
            </div>
        </form>
    </div>

    <div class="sea-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Catégorie</th>
                        <th>Citoyen</th>
                        <th>Description</th>
                        <th>Gravité</th>
                        <th>IA</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($signalements as $sig)
                        <tr>
                            <td class="fw-semibold">{{ $sig->reference }}</td>
                            <td>{{ $sig->category->name }}</td>
                            <td>
                                <div>{{ $sig->user->name }}</div>
                                <small class="text-muted">{{ $sig->user->email }}</small>
                            </td>
                            <td>{{ Str::limit($sig->description, 50) }}</td>
                            <td>@include('partials.gravite-badge', ['gravite' => $sig->gravite])</td>
                            <td>
                                @if($sig->ai_priority_rank)
                                    <span class="badge bg-danger">{{ $sig->ai_priority_rank }}/100</span>
                                @endif
                                @if($sig->ai_verdict === 'review')
                                    <span class="badge bg-warning text-dark">À vérifier</span>
                                @elseif($sig->ai_verdict === 'rejected')
                                    <span class="badge bg-secondary">Rejeté</span>
                                @endif
                            </td>
                            <td>@include('partials.statut-badge', ['statut' => $sig->statut])</td>
                            <td>{{ $sig->reported_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.signalements.show', $sig->reference) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye me-1"></i> Gérer
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-5">Aucun signalement trouvé.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($signalements->hasPages())
            <div class="p-3 border-top">{{ $signalements->links() }}</div>
        @endif
    </div>

@endsection
