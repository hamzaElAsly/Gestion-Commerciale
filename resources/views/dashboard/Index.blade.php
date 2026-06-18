@extends('layouts.app')
@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')

{{-- ════════════════════════════════════════════════════════
     EN-TÊTE PAGE
════════════════════════════════════════════════════════ --}}
<div class="page-header mb-4">
    <div>
        <h1 style="font-size:24px;font-weight:800;color:#0f172a;margin:0;">
            Tableau de bord
        </h1>
        <p class="text-muted mb-0" style="font-size:13.5px;margin-top:4px;">
            <i class="bi bi-calendar3 me-1"></i>
            {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
            &nbsp;—&nbsp;
            <i class="bi bi-clock me-1"></i>{{ now()->addHours(1)->format('H:i') }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('vente.create') }}" class="btn btn-light btn-sm">
            <i class="bi bi-receipt me-1"></i>Nouvelle Vente
        </a>
        <a href="{{ route('historique.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Nouveau Service
        </a>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════
     RANGÉE 1 — CA & VENTES (KPIs PRINCIPAUX)
════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- CA Aujourd'hui --}}
    <div class="col-6 col-md-4 col-xl-3">
        <div class="kpi-card kpi-blue">
            <div class="kpi-icon"><i class="bi bi-sun-fill"></i></div>
            <div class="kpi-body">
                <div class="kpi-value">{{ number_format($caAujourdhui,0) }}</div>
                <div class="kpi-label">CA aujourd'hui</div>
                <div class="kpi-sub text-muted">MAD</div>
            </div>
        </div>
    </div>

    {{-- CA Ce Mois --}}
    <div class="col-6 col-md-4 col-xl-3">
        <div class="kpi-card kpi-indigo">
            <div class="kpi-icon"><i class="bi bi-calendar-month-fill"></i></div>
            <div class="kpi-body">
                <div class="kpi-value">{{ number_format($caMois, 0) }}</div>
                <div class="kpi-label">CA ce mois</div>
                <div class="kpi-sub">
                    @if($deltaCaMois >= 0)
                        <span style="color:#059669;">
                            <i class="bi bi-arrow-up-short"></i>+{{ $deltaCaMois }}%
                        </span>
                    @else
                        <span style="color:#dc2626;">
                            <i class="bi bi-arrow-down-short"></i>{{ $deltaCaMois }}%
                        </span>
                    @endif
                    <span class="text-muted">vs mois préc.</span>
                </div>
            </div>
        </div>
    </div>

    {{-- CA Cette Année --}}
    <div class="col-6 col-md-4 col-xl-3">
        <div class="kpi-card kpi-violet">
            <div class="kpi-icon"><i class="bi bi-graph-up-arrow"></i></div>
            <div class="kpi-body">
                <div class="kpi-value">{{ number_format($caTotal, 0) }}</div>
                <div class="kpi-label">CA {{ now()->year }}</div>
                <div class="kpi-sub text-muted">MAD</div>
            </div>
        </div>
    </div>

    {{-- Nombre de Ventes --}}
    <div class="col-6 col-md-4 col-xl-3">
        <div class="kpi-card kpi-teal">
            <div class="kpi-icon"><i class="bi bi-receipt-cutoff"></i></div>
            <div class="kpi-body">
                <div class="kpi-value">{{ number_format($nbVentesMois) }}</div>
                <div class="kpi-label">Ventes ce mois</div>
                <div class="kpi-sub text-muted">Total : {{ $nbVentesTotal }}</div>
            </div>
        </div>
    </div>

    {{-- Charges ce mois --}}
    {{-- <div class="col-6 col-md-3">
        <div class="kpi-card kpi-rose">
            <div class="kpi-icon"><i class="bi bi-receipt"></i></div>
            <div class="kpi-body">
                <div class="kpi-value">{{ number_format($chargesTotalMois, 0, ',', ' ') }}</div>
                <div class="kpi-label">Charges ce mois</div>
                <div class="kpi-sub text-muted">Total : {{ number_format($chargesTotal, 0) }} MAD</div>
            </div>
        </div>
    </div> --}}
</div>

{{-- ════════════════════════════════════════════════════════
     RANGÉE 2 — CLIENTS & CHARGES
════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- Produits vendus --}}
    <div class="col-6 col-md-3">
        <div class="kpi-card kpi-cyan">
            <div class="kpi-icon"><i class="bi bi-people-fill"></i></div>
            <div class="kpi-body">
                <div class="kpi-value">{{ number_format($totalPrixVentes) }}</div>
                <div class="kpi-label">Produits vendus</div>
            </div>
        </div>
    </div>

    {{-- Produits achetés --}}
    <div class="col-6 col-md-3">
        <div class="kpi-card kpi-blue">
            <div class="kpi-icon"><i class="bi bi-person-check-fill"></i></div>
            <div class="kpi-body">
                <div class="kpi-value">{{ number_format($totalPrixAchats) }}</div>
                <div class="kpi-label">Produits achetés</div>
            </div>
        </div>
    </div>

    {{-- Panier moyen --}}
    <div class="col-6 col-md-3">
        <div class="kpi-card kpi-amber">
            <div class="kpi-icon"><i class="bi bi-basket3-fill"></i></div>
            <div class="kpi-body">
                <div class="kpi-value">{{ number_format($profit) }}</div>
                <div class="kpi-label">Profit total</div>
                <div class="kpi-sub text-muted">MAD</div>
            </div>
        </div>
    </div>

    {{-- Ventes aujourd'hui --}}
    <div class="col-6 col-md-4 col-xl-3">
        <div class="kpi-card kpi-amber">
            <div class="kpi-icon"><i class="bi bi-lightning-charge-fill"></i></div>
            <div class="kpi-body">
                <div class="kpi-value">{{ number_format($nbVentesAujourdhui) }}</div>
                <div class="kpi-label">Ventes aujourd'hui</div>
                <div class="kpi-sub text-muted">{{ now()->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════
     RANGÉE 3 — STOCK & PRODUITS
════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- Total produits --}}
    <div class="col-6 col-md-3">
        <div class="kpi-card kpi-slate">
            <div class="kpi-icon"><i class="bi bi-box-seam-fill"></i></div>
            <div class="kpi-body">
                <div class="kpi-value">{{ number_format($totalProduits) }}</div>
                <div class="kpi-label">Total produits</div>
                <div class="kpi-sub text-muted">références</div>
            </div>
        </div>
    </div>

    {{-- En stock --}}
    <div class="col-6 col-md-3">
        <div class="kpi-card kpi-green">
            <div class="kpi-icon"><i class="bi bi-archive-fill"></i></div>
            <div class="kpi-body">
                <div class="kpi-value">{{ number_format($produitsEnStock) }}</div>
                <div class="kpi-label">En stock</div>
                <div class="kpi-sub" style="color:#059669;font-weight:600;">disponibles</div>
            </div>
        </div>
    </div>

    {{-- Total unités vendues (sorties) --}}
    <div class="col-6 col-md-3">
        <div class="kpi-card kpi-indigo">
            <div class="kpi-icon"><i class="bi bi-arrow-down-circle-fill"></i></div>
            <div class="kpi-body">
                <div class="kpi-value">{{ number_format($totalUnitesSorties) }}</div>
                <div class="kpi-label">Unités vendues</div>
                <div class="kpi-sub text-muted">{{ number_format($unitesSortiesMois) }} ce mois</div>
            </div>
        </div>
    </div>

    {{-- Total unités achetées (entrées) --}}
    <div class="col-6 col-md-3">
        <div class="kpi-card kpi-teal">
            <div class="kpi-icon"><i class="bi bi-arrow-up-circle-fill"></i></div>
            <div class="kpi-body">
                <div class="kpi-value">{{ number_format($totalUnitesEntrees) }}</div>
                <div class="kpi-label">Unités achetées</div>
                <div class="kpi-sub text-muted">{{ number_format($unitesEntreesMois) }} ce mois</div>
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
    <div class="col-lg-4">
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
                            <th class="text-end">Total (MAD)</th>
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
                            <td class="text-end money fw-semibold">{{ number_format($client->total_depense) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">Aucun client</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Top produits vendus --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-trophy-fill me-2 text-warning"></i>Top Produits Vendus
            </div>
            <div class="card-body p-0">
                @forelse($topProduits as $idx => $p)
                <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                    <div style="width:24px;height:24px;border-radius:50%;
                                background:{{ ['#eff6ff','#f0fdf4','#fef9c3','#fff7ed','#fef2f2','#f5f3ff'][$idx] }};
                                display:flex;align-items:center;justify-content:center;
                                font-size:11px;font-weight:800;
                                color:{{ ['#1d4ed8','#166534','#854d0e','#9a3412','#991b1b','#4c1d95'][$idx] }};
                                flex-shrink:0;">
                        {{ $idx + 1 }}
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="fw-semibold text-truncate" style="font-size:13px;">
                            {{ $p->nom_produit }}
                        </div>
                        <div class="text-muted" style="font-size:11.5px;">
                            {{ number_format($p->chiffre_affaires, 2) }} MAD
                        </div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary fw-bold">
                        {{ number_format($p->total_vendu) }} u.
                    </span>
                </div>
                @empty
                <div class="text-center text-muted py-5" style="font-size:13px;">
                    <i class="bi bi-box-seam" style="font-size:2rem;display:block;opacity:.25;margin-bottom:8px;"></i>
                    Aucune vente enregistrée
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ===== DERNIERS MOUVEMENTS ===== -->
    <div class="col-lg-4">
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

@push('styles')
<style>
    /* ════════════ KPI CARDS ════════════ */
    .kpi-card {
        border-radius: 14px;
        padding: 18px 16px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,.07), 0 4px 10px rgba(0,0,0,.05);
        background: white;
        height: 100%;
        transition: transform .15s, box-shadow .15s;
        position: relative;
        overflow: hidden;
    }
    .kpi-card::after {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 4px; height: 100%;
        border-radius: 14px 0 0 14px;
    }
    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,.1);
    }

    .kpi-icon {
        width: 42px; height: 42px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; flex-shrink: 0;
    }
    .kpi-body { flex: 1; min-width: 0; }
    .kpi-value {
        font-size: 22px; font-weight: 800;
        color: #0f172a; line-height: 1.1;
        font-family: 'JetBrains Mono', monospace;
    }
    .kpi-label { font-size: 12px; font-weight: 600; color: #64748b; margin-top: 3px; }
    .kpi-sub   { font-size: 11.5px; margin-top: 3px; }

    /* Couleurs */
    .kpi-blue   .kpi-icon { background:#eff6ff; color:#2563eb; }
    .kpi-blue::after      { background:#2563eb; }
    .kpi-indigo .kpi-icon { background:#eef2ff; color:#4f46e5; }
    .kpi-indigo::after    { background:#4f46e5; }
    .kpi-violet .kpi-icon { background:#f5f3ff; color:#7c3aed; }
    .kpi-violet::after    { background:#7c3aed; }
    .kpi-teal   .kpi-icon { background:#f0fdfa; color:#0d9488; }
    .kpi-teal::after      { background:#0d9488; }
    .kpi-green  .kpi-icon { background:#f0fdf4; color:#059669; }
    .kpi-green::after     { background:#059669; }
    .kpi-amber  .kpi-icon { background:#fffbeb; color:#d97706; }
    .kpi-amber::after     { background:#d97706; }
    .kpi-rose   .kpi-icon { background:#fff1f2; color:#e11d48; }
    .kpi-rose::after      { background:#e11d48; }
    .kpi-cyan   .kpi-icon { background:#ecfeff; color:#0891b2; }
    .kpi-cyan::after      { background:#0891b2; }
    .kpi-red    .kpi-icon { background:#fef2f2; color:#dc2626; }
    .kpi-red::after       { background:#dc2626; }
    .kpi-slate  .kpi-icon { background:#f8fafc; color:#475569; }
    .kpi-slate::after     { background:#475569; }
</style>
@endpush