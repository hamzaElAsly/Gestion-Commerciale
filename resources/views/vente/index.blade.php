@extends('layouts.app')
@section('title', 'Ventes')
@section('page-title', 'Ventes')

@section('content')
<div class="page-header">
    <div>
        <h1>Ventes</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted">Accueil</a></li>
                <li class="breadcrumb-item active text-muted">Ventes</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('vente.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nouvelle Vente
    </a>
</div>

{{-- ── Filtres ── --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" class="form-control"
                           placeholder="Rechercher par nom client…"
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">Mois</label>
                <select name="mois" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('mois') == $m ? 'selected':'' }}>
                            {{ \Carbon\Carbon::create()->month($m)->locale('fr')->monthName }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Année</label>
                <select name="annee" class="form-select form-select-sm">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ request('annee', now()->year) == $y ? 'selected':'' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-funnel me-1"></i>Filtrer
                </button>
                <a href="{{ route('vente.index') }}" class="btn btn-light btn-sm">Réinitialiser</a>
            </div>
        </form>
    </div>
</div>

{{-- ── Tableau ── --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-receipt me-2 text-primary"></i>
            {{ $ventes->total() }} vente(s)
        </span>
        @if($ventes->total())
            <span class="money fw-bold text-primary">
                Total : {{ number_format($totalFiltré, 2) }} MAD
            </span>
        @endif
    </div>

    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Client</th>
                    <th class="text-center">Produits</th>
                    <th class="text-end">Charges</th>
                    <th class="text-end">Total TTC</th>
                    <th>Date</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ventes as $vente)
                <tr>
                    <td class="text-muted fw-semibold">#{{ str_pad($vente->id_vente, 4, '0', STR_PAD_LEFT) }}</td>

                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar" style="width:32px;height:32px;font-size:13px;flex-shrink:0;">
                                {{ strtoupper(substr($vente->nom_client, 0, 1)) }}
                            </div>
                            <span class="fw-semibold">{{ $vente->nom_client }}</span>
                        </div>
                    </td>

                    <td class="text-center">
                        @if($vente->details->count())
                            <span class="badge bg-primary-subtle text-primary">
                                {{ $vente->details->count() }} produit(s)
                            </span>
                        @else
                            <span class="badge bg-light text-muted">Aucun</span>
                        @endif
                    </td>

                    <td class="text-end money text-muted">
                        {{ number_format($vente->charges, 2) }}
                    </td>

                    <td class="text-end money fw-bold text-primary">
                        {{ number_format($vente->montant_total, 2) }} MAD
                    </td>

                    <td class="text-muted" style="font-size:13px;">
                        {{ $vente->created_at->format('d/m/Y H:i') }}
                    </td>

                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('vente.show', $vente) }}"
                               class="btn btn-sm btn-light" title="Voir le détail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('vente.edit', $vente) }}"
                               class="btn btn-sm btn-light" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="{{ route('vente.imprimer', $vente->id_vente) }}"
                               class="btn btn-sm btn-light" title="Imprimer PDF" target="_blank">
                                <i class="bi bi-printer"></i>
                            </a>
                            <form method="POST" action="{{ route('vente.destroy', $vente) }}"
                                  onsubmit="return confirm('Supprimer cette vente ? Le stock sera restauré.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-light text-danger" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-receipt" style="font-size:2.5rem;display:block;opacity:.25;margin-bottom:10px;"></i>
                        Aucune vente trouvée.
                        <a href="{{ route('vente.create') }}" class="d-block mt-2">Créer la première vente</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($ventes->hasPages())
    <div class="card-body border-top py-3">
        {{ $ventes->links() }}
    </div>
    @endif
</div>
@endsection