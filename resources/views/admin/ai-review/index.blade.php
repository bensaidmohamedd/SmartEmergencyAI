@extends('layouts.app')

@section('title', 'File de vérification IA — Administration')
@section('page-title', 'Vérification IA')
@section('page-subtitle', $pendingCount . ' signalement(s) en attente de validation')

@section('content')

    <div class="ops-alert-bar mb-4 {{ $pendingCount > 0 ? 'ops-alert-active' : '' }}">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-robot fs-4"></i>
            <div>
                <strong>File de vérification</strong>
                <span class="ms-2 text-muted">Signalements suspects détectés par l'IA — approbation ou rejet manuel requis.</span>
            </div>
            @if($pendingCount > 0)
                <span class="badge bg-warning text-dark ms-auto">{{ $pendingCount }} en attente</span>
            @endif
        </div>
    </div>

    <div class="filter-bar mb-4">
        <form method="GET" action="{{ route('admin.ai-review.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control" value="{{ $filters['q'] ?? '' }}" placeholder="Rechercher...">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i> Filtrer</button>
            </div>
        </form>
    </div>

    <div class="sea-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th>Réf.</th>
                        <th>Citoyen</th>
                        <th>Catégorie</th>
                        <th>Description</th>
                        <th>Crédibilité</th>
                        <th>Priorité</th>
                        <th>Alertes IA</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($signalements as $sig)
                        <tr>
                            <td class="fw-semibold">{{ $sig->reference }}</td>
                            <td>
                                <div>{{ $sig->user->name }}</div>
                                <small class="text-muted">{{ $sig->user->phone }}</small>
                            </td>
                            <td>{{ $sig->category->name }}</td>
                            <td>{{ Str::limit($sig->description, 60) }}</td>
                            <td><span class="badge bg-info">{{ $sig->ai_credibility_score ?? '—' }}/100</span></td>
                            <td><span class="badge bg-danger">{{ $sig->ai_priority_rank ?? '—' }}/100</span></td>
                            <td class="small">
                                @if($sig->ai_rejection_reasons)
                                    @foreach(array_slice($sig->ai_rejection_reasons, 0, 2) as $reason)
                                        <span class="d-block text-warning">• {{ $reason }}</span>
                                    @endforeach
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <a href="{{ route('admin.signalements.show', $sig->reference) }}" class="btn btn-sm btn-light">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.ai-review.approve', $sig->reference) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Approuver">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.ai-review.reject', $sig->reference) }}" method="POST"
                                          onsubmit="return confirm('Rejeter ce signalement ?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger" title="Rejeter">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-5">Aucun signalement en attente de vérification.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($signalements->hasPages())
            <div class="p-3 border-top">{{ $signalements->links() }}</div>
        @endif
    </div>

@endsection
