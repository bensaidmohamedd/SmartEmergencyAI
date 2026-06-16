@extends('layouts.app')

@section('title', 'Centre opérationnel — Administration')
@section('page-title', 'Centre opérationnel')
@section('page-subtitle', 'Dispatch en temps réel — ' . $critical->count() . ' urgence(s) critique(s)')

@section('content')

<div class="ops-alert-bar mb-4 {{ $critical->count() > 0 ? 'ops-alert-active' : '' }}">
    <div class="d-flex align-items-center gap-3">
        <i class="bi bi-broadcast fs-4"></i>
        <div>
            <strong>Statut opérationnel</strong>
            <span class="ms-2">{{ $stats['en_cours'] }} intervention(s) active(s) — {{ $stats['urgences_actives'] }} critique(s)</span>
        </div>
        <span class="ms-auto small" id="opsClock"></span>
    </div>
</div>

@if($critical->count() > 0)
<div class="sea-card p-0 overflow-hidden mb-4 border-danger">
    <div class="p-3 bg-danger text-white fw-semibold"><i class="bi bi-exclamation-octagon-fill me-2"></i>File prioritaire — Urgences critiques</div>
    <div class="table-responsive">
        <table class="table admin-table mb-0">
            <thead><tr><th>Réf.</th><th>Priorité IA</th><th>Catégorie</th><th>Citoyen</th><th>Lieu</th><th>Unité</th><th>Actions</th></tr></thead>
            <tbody>
                @foreach($critical as $sig)
                    <tr class="table-danger-subtle">
                        <td class="fw-bold">{{ $sig->reference }}</td>
                        <td>
                            <span class="badge bg-danger">{{ $sig->ai_priority_rank ?? $sig->ai_score ?? '—' }}/100</span>
                            @if($sig->ai_verdict === 'review')<span class="badge bg-warning text-dark ms-1">À vérifier</span>@endif
                        </td>
                        <td>{{ $sig->category->name }}</td>
                        <td>{{ $sig->user->name }}</td>
                        <td>{{ Str::limit($sig->localisation, 30) }}</td>
                        <td>{{ $sig->assignedService?->name ?? '—' }}</td>
                        <td>
                            <a href="{{ route('admin.signalements.show', $sig->reference) }}" class="btn btn-sm btn-danger">Gérer</a>
                            <form action="{{ route('admin.signalements.auto-dispatch', $sig->reference) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-lightning"></i> Auto</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="sea-card p-0 overflow-hidden">
            <div class="p-3 border-bottom fw-semibold">Interventions actives</div>
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead><tr><th>Réf.</th><th>Priorité</th><th>Catégorie</th><th>ETA</th><th>Unité assignée</th><th></th></tr></thead>
                    <tbody>
                        @forelse($active as $sig)
                            <tr>
                                <td>{{ $sig->reference }}</td>
                                <td>
                                    @if($sig->ai_priority_rank || $sig->ai_score)
                                        <span class="badge bg-secondary">{{ $sig->priorityLabel() }}</span>
                                        <small class="text-muted">({{ $sig->ai_priority_rank ?? $sig->ai_score }})</small>
                                    @endif
                                </td>
                                <td>{{ $sig->category->name }}</td>
                                <td>{{ $sig->estimated_response_min ? $sig->estimated_response_min.' min' : '—' }}</td>
                                <td>{{ $sig->assignedService?->name ?? 'Non assigné' }}</td>
                                <td><a href="{{ route('admin.signalements.show', $sig->reference) }}" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Aucune intervention active.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="sea-card p-4">
            <h6 class="fw-semibold mb-3">Capacité des services</h6>
            @foreach($services as $svc)
                <div class="d-flex justify-content-between mb-2">
                    <span class="small">{{ $svc->name }}</span>
                    <span class="badge {{ $svc->signalements_count > 0 ? 'bg-warning' : 'bg-success' }}">{{ $svc->signalements_count }} mission(s)</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
setInterval(function(){ var el=document.getElementById('opsClock'); if(el) el.textContent=new Date().toLocaleTimeString('fr-FR'); }, 1000);
setTimeout(function(){ location.reload(); }, 60000);
</script>
@endpush
