@extends('layouts.app')

@section('title', 'Catégories — Administration')
@section('page-title', 'Gestion des catégories')
@section('page-subtitle', $categories->count() . ' catégorie(s)')

@section('content')

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="sea-card p-4">
                <h5 class="fw-semibold mb-3">Ajouter une catégorie</h5>
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nom</label>
                        <input type="text" name="name" class="form-control" placeholder="Ex. : Incendie" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-lg me-1"></i> Créer
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="sea-card p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table admin-table mb-0">
                        <thead>
                            <tr>
                                <th>Catégorie</th>
                                <th>Signalements</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                                <tr>
                                    <td>
                                        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="d-flex gap-2">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="name" class="form-control form-control-sm" value="{{ $category->name }}" required>
                                            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-check-lg"></i></button>
                                        </form>
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $category->signalements_count }}</span></td>
                                    <td>
                                        @if($category->signalements_count === 0)
                                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Supprimer cette catégorie ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        @else
                                            <span class="text-muted small">Utilisée</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
