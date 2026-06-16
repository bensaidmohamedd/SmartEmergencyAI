@extends('layouts.app')

@section('title', 'Signaler une urgence — Smart Emergency AI')
@section('page-title', 'Signaler une urgence')
@section('page-subtitle', 'Décrivez la situation le plus précisément possible')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="sea-card p-4">
                <form method="POST" action="{{ route('signalement.store') }}" enctype="multipart/form-data" id="reportForm">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="reportName" class="form-label fw-medium">Nom</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="reportName" name="name"
                                   value="{{ old('name', $user['name']) }}" required>
                            @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="reportPhone" class="form-label fw-medium">Téléphone</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                   id="reportPhone" name="phone"
                                   value="{{ old('phone', $user['phone']) }}" required>
                            @error('phone')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label for="reportCategory" class="form-label fw-medium">Catégorie</label>
                            <select class="form-select @error('category') is-invalid @enderror"
                                    id="reportCategory" name="category" required>
                                <option value="" disabled {{ old('category') ? '' : 'selected' }}>Choisir une catégorie...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ old('category', $prefillCategory ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                            @error('category')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        {{-- Champs spécifiques INCENDIE --}}
                        <div class="col-12 d-none" id="fireFields">
                            <div class="fire-fields-card p-3 rounded-3">
                                <h6 class="fw-semibold mb-3"><i class="bi bi-fire text-danger me-2"></i>Détails incendie</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">Personnes piégées ?</label>
                                        <select name="fire_people_trapped" class="form-select" id="firePeopleTrapped">
                                            <option value="0">Non / Inconnu</option>
                                            <option value="1">Oui — personnes bloquées</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">Niveau de fumée</label>
                                        <select name="fire_smoke_level" class="form-select" id="fireSmokeLevel">
                                            <option value="">Non précisé</option>
                                            <option value="faible">Faible</option>
                                            <option value="modere">Modéré</option>
                                            <option value="dense">Dense / Visibilité nulle</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">Type de bâtiment</label>
                                        <select name="fire_building_type" class="form-select" id="fireBuildingType">
                                            <option value="">Non précisé</option>
                                            <option value="habitation">Habitation</option>
                                            <option value="commerce">Commerce / Marché</option>
                                            <option value="ecole">École</option>
                                            <option value="hopital">Hôpital</option>
                                            <option value="industrie">Industrie / Entrepôt</option>
                                            <option value="autre">Autre</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Géolocalisation --}}
                        <div class="col-12">
                            <label class="form-label fw-medium">
                                <i class="bi bi-geo-alt-fill text-primary me-1"></i> Localisation GPS
                            </label>
                            <div class="geo-card" id="geoCard">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                    <div id="geoStatus" class="geo-status text-muted small">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Cliquez pour obtenir votre position exacte
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="geoBtn">
                                        <i class="bi bi-crosshair me-1"></i> Obtenir ma position
                                    </button>
                                </div>
                                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                                <input type="text" class="form-control @error('localisation') is-invalid @enderror"
                                       id="localisation" name="localisation"
                                       placeholder="Adresse détectée automatiquement..."
                                       value="{{ old('localisation') }}" readonly>
                                <div id="geoCoords" class="text-muted small mt-2 d-none"></div>
                                <div id="geoMap" class="geo-map mt-3 d-none"></div>
                                @error('latitude')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                @error('longitude')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="reportDescription" class="form-label fw-medium">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="reportDescription" name="description" rows="4"
                                      placeholder="Décrivez la situation (feu, fumée, personnes bloquées...)" required>{{ old('description') }}</textarea>
                            @error('description')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        {{-- Analyse IA en temps réel --}}
                        <div class="col-12 d-none" id="aiPreviewPanel">
                            @include('partials.ai-preview-panel')
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Photo</label>
                            <div class="upload-zone" id="photoUploadZone">
                                <input type="file" class="d-none @error('photo') is-invalid @enderror"
                                       id="reportPhoto" name="photo" accept="image/*">
                                <label for="reportPhoto" class="upload-label mb-0">
                                    <i class="bi bi-camera-fill fs-3 text-primary"></i>
                                    <span class="small text-muted">Cliquez pour ajouter une photo</span>
                                </label>
                                <div class="upload-preview d-none" id="photoPreview"></div>
                            </div>
                            @error('photo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Vidéo</label>
                            <div class="upload-zone" id="videoUploadZone">
                                <input type="file" class="d-none @error('video') is-invalid @enderror"
                                       id="reportVideo" name="video" accept="video/*">
                                <label for="reportVideo" class="upload-label mb-0">
                                    <i class="bi bi-camera-video-fill fs-3 text-primary"></i>
                                    <span class="small text-muted">Cliquez pour ajouter une vidéo</span>
                                </label>
                                <div class="upload-preview d-none" id="videoPreview"></div>
                            </div>
                            @error('video')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 mt-4" id="reportSubmitBtn">
                        <i class="bi bi-send-fill me-2"></i> Signaler l'urgence
                    </button>
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
