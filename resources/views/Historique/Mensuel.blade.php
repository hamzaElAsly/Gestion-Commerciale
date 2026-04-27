@extends('layouts.app')
@section('title', 'Rapport Mensuel')
@section('page-title', 'Rapport Mensuel')

@section('content')
<div class="page-header">
    <div>
        <h1>Rapport Mensuel</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active text-muted">{{ ucfirst($nomMois) }} {{ $annee }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ request()->fullUrlWithQuery(['export_pdf' => true]) }}" class="btn btn-danger" target="_blank">
        <i class="bi bi-file-earmark-pdf me-1"></i> Exporter PDF
    </a>
</div>

<!-- Filtres -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Mois</label>
                <select name="mois" class="form-select form-select-sm">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $mois == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->locale('fr')->monthName }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Année</label>
                <select name="annee" class="form-select form-select-sm">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ $annee == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Client (optionnel)</label>
                <select name="id_client" class="form-select form-select-sm">
                    <option value="">Tous les clients</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id_client }}" {{ request('id_client') == $c->id_client ? 'selected' : '' }}>
                            {{ $c->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm">Afficher</button>
            </div>
        </form>
    </div>
</div>

<!-- Statistiques du mois -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-calendar-check"></i></div>
            <div>
                <div class="stat-value">{{ $historiques->count() }}</div>
                <div class="stat-label">Services ce mois</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-currency-dollar"></i></div>
            <div>
                <div class="stat-value" style="font-size:20px;">{{ number_format($totalMois, 2) }}</div>
                <div class="stat-label">CA Total (MAD)</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="bi bi-people"></i></div>
            <div>
                <div class="stat-value">{{ $historiques->unique('id_client')->count() }}</div>
                <div class="stat-label">Clients servis</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-table me-2 text-primary"></i>
        Services de <strong>{{ ucfirst($nomMois) }} {{ $annee }}</strong>
        — {{ $historiques->count() }} enregistrement(s)
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Produits utilisés</th>
                    <th>Remarque</th>
                    <th class="text-end">Montant</th>
                    <th class="text-center">Facture</th>
                </tr>
            </thead>
            <tbody>
                @forelse($historiques as $h)
                <tr>
                    <td class="text-muted">#{{ $h->id_historique }}</td>
                    <td class="fw-semibold">{{ $h->client->nom ?? 'N/A' }}</td>
                    <td class="text-muted">{{ $h->date_service->format('d/m/Y') }}</td>
                    <td>
                        @foreach($h->details as $d)
                            <div style="font-size:12px;" class="text-muted">
                                • {{ $d->produit->nom_produit ?? '?' }} × {{ $d->quantite_utilisee }}
                                <span class="text-primary">= {{ number_format($d->prix_total, 2) }} MAD</span>
                            </div>
                        @endforeach
                    </td>
                    <td class="text-muted" style="font-size:12px;">{{ $h->remarque ?? '—' }}</td>
                    <td class="text-end money fw-bold text-primary">{{ number_format($h->montant_total, 2) }} MAD</td>
                    <td class="text-center">
                        <a href="{{ route('historique.facture', $h) }}" class="btn btn-sm btn-light" target="_blank">
                            <i class="bi bi-printer"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-calendar-x" style="font-size:2.5rem;display:block;opacity:.3;margin-bottom:10px;"></i>
                        Aucun service pour {{ $nomMois }} {{ $annee }}.
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($historiques->count() > 0)
            <tfoot>
                <tr style="background: #f8fafc;">
                    <td colspan="5" class="text-end fw-bold">TOTAL DU MOIS :</td>
                    <td class="text-end money fw-bold text-primary" style="font-size:16px;">
                        {{ number_format($totalMois, 2) }} MAD
                    </td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection