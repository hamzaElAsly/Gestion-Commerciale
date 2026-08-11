@extends('layouts.app')
@section('title', $titreRapport)
@section('page-title', 'Rapport des Services')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $titreRapport }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active text-muted">{{ $titreRapport }}</li>
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
            <div class="col-md-2">
                <label class="form-label">Année</label>
                <select name="annee" class="form-select form-select-sm" required>
                    @for($y = now()->year + 1; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ $annee == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Mois</label>
                <select name="mois" id="rapport-mois" class="form-select form-select-sm">
                    <option value="">Tous les mois</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ (int) $mois === $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->locale('fr')->monthName }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Jour</label>
                <select name="jour" id="rapport-jour" class="form-select form-select-sm" data-selected="{{ $jour }}">
                    <option value="">Tous les jours</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Client (optionnel)</label>
                <select name="id_client" class="form-select form-select-sm">
                    <option value="">Tous les clients</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id_client }}" {{ (string) $idClient === (string) $c->id_client ? 'selected' : '' }}>
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
<div class="container-fluid mb-4">

    <!-- Ligne 1 -->
    <div class="row g-3 mb-2 d-flex justify-content-center">
        <div class="col-md-4">
            <div class="stat-card h-100">
                <div class="stat-icon blue"><i class="bi bi-calendar-check"></i></div>
                <div>
                    <div class="stat-value">{{ $historiques->count() }}</div>
                    <div class="stat-label">Services filtrés</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card h-100">
                <div class="stat-icon purple"><i class="bi bi-people"></i></div>
                <div>
                    <div class="stat-value">{{ $historiques->unique('id_client')->count() }}</div>
                    <div class="stat-label">Clients servis</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ligne 2 -->
    <div class="row g-3">
        <div class="col-md-4">
            <div class="stat-card h-100">
                <div class="stat-icon green"><i class="bi bi-graph-up-arrow"></i></div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ number_format($totalVenteMois, 2) }}</div>
                    <div class="stat-label">CA TTC (MAD)</div>
                    <small class="text-muted">HT {{ number_format($totalHt, 2) }} · TVA {{ number_format($totalTva, 2) }}</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card h-100">
                <div class="stat-icon orange">
                    <i class="bi bi-cart-dash"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size:20px;">
                        {{ number_format($totalAchatMois, 2) }}
                    </div>
                    <div class="stat-label">Coût Total (MAD)</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card h-100">
                <div class="stat-icon success">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size:20px;">
                        {{ number_format($totalVenteMois - $totalAchatMois, 2) }}
                    </div>
                    <div class="stat-label">Profit (MAD)</div>
                </div>
            </div>
        </div>

    </div>

</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-table me-2 text-primary"></i>
        <strong>{{ $titreRapport }}</strong>
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
                        Aucun service pour la période sélectionnée.
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($historiques->count() > 0)
            <tfoot>
                <tr style="background: #f8fafc;">
                    <td colspan="5" class="text-end fw-bold">TOTAL TTC :</td>
                    <td class="text-end money fw-bold text-primary" style="font-size:16px;">
                        {{ number_format($totalVenteMois, 2) }} MAD
                    </td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function mettreAJourJoursRapport() {
        const mois = document.getElementById('rapport-mois');
        const jour = document.getElementById('rapport-jour');
        const annee = document.querySelector('select[name="annee"]');
        const selection = jour.dataset.selected || jour.value;
        jour.innerHTML = '<option value="">Tous les jours</option>';
        if (!mois.value) {
            jour.disabled = true;
            jour.value = '';
            return;
        }
        jour.disabled = false;
        const nombreJours = new Date(Number(annee.value), Number(mois.value), 0).getDate();
        for (let numero = 1; numero <= nombreJours; numero++) {
            const option = new Option(numero, numero, false, String(numero) === String(selection));
            jour.add(option);
        }
        jour.dataset.selected = '';
    }
    document.getElementById('rapport-mois').addEventListener('change', mettreAJourJoursRapport);
    document.querySelector('select[name="annee"]').addEventListener('change', mettreAJourJoursRapport);
    document.addEventListener('DOMContentLoaded', mettreAJourJoursRapport);
</script>
@endpush
