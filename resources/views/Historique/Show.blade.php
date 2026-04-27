@extends('layouts.app')
@section('title', 'Service #' . $historique->id_historique)
@section('page-title', 'Historique des Services')

@section('content')
<div class="page-header">
    <div>
        <h1>Service #{{ $historique->id_historique }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('historique.index') }}" class="text-muted">Historique</a></li>
                <li class="breadcrumb-item active text-muted">Service #{{ $historique->id_historique }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('historique.facture', $historique) }}" class="btn btn-light btn-sm" target="_blank">
            <i class="bi bi-printer me-1"></i> Imprimer Facture
        </a>
        <a href="{{ route('historique.edit', $historique) }}" class="btn btn-light btn-sm">
            <i class="bi bi-pencil me-1"></i> Modifier
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <!-- Infos Service -->
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-info-circle me-2 text-primary"></i>Informations</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted fw-medium">Client</td>
                        <td class="fw-bold">
                            <a href="{{ route('clients.show', $historique->client) }}" class="text-decoration-none">
                                {{ $historique->client->nom }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-medium">Téléphone</td>
                        <td>{{ $historique->client->telephone ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-medium">Date</td>
                        <td>{{ $historique->date_service->format('d/m/Y à H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-medium">Statut</td>
                        <td>
                            <span class="badge bg-{{ $historique->statut_badge }}-subtle text-{{ $historique->statut_badge }}">
                                {{ $historique->statut_label }}
                            </span>
                        </td>
                    </tr>
                    @if($historique->remarque)
                    <tr>
                        <td class="text-muted fw-medium">Remarque</td>
                        <td>{{ $historique->remarque }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Montant Total -->
        <div class="card" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white;">
            <div class="card-body text-center py-4">
                <div style="font-size:13px;opacity:.8;margin-bottom:8px;">Montant Total du Service</div>
                <div class="money" style="font-size:32px;font-weight:800;">
                    {{ number_format($historique->montant_total, 2) }}
                </div>
                <div style="font-size:16px;opacity:.8;margin-top:4px;">MAD</div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Produits utilisés -->
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-box-seam me-2 text-primary"></i>Produits Utilisés</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Catégorie</th>
                            <th class="text-center">Quantité</th>
                            <th class="text-end">Prix Unitaire</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historique->details as $detail)
                        <tr>
                            <td class="fw-semibold">{{ $detail->produit->nom_produit ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $detail->produit->categorie->nom_categorie ?? '—' }}
                                </span>
                            </td>
                            <td class="text-center">{{ $detail->quantite_utilisee }}</td>
                            <td class="text-end money">{{ number_format($detail->prix_unitaire, 2) }}</td>
                            <td class="text-end money fw-semibold">{{ number_format($detail->prix_total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background: #f8fafc;">
                            <td colspan="4" class="text-end fw-bold">Total :</td>
                            <td class="text-end money fw-bold text-primary">
                                {{ number_format($historique->montant_total, 2) }} MAD
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Mouvements de stock générés -->
        @if($historique->mouvementsStock->count() > 0)
        <div class="card">
            <div class="card-header"><i class="bi bi-arrow-left-right me-2 text-secondary"></i>Mouvements de Stock Générés</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Type</th>
                            <th class="text-center">Quantité</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historique->mouvementsStock as $mvt)
                        <tr>
                            <td>{{ $mvt->produit->nom_produit ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $mvt->type_badge }}-subtle text-{{ $mvt->type_badge }}">
                                    {{ $mvt->type_mouvement }}
                                </span>
                            </td>
                            <td class="text-center money">{{ $mvt->quantite }}</td>
                            <td class="text-muted">{{ $mvt->description }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection