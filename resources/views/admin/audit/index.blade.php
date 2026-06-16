@extends('layouts.app')

@section('title', 'Journal d\'audit — Administration')
@section('page-title', 'Journal d\'audit')
@section('page-subtitle', 'Historique des actions administrateur')

@section('content')

    <div class="filter-bar mb-4">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="q" class="form-control" value="{{ $filters['q'] ?? '' }}" placeholder="Rechercher une action...">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filtrer</button>
            </div>
        </form>
    </div>

    <div class="sea-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Admin</th>
                        <th>Action</th>
                        <th>Détails</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $log->user?->name ?? '—' }}</td>
                            <td><code>{{ $log->action }}</code></td>
                            <td class="small">{{ $log->metadata ? json_encode($log->metadata, JSON_UNESCAPED_UNICODE) : '—' }}</td>
                            <td>{{ $log->ip_address ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Aucune entrée.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="p-3 border-top">{{ $logs->links() }}</div>
        @endif
    </div>

@endsection
