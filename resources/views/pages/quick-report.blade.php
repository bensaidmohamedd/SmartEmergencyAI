@extends('layouts.app')

@section('title', 'Urgence rapide — Smart Emergency AI')
@section('page-title', 'Signalement express')
@section('page-subtitle', 'Déclarez une urgence en quelques secondes')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="sea-card p-4 mb-4">
                <h5 class="fw-semibold mb-3"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Choisir le type d'urgence</h5>
                <div class="row g-2">
                    @foreach([
                        ['name' => 'Incendie', 'icon' => 'fire', 'color' => 'danger'],
                        ['name' => 'Accident', 'icon' => 'car-front-fill', 'color' => 'warning'],
                        ['name' => 'Urgence médicale', 'icon' => 'heart-pulse-fill', 'color' => 'danger'],
                        ['name' => 'Agression', 'icon' => 'shield-exclamation', 'color' => 'dark'],
                        ['name' => 'Inondation', 'icon' => 'droplet-fill', 'color' => 'info'],
                        ['name' => 'Coupure électrique', 'icon' => 'lightning-fill', 'color' => 'secondary'],
                    ] as $cat)
                        <div class="col-6 col-md-4">
                            <a href="{{ route('report', ['category' => $cat['name'], 'mode' => 'rapide']) }}"
                               class="quick-cat-btn btn btn-outline-{{ $cat['color'] }} w-100 py-3">
                                <i class="bi bi-{{ $cat['icon'] }} fs-4 d-block mb-1"></i>
                                {{ $cat['name'] }}
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="sea-card p-4">
                <form method="POST" action="{{ route('signalement.store') }}" enctype="multipart/form-data" id="quickReportForm">
                    @csrf
                    <input type="hidden" name="name" value="{{ $user['name'] }}">
                    <input type="hidden" name="phone" value="{{ $user['phone'] }}">

                    <div class="mb-3">
                        <label class="form-label fw-medium">Catégorie</label>
                        <select name="category" id="quickCategory" class="form-select @error('category') is-invalid @enderror" required>
                            <option value="" disabled {{ old('category', $prefillCategory) ? '' : 'selected' }}>Choisir...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" @selected(old('category', $prefillCategory) === $cat)>{{ $cat }}</option>
                            @endforeach
                        </select>
                        @error('category')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium"><i class="bi bi-geo-alt-fill text-primary me-1"></i> Position GPS</label>
                        <div class="geo-card" id="geoCard">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div id="geoStatus" class="small text-muted">Cliquez pour obtenir votre position</div>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="geoBtn">
                                    <i class="bi bi-crosshair me-1"></i> GPS
                                </button>
                            </div>
                            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                            <input type="text" name="localisation" id="localisation" class="form-control" readonly
                                   placeholder="Adresse automatique..." value="{{ old('localisation') }}">
                            @error('latitude')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Description rapide</label>
                        <textarea name="description" id="reportDescription" class="form-control @error('description') is-invalid @enderror"
                                  rows="3" placeholder="Ex. : gros incendie, fumée visible, personnes bloquées..." required>{{ old('description') }}</textarea>
                        @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 d-none mb-3" id="aiPreviewPanel">
                        @include('partials.ai-preview-panel')
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Photo (optionnel)</label>
                        <input type="file" name="photo" id="reportPhoto" class="form-control" accept="image/*">
                    </div>

                    <button type="submit" class="btn btn-danger btn-lg w-100" id="reportSubmitBtn">
                        <i class="bi bi-broadcast me-2"></i> Envoyer l'alerte
                    </button>
                    <div class="text-center mt-2">
                        <a href="{{ route('report') }}" class="small text-muted">Formulaire complet →</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    window.SEA_ANALYZE_URL = @json(route('signalement.analyze'));
    window.SEA_CSRF = @json(csrf_token());
</script>
@endpush
