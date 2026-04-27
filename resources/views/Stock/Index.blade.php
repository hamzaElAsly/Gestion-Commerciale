@extends('layouts.app')
@section('title', 'Mouvements de Stock')
@section('page-title', 'Gestion du Stock')

@section('content')
<div class="page-header">
    <div>
        <h1>Mouvements de Stock</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active text-muted">Historique entrées / sorties</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('stock.etat') }}" class="btn btn-light btn-sm">
        <i class="bi bi-archive me-1"></i> État du Stock
    </a>
</div>

<!-- Stats entrées/sorties -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-arrow-up-circle-fill"></i></div>
            <div>
                <div class="stat-value">{{ number_format($totaux['entrees']) }}</div>
                <div class="stat-label">Total entrées (unités)</div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card">
            <div class="stat-icon red"><i class="bi bi-arrow-down-circle-fill"></i></div>
            <div>
                <div class="stat-value">{{ number_format($totaux['sorties']) }}</div>
                <div class="stat-label">Total sorties (unités)</div>
            </div>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Produit</label>
                <select name="produit_id" class="form-select form-select-sm">
                    <option value="">Tous les produits</option>
                    @foreach($produits as $p)
                        <option value="{{ $p->id_produit }}" {{ request('produit_id') == $p->id_produit ? 'selected' : '' }}>
                            {{ $p->nom_produit }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    <option value="ENTREE" {{ request('type') == 'ENTREE' ? 'selected' : '' }}>↑ Entrée</option>
                    <option value="SORTIE" {{ request('type') == 'SORTIE' ? 'selected' : '' }}>↓ Sortie</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Date début</label>
                <input type="date" name="date_debut" class="form-control form-control-sm" value="{{ request('date_debut') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Date fin</label>
                <input type="date" name="date_fin" class="form-control form-control-sm" value="{{ request('date_fin') }}">
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
                    <a href="{{ route('stock.index') }}" class="btn btn-light btn-sm">Réinitialiser</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-arrow-left-right me-2 text-secondary"></i>
        {{ $mouvements->total() }} mouvement(s)
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Produit</th>
                    <th>Type</th>
                    <th class="text-center">Quantité</th>
                    <th>Description</th>
                    <th>Service lié</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mouvements as $mvt)
                <tr>
                    <td class="text-muted">{{ $mvt->id_stock }}</td>
                    <td>
                        <div class="fw-semibold">{{ $mvt->produit->nom_produit ?? 'N/A' }}</div>
                        <div class="text-muted" style="font-size:11.5px;">{{ $mvt->produit->categorie->nom_categorie ?? '' }}</div>
                    </td>
                    <td>
                        <span class="badge bg-{{ $mvt->type_badge }}-subtle text-{{ $mvt->type_badge }} d-flex align-items-center gap-1" style="width:fit-content;">
                            <i class="bi bi-arrow-{{ $mvt->type_mouvement === 'ENTREE' ? 'up' : 'down' }}"></i>
                            {{ $mvt->type_mouvement }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="money fw-bold {{ $mvt->type_mouvement === 'ENTREE' ? 'text-success' : 'text-danger' }}" style="font-size:15px;">
                            {{ $mvt->type_mouvement === 'ENTREE' ? '+' : '-' }}{{ $mvt->quantite }}
                        </span>
                    </td>
                    <td class="text-muted" style="font-size:12.5px; max-width:200px;">{{ $mvt->description ?? '—' }}</td>
                    <td>
                        @if($mvt->historique)
                            <a href="{{ route('historique.show', $mvt->historique) }}" class="btn btn-xs btn-light" style="font-size:12px;padding:3px 8px;">
                                <i class="bi bi-link me-1"></i>{{ $mvt->historique->client->nom ?? "Service #{$mvt->id_historique}" }}
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-muted" style="font-size:12.5px;">{{ $mvt->date_mouvement->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-arrow-left-right" style="font-size:2.5rem;display:block;opacity:.3;margin-bottom:10px;"></i>
                        Aucun mouvement trouvé.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($mouvements->hasPages())
    <div class="card-body border-top py-3">{{ $mouvements->links() }}</div>
    @endif
</div>
@endsection