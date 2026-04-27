@extends('layouts.app')
@section('title', 'Nouvelle Catégorie')
@section('page-title', 'Catégories')

@section('content')
<div class="page-header">
    <div>
        <h1>Nouvelle Catégorie</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('categories.index') }}" class="text-muted">Catégories</a></li>
                <li class="breadcrumb-item active text-muted">Nouvelle</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-tag-fill me-2 text-primary"></i> Créer une catégorie
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('categories.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label">Nom de la catégorie <span class="text-danger">*</span></label>
                        <input type="text" name="nom_categorie"
                               class="form-control @error('nom_categorie') is-invalid @enderror"
                               value="{{ old('nom_categorie') }}"
                               placeholder="Ex: Lubrifiants, Filtres, Pièces détachées..." autofocus required>
                        @error('nom_categorie')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3" placeholder="Description optionnelle...">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Enregistrer
                        </button>
                        <a href="{{ route('categories.index') }}" class="btn btn-light">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection