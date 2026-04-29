@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')
<div class="page-header">
    <div>
        <h1>Tableau de bord</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active text-muted">Vue d'ensemble — {{ now()->format('d F Y') }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('historique.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nouveau Service
    </a>
</div>

<!-- ===== STAT CARDS ===== -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="stat-value">{{ number_format($stats['total_clients']) }}</div>
                <div class="stat-label">Clients actifs</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-currency-dollar"></i></div>
            <div>
                <div class="stat-value">{{ number_format($stats['ca_mois'], 2) }}</div>
                <div class="stat-label">CA ce mois (MAD)</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="bi bi-clock-history"></i></div>
            <div>
                <div class="stat-value">{{ number_format($stats['total_services']) }}</div>
                <div class="stat-label">Services réalisés</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon {{ $stats['produits_faible_stock'] > 0 ? 'red' : 'teal' }}">
                <i class="bi bi-archive-fill"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['produits_faible_stock'] }}</div>
                <div class="stat-label">
                    Alertes stock
                    @if($stats['produits_rupture'] > 0)
                        <span class="badge bg-danger ms-1">{{ $stats['produits_rupture'] }} rupture(s)</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">

    <!-- ===== DERNIERS SERVICES ===== -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-clock-history me-2 text-primary"></i>Derniers Services</span>
                <a href="{{ route('historique.index') }}" class="btn btn-sm btn-light">Voir tout</a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Produits</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Voir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($derniersServices as $service)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar" style="width:30px;height:30px;font-size:12px;">
                                        {{ strtoupper(substr($service->client->nom ?? '?', 0, 1)) }}
                                    </div>
                                    <span class="fw-semibold">{{ $service->client->nom ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="text-muted">{{ $service->date_service->format('d/m/Y') }}</td>
                            <td><span class="badge bg-light text-dark">{{ $service->details->count() }} produit(s)</span></td>
                            <td class="money fw-semibold">{{ number_format($service->montant_total, 2) }} MAD</td>
                            <td>
                                <span class="badge bg-{{ $service->statut_badge }}-subtle text-{{ $service->statut_badge }}">
                                    {{ $service->statut_label }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('historique.show', $service) }}" class="btn btn-sm btn-light">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.4;"></i>
                                Aucun service enregistré
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===== ALERTES STOCK ===== -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>Alertes Stock</span>
                <a href="{{ route('stock.etat') }}" class="btn btn-sm btn-light">Gérer</a>
            </div>
            <div class="card-body p-0">
                @forelse($alertesStock as $produit)
                <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                    <div>
                        <div class="fw-semibold" style="font-size:13.5px;">{{ $produit->nom_produit }}</div>
                        <div class="text-muted" style="font-size:12px;">{{ $produit->categorie->nom_categorie ?? '-' }}</div>
                    </div>
                    <span class="stock-badge {{ $produit->statut_stock }}">
                        <i class="bi bi-circle-fill" style="font-size:6px;"></i>
                        {{ $produit->quantite_stock }} u.
                    </span>
                </div>
                @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-check-circle" style="font-size:2rem;display:block;opacity:.4;margin-bottom:8px;color:#059669;"></i>
                    <span style="font-size:13px;">Tous les stocks sont OK</span>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ===== TOP CLIENTS ===== -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-trophy-fill me-2 text-warning"></i>Top Clients</span>
                <a href="{{ route('clients.index') }}" class="btn btn-sm btn-light">Voir tout</a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th class="text-center">Services</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topClients as $idx => $client)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted fw-bold" style="width:18px;font-size:12px;">{{ $idx+1 }}</span>
                                    <div class="avatar" style="width:30px;height:30px;font-size:12px;">
                                        {{ strtoupper(substr($client->nom, 0, 1)) }}
                                    </div>
                                    <a href="{{ route('clients.show', $client) }}" class="text-decoration-none fw-semibold text-dark">
                                        {{ $client->nom }}
                                    </a>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary">{{ $client->historiques_count }}</span>
                            </td>
                            <td class="text-end money fw-semibold">{{ number_format($client->total_depense, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">Aucun client</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===== DERNIERS MOUVEMENTS ===== -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-arrow-left-right me-2 text-secondary"></i>Mouvements Stock</span>
                <a href="{{ route('stock.index') }}" class="btn btn-sm btn-light">Voir tout</a>
            </div>
            <div class="card-body p-0">
                @forelse($derniersMouvements as $mvt)
                <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                    <div class="stat-icon {{ $mvt->type_mouvement === 'ENTREE' ? 'green' : 'red' }}"
                         style="width:34px;height:34px;border-radius:8px;font-size:14px;">
                        <i class="bi bi-{{ $mvt->type_mouvement === 'ENTREE' ? 'arrow-up' : 'arrow-down' }}"></i>
                    </div>
                    <div class="flex-1">
                        <div class="fw-semibold" style="font-size:13px;">{{ $mvt->produit->nom_produit ?? 'N/A' }}</div>
                        <div class="text-muted" style="font-size:11.5px;">{{ $mvt->date_mouvement->format('d/m/Y H:i') }}</div>
                    </div>
                    <span class="money {{ $mvt->type_mouvement === 'ENTREE' ? 'text-success' : 'text-danger' }} fw-bold">
                        {{ $mvt->type_mouvement === 'ENTREE' ? '+' : '-' }}{{ $mvt->quantite }}
                    </span>
                </div>
                @empty
                <div class="text-center text-muted py-4">Aucun mouvement</div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection