@extends('layouts.app')
@section('title', $produit->nom_produit)
@section('page-title', 'Produits')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $produit->nom_produit }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('produits.index') }}" class="text-muted">Produits</a></li>
                <li class="breadcrumb-item active text-muted">{{ $produit->nom_produit }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('produits.edit', $produit) }}" class="btn btn-light btn-sm">
            <i class="bi bi-pencil me-1"></i> Modifier
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Infos produit -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-info-circle me-2 text-primary"></i>Informations</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted">Catégorie</td>
                        <td><span class="badge bg-light text-dark border">{{ $produit->categorie->nom_categorie ?? '—' }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Prix unitaire</td>
                        <td class="money fw-bold">{{ number_format($produit->prix_unitaire, 2) }} MAD</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Stock actuel</td>
                        <td>
                            <span class="fw-bold" style="font-size:18px;">{{ $produit->quantite_stock }}</span>
                            <span class="text-muted ms-1">unités</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Seuil alerte</td>
                        <td>{{ $produit->seuil_alerte }} unités</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Statut</td>
                        <td><span class="stock-badge {{ $produit->statut_stock }}">
                            @if($produit->statut_stock === 'normal') ✅ Normal
                            @elseif($produit->statut_stock === 'faible') ⚠️ Faible
                            @else ❌ Rupture
                            @endif
                        </span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Valeur stock</td>
                        <td class="money fw-semibold text-success">{{ number_format($produit->prix_unitaire * $produit->quantite_stock, 2) }} MAD</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Ajouté le</td>
                        <td>{{ $produit->date_ajout->format('d/m/Y') }}</td>
                    </tr>
                </table>
                @if($produit->description)
                    <hr>
                    <p class="text-muted mb-0" style="font-size:13px;">{{ $produit->description }}</p>
                @endif
            </div>
        </div>

        <!-- Ajouter du stock -->
        <div class="card" style="border: 2px solid #059669;">
            <div class="card-header" style="background:#f0fdf4;color:#166534;">
                <i class="bi bi-plus-circle-fill me-2"></i>Réapprovisionner le Stock
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('produits.ajouter-stock', $produit) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Quantité à ajouter</label>
                        <input type="number" name="quantite" class="form-control" min="1" value="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motif</label>
                        <input type="text" name="description" class="form-control" placeholder="Réapprovisionnement fournisseur...">
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-plus-lg me-1"></i> Ajouter au Stock
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Historique mouvements -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-arrow-left-right me-2 text-secondary"></i>Historique des Mouvements</span>
                <span class="badge bg-secondary-subtle text-secondary">{{ $mouvements->total() }} mouvements</span>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th class="text-center">Quantité</th>
                            <th>Description</th>
                            <th>Service lié</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mouvements as $mvt)
                        <tr>
                            <td class="text-muted">{{ $mvt->date_mouvement->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge bg-{{ $mvt->type_badge }}-subtle text-{{ $mvt->type_badge }}">
                                    {{ $mvt->type_icon }} {{ $mvt->type_mouvement }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="money fw-bold {{ $mvt->type_mouvement === 'ENTREE' ? 'text-success' : 'text-danger' }}">
                                    {{ $mvt->type_mouvement === 'ENTREE' ? '+' : '-' }}{{ $mvt->quantite }}
                                </span>
                            </td>
                            <td class="text-muted" style="font-size:12.5px;">{{ $mvt->description ?? '—' }}</td>
                            <td>
                                @if($mvt->historique)
                                    <a href="{{ route('historique.show', $mvt->historique) }}" class="btn btn-sm btn-light">
                                        #{{ $mvt->id_historique }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Aucun mouvement enregistré</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($mouvements->hasPages())
            <div class="card-body border-top py-3">{{ $mouvements->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection