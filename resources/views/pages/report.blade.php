@extends('layouts.app')

@section('title', 'Signaler une urgence — Smart Emergency AI')
@section('page-title', 'Signaler une urgence')
@section('page-subtitle', 'Décrivez la situation le plus précisément possible')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="sea-card p-4">
                <form id="reportForm">
                    <div class="row g-3">
                        {{-- Nom --}}
                        <div class="col-md-6">
                            <label for="reportName" class="form-label fw-medium">Nom</label>
                            <input type="text" class="form-control" id="reportName"
                                value="{{ $user['name'] }}" required>
                        </div>

                        {{-- Téléphone --}}
                        <div class="col-md-6">
                            <label for="reportPhone" class="form-label fw-medium">Téléphone</label>
                            <input type="tel" class="form-control" id="reportPhone"
                                value="{{ $user['phone'] }}" required>
                        </div>

                        {{-- Catégorie --}}
                        <div class="col-12">
                            <label for="reportCategory" class="form-label fw-medium">Catégorie</label>
                            <select class="form-select" id="reportCategory" required>
                                <option value="" disabled selected>Choisir une catégorie...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label for="reportDescription" class="form-label fw-medium">Description</label>
                            <textarea class="form-control" id="reportDescription" rows="4"
                                    placeholder="Décrivez la situation..." required></textarea>
                        </div>

                        {{-- Upload Photo / Vidéo --}}
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Photo</label>
                            <div class="upload-zone" id="photoUploadZone">
                                <input type="file" class="d-none" id="reportPhoto" accept="image/*">
                                <label for="reportPhoto" class="upload-label mb-0">
                                    <i class="bi bi-camera-fill fs-3 text-primary"></i>
                                    <span class="small text-muted">Cliquez pour ajouter une photo</span>
                                </label>
                                <div class="upload-preview d-none" id="photoPreview"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Vidéo</label>
                            <div class="upload-zone" id="videoUploadZone">
                                <input type="file" class="d-none" id="reportVideo" accept="video/*">
                                <label for="reportVideo" class="upload-label mb-0">
                                    <i class="bi bi-camera-video-fill fs-3 text-primary"></i>
                                    <span class="small text-muted">Cliquez pour ajouter une vidéo</span>
                                </label>
                                <div class="upload-preview d-none" id="videoPreview"></div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 mt-4" id="reportSubmitBtn">
                        <i class="bi bi-send-fill me-2"></i> Signaler l'urgence
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal confirmation signalement --}}
    <div class="modal fade" id="reportModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content sea-card border-0">
                <div class="modal-body text-center p-5">
                    <div class="success-icon mb-4">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Signalement envoyé !</h4>
                    <p class="text-muted mb-4">
                        Votre urgence a été transmise aux services compétents.
                        Vous pouvez suivre son évolution dans votre historique.
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('history') }}" class="btn btn-primary">Voir l'historique</a>
                        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
