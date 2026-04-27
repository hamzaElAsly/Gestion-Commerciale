@extends('layouts.app')
@section('title', 'Produits')
@section('page-title', 'Produits')

@section('content')
<div class="page-header">
    <div>
        <h1>Produits</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active text-muted">Catalogue des produits</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('produits.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nouveau Produit
    </a>
</div>

<!-- Filtres -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Rechercher un produit..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="categorie" class="form-select form-select-sm">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id_categorie }}" {{ request('categorie') == $cat->id_categorie ? 'selected' : '' }}>
                            {{ $cat->nom_categorie }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="stock" class="form-select form-select-sm">
                    <option value="">Tout le stock</option>
                    <option value="normal" {{ request('stock') == 'normal' ? 'selected' : '' }}>✅ Normal</option>
                    <option value="faible" {{ request('stock') == 'faible' ? 'selected' : '' }}>⚠️ Faible</option>
                    <option value="rupture" {{ request('stock') == 'rupture' ? 'selected' : '' }}>❌ Rupture</option>
                </select>
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
                    <a href="{{ route('produits.index') }}" class="btn btn-light btn-sm">Réinitialiser</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Produit</th>
                    <th>Catégorie</th>
                    <th class="text-end">Prix Unitaire</th>
                    <th class="text-center">Stock Actuel</th>
                    <th class="text-center">Seuil Alerte</th>
                    <th>Statut</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produits as $produit)
                <tr class="{{ $produit->statut_stock === 'rupture' ? 'table-danger-subtle' : ($produit->statut_stock === 'faible' ? 'table-warning-subtle' : '') }}">
                    <td class="text-muted">{{ $produit->id_produit }}</td>
                    <td>
                        <div class="fw-semibold">{{ $produit->nom_produit }}</div>
                        @if($produit->description)
                            <div class="text-muted" style="font-size:11.5px;">{{ Str::limit($produit->description, 60) }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            {{ $produit->categorie->nom_categorie ?? '—' }}
                        </span>
                    </td>
                    <td class="text-end money">{{ number_format($produit->prix_unitaire, 2) }} MAD</td>
                    <td class="text-center">
                        <span class="fw-bold" style="font-size:15px;">{{ $produit->quantite_stock }}</span>
                    </td>
                    <td class="text-center text-muted">{{ $produit->seuil_alerte }}</td>
                    <td>
                        <span class="stock-badge {{ $produit->statut_stock }}">
                            <i class="bi bi-circle-fill" style="font-size:6px;"></i>
                            @if($produit->statut_stock === 'normal') Stock OK
                            @elseif($produit->statut_stock === 'faible') Stock Faible
                            @else Rupture
                            @endif
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('produits.show', $produit) }}" class="btn btn-sm btn-light" title="Voir">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('produits.edit', $produit) }}" class="btn btn-sm btn-light" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <!-- Ajouter Stock -->
                            <button class="btn btn-sm btn-success" title="Ajouter stock"
                                    data-bs-toggle="modal" data-bs-target="#modalStock{{ $produit->id_produit }}">
                                <i class="bi bi-plus"></i>
                            </button>
                            <form method="POST" action="{{ route('produits.destroy', $produit) }}"
                                  onsubmit="return confirm('Supprimer ce produit ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-light text-danger" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                <!-- Modal Ajouter Stock -->
                <div class="modal fade" id="modalStock{{ $produit->id_produit }}" tabindex="-1">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title">Ajouter Stock — {{ $produit->nom_produit }}</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="{{ route('produits.ajouter-stock', $produit) }}">
                                @csrf
                                <div class="modal-body">
                                    <p class="text-muted">Stock actuel : <strong>{{ $produit->quantite_stock }}</strong></p>
                                    <div class="mb-3">
                                        <label class="form-label">Quantité à ajouter</label>
                                        <input type="number" name="quantite" class="form-control" min="1" value="1" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <input type="text" name="description" class="form-control" placeholder="Réapprovisionnement...">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="bi bi-plus-lg me-1"></i> Ajouter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-box-seam" style="font-size:2.5rem;display:block;opacity:.3;margin-bottom:10px;"></i>
                        Aucun produit trouvé.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($produits->hasPages())
    <div class="card-body border-top py-3">{{ $produits->links() }}</div>
    @endif
</div>
@endsection