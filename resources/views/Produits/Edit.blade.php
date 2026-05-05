@extends('layouts.app')
@section('title', 'Modifier Produit')
@section('page-title', 'Produits')

@section('content')
<div class="page-header">
    <div>
        <h1>Modifier le Produit</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('produits.index') }}" class="text-muted">Produits</a></li>
                <li class="breadcrumb-item active text-muted">{{ $produit->nom_produit }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil-square me-2 text-primary"></i> Modifier : {{ $produit->nom_produit }}
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('produits.update', $produit) }}">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Nom du produit <span class="text-danger">*</span></label>
                        <input type="text" name="nom_produit" class="form-control @error('nom_produit') is-invalid @enderror"
                               value="{{ old('nom_produit', $produit->nom_produit) }}" required>
                        @error('nom_produit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Prix unitaire (MAD) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="prix_unitaire" step="0.01" min="0"
                                       class="form-control @error('prix_unitaire') is-invalid @enderror"
                                       value="{{ old('prix_unitaire', $produit->prix_unitaire) }}" required>
                                <span class="input-group-text">MAD</span>
                            </div>
                            @error('prix_unitaire')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Prix Vente (MAD) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="prix_vente" step="0.01" min="0"
                                       class="form-control @error('prix_vente') is-invalid @enderror"
                                       value="{{ old('prix_vente', $produit->prix_vente) }}" required>
                                <span class="input-group-text">MAD</span>
                            </div>
                            @error('prix_vente')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Quantité en stock</label>
                            <input type="number" name="quantite_stock" min="0"
                                   class="form-control @error('quantite_stock') is-invalid @enderror"
                                   value="{{ old('quantite_stock', $produit->quantite_stock) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Seuil d'alerte</label>
                            <input type="number" name="seuil_alerte" min="0"
                                   class="form-control @error('seuil_alerte') is-invalid @enderror"
                                   value="{{ old('seuil_alerte', $produit->seuil_alerte) }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $produit->description) }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Mettre à jour
                        </button>
                        <a href="{{ route('produits.index') }}" class="btn btn-light">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection