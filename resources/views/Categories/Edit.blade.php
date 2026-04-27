@extends('layouts.app')
@section('title', 'Modifier Catégorie')
@section('page-title', 'Catégories')

@section('content')
<div class="page-header">
    <div>
        <h1>Modifier la Catégorie</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('categories.index') }}" class="text-muted">Catégories</a></li>
                <li class="breadcrumb-item active text-muted">{{ $categorie->nom_categorie }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil-square me-2 text-primary"></i> Modifier : {{ $categorie->nom_categorie }}
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('categories.update', $categorie) }}">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <label class="form-label">Nom de la catégorie <span class="text-danger">*</span></label>
                        <input type="text" name="nom_categorie"
                               class="form-control @error('nom_categorie') is-invalid @enderror"
                               value="{{ old('nom_categorie', $categorie->nom_categorie) }}" required>
                        @error('nom_categorie')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $categorie->description) }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Mettre à jour
                        </button>
                        <a href="{{ route('categories.index') }}" class="btn btn-light">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection