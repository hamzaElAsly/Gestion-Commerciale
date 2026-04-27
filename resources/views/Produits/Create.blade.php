@extends('layouts.app')
@section('title', 'Nouveau Produit')
@section('page-title', 'Produits')

@section('content')
<div class="page-header">
    <div>
        <h1>Nouveau Produit</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('produits.index') }}" class="text-muted">Produits</a></li>
                <li class="breadcrumb-item active text-muted">Nouveau</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-box-seam-fill me-2 text-primary"></i> Informations du produit
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('produits.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Nom du produit <span class="text-danger">*</span></label>
                        <input type="text" name="nom_produit" class="form-control @error('nom_produit') is-invalid @enderror"
                               value="{{ old('nom_produit') }}" placeholder="Ex: Huile moteur 5W30" autofocus required>
                        @error('nom_produit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catégorie <span class="text-danger">*</span></label>
                        <select name="id_categorie" class="form-select @error('id_categorie') is-invalid @enderror" required>
                            <option value="">— Sélectionner une catégorie —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id_categorie }}" {{ old('id_categorie') == $cat->id_categorie ? 'selected' : '' }}>
                                    {{ $cat->nom_categorie }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_categorie')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="mt-1">
                            <a href="{{ route('categories.create') }}" class="text-primary" style="font-size:12px;">
                                <i class="bi bi-plus-circle me-1"></i>Créer une nouvelle catégorie
                            </a>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Prix unitaire (MAD) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="prix_unitaire" step="0.01" min="0"
                                       class="form-control @error('prix_unitaire') is-invalid @enderror"
                                       value="{{ old('prix_unitaire', '0.00') }}" required>
                                <span class="input-group-text">MAD</span>
                            </div>
                            @error('prix_unitaire')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Quantité en stock <span class="text-danger">*</span></label>
                            <input type="number" name="quantite_stock" min="0"
                                   class="form-control @error('quantite_stock') is-invalid @enderror"
                                   value="{{ old('quantite_stock', 0) }}" required>
                            @error('quantite_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Seuil d'alerte <span class="text-danger">*</span></label>
                            <input type="number" name="seuil_alerte" min="0"
                                   class="form-control @error('seuil_alerte') is-invalid @enderror"
                                   value="{{ old('seuil_alerte', 5) }}" required>
                            <div class="form-text">Alerte si stock ≤ cette valeur</div>
                            @error('seuil_alerte')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"
                                  placeholder="Description optionnelle du produit...">{{ old('description') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Enregistrer
                        </button>
                        <a href="{{ route('produits.index') }}" class="btn btn-light">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection