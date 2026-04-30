@extends('layouts.app')
@section('title', 'État du Stock')
@section('page-title', 'Gestion du Stock')

@section('content')
<div class="page-header">
    <div>
        <h1>État du Stock</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active text-muted">Inventaire en temps réel</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('stock.index') }}" class="btn btn-light btn-sm">
            <i class="bi bi-arrow-left-right me-1"></i> Mouvements
        </a>
        <a href="{{ route('produits.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Nouveau Produit
        </a>
    </div>
</div>

<!-- Statistiques globales -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-box-seam-fill"></i></div>
            <div>
                <div class="stat-value">{{ $stats['total_produits'] }}</div>
                <div class="stat-label">Total produits</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
            <div>
                <div class="stat-value">{{ $stats['stock_normal'] }}</div>
                <div class="stat-label">Stock normal</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div>
                <div class="stat-value">{{ $stats['stock_faible'] }}</div>
                <div class="stat-label">Stock faible</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon red"><i class="bi bi-x-circle-fill"></i></div>
            <div>
                <div class="stat-value">{{ $stats['stock_rupture'] }}</div>
                <div class="stat-label">En rupture</div>
            </div>
        </div>
    </div>
</div>

<!-- Valeur totale du stock -->
<div class="alert d-flex align-items-center gap-3 mb-4" style="background: linear-gradient(135deg, #eff6ff, #f5f3ff); border: none;">
    <i class="bi bi-safe2-fill" style="font-size: 24px; color: var(--primary);"></i>
    <div>
        <div style="font-size: 13px; color: #64748b;">Valeur totale du stock en cours</div>
        <div class="money fw-bold" style="font-size: 22px; color: var(--primary);">
            {{ number_format($stats['valeur_totale'], 2) }} MAD
        </div>
    </div>
</div>

<!-- Alertes en priorité -->
@if($produits->where('statut_stock', '!=', 'normal')->count() > 0)
<div class="card mb-4" style="border: 2px solid #fbbf24;">
    <div class="card-header" style="background: #fffbeb; color: #92400e;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        Produits nécessitant une attention — {{ $produits->where('statut_stock', '!=', 'normal')->count() }} produit(s)
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Produitttt</th>
                    {{-- <th>Catégorie</th> --}}
                    <th class="text-center">Stock actuel</th>
                    <th class="text-center">Seuil</th>
                    <th>Statut</th>
                    <th class="text-center">Action rapide</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produits->where('statut_stock', '!=', 'normal') as $p)
                <tr>
                    <td class="fw-semibold">{{ $p->nom_produit }}</td>
                    {{-- <td><span class="badge bg-light text-dark border">{{ $p->categorie->nom_categorie ?? '—' }}</span></td> --}}
                    <td class="text-center">
                        <span class="fw-bold" style="font-size:16px; color: {{ $p->statut_stock === 'rupture' ? '#dc2626' : '#d97706' }}">
                            {{ $p->quantite_stock }}
                        </span>
                    </td>
                    <td class="text-center text-muted">{{ $p->seuil_alerte }}</td>
                    <td><span class="stock-badge {{ $p->statut_stock }}">
                        @if($p->statut_stock === 'faible') ⚠️ Stock faible
                        @else ❌ Rupture de stock
                        @endif
                    </span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-success"
                                data-bs-toggle="modal"
                                data-bs-target="#modalStockRapide{{ $p->id_produit }}">
                            <i class="bi bi-plus-lg me-1"></i> Réappro.
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Tous les produits -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-archive me-2 text-primary"></i>Inventaire Complet</span>
        <span class="text-muted" style="font-size: 13px;">{{ $produits->count() }} produit(s)</span>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Produit</th>
                    {{-- <th>Catégorie</th> --}}
                    <th class="text-end">Prix Vente</th>
                    <th class="text-center">Stock</th>
                    <th class="text-center">Seuil</th>
                    <th>Statut</th>
                    <th class="text-end">Valeur stock</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produits as $p)
                <tr>
                    <td>
                        <a href="{{ route('produits.show', $p) }}" class="text-decoration-none fw-semibold text-dark">
                            {{ $p->nom_produit }}
                        </a>
                    </td>
                    {{-- <td><span class="badge bg-light text-dark border">{{ $p->categorie->nom_categorie ?? '—' }}</span></td> --}}
                    <td class="text-end money">{{ number_format($p->prix_vente, 2) }}</td>
                    <td class="text-center">
                        <span class="fw-bold" style="font-size:15px;">{{ $p->quantite_stock }}</span>
                    </td>
                    <td class="text-center text-muted">{{ $p->seuil_alerte }}</td>
                    <td>
                        <span class="stock-badge {{ $p->statut_stock }}">
                            <i class="bi bi-circle-fill" style="font-size:6px;"></i>
                            @if($p->statut_stock === 'normal') Normal
                            @elseif($p->statut_stock === 'faible') Faible
                            @else Rupture
                            @endif
                        </span>
                    </td>
                    <td class="text-end money">{{ number_format($p->prix_vente * $p->quantite_stock, 2) }}</td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('produits.show', $p) }}" class="btn btn-sm btn-light">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button class="btn btn-sm btn-success"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalStockRapide{{ $p->id_produit }}"
                                    title="Ajouter stock">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modals réapprovisionnement rapide -->
@foreach($produits as $p)
<div class="modal fade" id="modalStockRapide{{ $p->id_produit }}" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-plus-circle me-2 text-success"></i>Réapprovisionner</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('stock.entree') }}">
                @csrf
                <input type="hidden" name="id_produit" value="{{ $p->id_produit }}">
                <div class="modal-body">
                    <p class="fw-semibold mb-1">{{ $p->nom_produit }}</p>
                    <p class="text-muted mb-3">Stock actuel : <strong>{{ $p->quantite_stock }}</strong> unités</p>
                    <div class="mb-3">
                        <label class="form-label">Quantité à ajouter</label>
                        <input type="number" name="quantite" class="form-control" min="1" value="10" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" value="Réapprovisionnement">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection