@extends('layouts.app')
@section('title', 'Vente #' . str_pad($vente->id_vente, 4, '0', STR_PAD_LEFT))
@section('page-title', 'Ventes')

@section('content')
<div class="page-header">
    <div>
        <h1>Vente #{{ str_pad($vente->id_vente, 4, '0', STR_PAD_LEFT) }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('vente.index') }}" class="text-muted">Ventes</a></li>
                <li class="breadcrumb-item active text-muted">
                    Vente #{{ str_pad($vente->id_vente, 4, '0', STR_PAD_LEFT) }}
                </li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('vente.imprimer', $vente->id_vente) }}"
           class="btn btn-light btn-sm" target="_blank">
            <i class="bi bi-printer me-1"></i>Imprimer PDF
        </a>
        <a href="{{ route('vente.edit', $vente) }}" class="btn btn-light btn-sm">
            <i class="bi bi-pencil me-1"></i>Modifier
        </a>
        <form method="POST" action="{{ route('vente.destroy', $vente) }}"
              onsubmit="return confirm('Supprimer cette vente ? Le stock sera restauré.')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-light text-danger">
                <i class="bi bi-trash me-1"></i>Supprimer
            </button>
        </form>
    </div>
</div>

<div class="row g-4">

    {{-- ── Infos + totaux ── --}}
    <div class="col-lg-4">

        {{-- Carte client --}}
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-person-fill me-2 text-primary"></i>Client
            </div>
            <div class="card-body text-center py-4">
                <div class="avatar mx-auto mb-3" style="width:60px;height:60px;font-size:22px;">
                    {{ strtoupper(substr($vente->nom_client, 0, 1)) }}
                </div>
                <h5 class="fw-bold mb-0">{{ $vente->nom_client }}</h5>
                <div class="text-muted mt-1" style="font-size:12.5px;">
                    Vente du {{ $vente->created_at->format('d/m/Y à H:i') }}
                </div>
            </div>
        </div>

        {{-- Totaux --}}
        <div class="card" style="background:linear-gradient(135deg,var(--primary),#4f46e5);color:white;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between mb-2" style="font-size:13.5px;">
                    <span style="opacity:.75;">Sous-total produits</span>
                    <span class="money fw-semibold">{{ number_format($vente->details->sum('prix_total'), 2) }} MAD</span>
                </div>
                <div class="d-flex justify-content-between mb-3" style="font-size:13.5px;">
                    <span style="opacity:.75;">Charges / Frais</span>
                    <span class="money fw-semibold">{{ number_format($vente->charges, 2) }} MAD</span>
                </div>
                <div style="border-top:1px solid rgba(255,255,255,.2);padding-top:12px;"
                     class="d-flex justify-content-between">
                    <span class="fw-bold" style="font-size:14px;">TOTAL TTC</span>
                    <span class="money fw-bold" style="font-size:20px;">
                        {{ number_format($vente->montant_total, 2) }} MAD
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Détail produits ── --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-box-seam me-2 text-primary"></i>Détail des Produits
            </div>

            @if($vente->details->count())
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Produit</th>
                            <th class="text-center">Quantité</th>
                            <th class="text-end">Prix unit.</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vente->details as $idx => $d)
                        <tr>
                            <td class="text-muted">{{ $idx + 1 }}</td>
                            <td class="fw-semibold">{{ $d->nom_produit }}</td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">{{ $d->quantite }}</span>
                            </td>
                            <td class="text-end money">{{ number_format($d->prix_vente, 2) }}</td>
                            <td class="text-end money fw-bold">{{ number_format($d->prix_total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background:#f8fafc;">
                            <td colspan="4" class="text-end fw-bold">Sous-total :</td>
                            <td class="text-end money fw-bold text-primary">
                                {{ number_format($vente->details->sum('prix_total'), 2) }} MAD
                            </td>
                        </tr>
                        <tr style="background:#f8fafc;">
                            <td colspan="4" class="text-end fw-bold">Charges :</td>
                            <td class="text-end money fw-semibold text-muted">
                                + {{ number_format($vente->charges, 2) }} MAD
                            </td>
                        </tr>
                        <tr style="background:#eff6ff;">
                            <td colspan="4" class="text-end fw-bold text-primary">TOTAL TTC :</td>
                            <td class="text-end money fw-bold text-primary" style="font-size:16px;">
                                {{ number_format($vente->montant_total, 2) }} MAD
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-box-seam" style="font-size:2.5rem;display:block;opacity:.25;margin-bottom:10px;"></i>
                Aucun produit — vente de charges uniquement.
            </div>
            @endif
        </div>

        {{-- Bouton imprimer en bas --}}
        <div class="mt-3 d-flex justify-content-end">
            <a href="{{ route('vente.imprimer', $vente->id_vente) }}"
               class="btn btn-danger" target="_blank">
                <i class="bi bi-file-earmark-pdf me-2"></i>Télécharger la facture PDF
            </a>
        </div>
    </div>

</div>
@endsection