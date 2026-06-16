@extends('layouts.app')

@section('title', 'Carte des urgences — Administration')
@section('page-title', 'Carte des urgences actives')
@section('page-subtitle', $signalements->count() . ' signalement(s) géolocalisé(s)')

@section('content')

    <div class="sea-card p-0 overflow-hidden mb-4">
        <div id="adminMap" class="admin-map"></div>
    </div>

    <div class="row g-3">
        @foreach($signalements as $sig)
            <div class="col-md-6 col-lg-4">
                <div class="sea-card p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <strong>{{ $sig->reference }}</strong>
                        @include('partials.gravite-badge', ['gravite' => $sig->gravite])
                    </div>
                    <div class="small text-muted mb-2">{{ $sig->category->name }} — {{ $sig->user->name }}</div>
                    <div class="small mb-2">{{ Str::limit($sig->localisation, 50) }}</div>
                    <a href="{{ route('admin.signalements.show', $sig->reference) }}" class="btn btn-sm btn-primary">Gérer</a>
                </div>
            </div>
        @endforeach
    </div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>.admin-map { height: 500px; width: 100%; }</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var map = L.map('adminMap').setView([{{ $mapCenter['lat'] }}, {{ $mapCenter['lng'] }}], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);
    var markers = @json($markers);
    markers.forEach(function (m) {
        L.marker([m.lat, m.lng]).addTo(map)
            .bindPopup('<strong>' + m.ref + '</strong><br>' + m.cat + '<br><a href="' + m.url + '">Voir</a>');
    });
    if (markers.length > 1) {
        var group = L.featureGroup(markers.map(function (m) { return L.marker([m.lat, m.lng]); }));
        map.fitBounds(group.getBounds().pad(0.1));
    }
});
</script>
@endpush
