@extends('layouts.app')
@section('title', 'Historique des Services')
@section('page-title', 'Historique des Services')

@section('content')
<div class="page-header">
    <div>
        <h1>Historique des Services</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active text-muted">Tous les services réalisés</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('historique.mensuel') }}" class="btn btn-light btn-sm">
            <i class="bi bi-calendar-month me-1"></i> Rapport Mensuel
        </a>
        <a href="{{ route('historique.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Nouveau Service
        </a>
    </div>
</div>

<!-- Filtres -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Client</label>
                <select name="client_id" class="form-select form-select-sm">
                    <option value="">Tous les clients</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id_client }}" {{ request('client_id') == $c->id_client ? 'selected' : '' }}>
                            {{ $c->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Mois</label>
                <select name="mois" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('mois') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->locale('fr')->monthName }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Année</label>
                <select name="annee" class="form-select form-select-sm">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ request('annee', now()->year) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Statut</label>
                <select name="statut" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    <option value="termine" {{ request('statut') == 'termine' ? 'selected' : '' }}>Terminé</option>
                    <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                    <option value="annule" {{ request('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
                    <a href="{{ route('historique.index') }}" class="btn btn-light btn-sm">Réinitialiser</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>{{ $historiques->total() }} service(s) trouvé(s)</span>
        @if($historiques->total() > 0)
        <span class="money fw-bold text-primary">Total : {{ number_format($totalFiltré, 2) }} MAD</span>
        @endif
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
                    <th>Statut</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($historiques as $h)
                <tr>
                    <td class="text-muted">#{{ $h->id_historique }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar" style="width:30px;height:30px;font-size:12px;">
                                {{ strtoupper(substr($h->client->nom ?? '?', 0, 1)) }}
                            </div>
                            <a href="{{ route('clients.show', $h->client) }}" class="text-decoration-none fw-semibold text-dark">
                                {{ $h->client->nom ?? 'N/A' }}
                            </a>
                        </div>
                    </td>
                    <td class="text-muted">{{ $h->date_service->format('d/m/Y H:i') }}</td>
                    <td>
                        @foreach($h->details->take(2) as $d)
                            <span class="badge bg-light text-dark border me-1" style="font-size:11px;">
                                {{ $d->produit->nom_produit ?? '?' }} ×{{ $d->quantite_utilisee }}
                            </span>
                        @endforeach
                        @if($h->details->count() > 2)
                            <span class="text-muted" style="font-size:11px;">+{{ $h->details->count() - 2 }} autre(s)</span>
                        @endif
                    </td>
                    <td class="text-muted" style="max-width:140px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $h->remarque ?? '—' }}
                    </td>
                    <td class="text-end money fw-semibold">{{ number_format($h->montant_total, 2) }} MAD</td>
                    <td>
                        <span class="badge bg-{{ $h->statut_badge }}-subtle text-{{ $h->statut_badge }}">
                            {{ $h->statut_label }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('historique.show', $h) }}" class="btn btn-sm btn-light" title="Voir">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('historique.facture', $h) }}" class="btn btn-sm btn-light" title="Imprimer facture" target="_blank">
                                <i class="bi bi-printer"></i>
                            </a>
                            <form method="POST" action="{{ route('historique.destroy', $h) }}"
                                  onsubmit="return confirm('Supprimer ce service ? Le stock sera restauré.')">
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
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox" style="font-size:2.5rem;display:block;opacity:.3;margin-bottom:10px;"></i>
                        Aucun service trouvé avec ces filtres.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($historiques->hasPages())
    <div class="card-body border-top py-3">
        {{ $historiques->links() }}
    </div>
    @endif
</div>
@endsection