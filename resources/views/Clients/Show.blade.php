@extends('layouts.app')
@section('title', $client->nom)
@section('page-title', 'Clients')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $client->nom }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('clients.index') }}" class="text-muted">Clients</a></li>
                <li class="breadcrumb-item active text-muted">{{ $client->nom }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('historique.create') }}?client={{ $client->id_client }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Nouveau Service
        </a>
        <a href="{{ route('clients.edit', $client) }}" class="btn btn-light btn-sm">
            <i class="bi bi-pencil me-1"></i> Modifier
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body text-center py-4">
                <div class="avatar mx-auto mb-3" style="width:64px;height:64px;font-size:24px;">
                    {{ strtoupper(substr($client->nom, 0, 1)) }}
                </div>
                <h4 class="fw-bold mb-1">{{ $client->nom }}</h4>
                @if($client->telephone)
                    <p class="text-muted mb-1"><i class="bi bi-telephone me-1"></i>{{ $client->telephone }}</p>
                @endif
                @if($client->adresse)
                    <p class="text-muted mb-0"><i class="bi bi-geo-alt me-1"></i>{{ $client->adresse }}</p>
                @endif
            </div>
        </div>

        <div class="row g-3">
            <div class="col-6">
                <div class="stat-card">
                    <div>
                        <div class="stat-value">{{ $client->historiques_count }} 0</div>
                        <div class="stat-label">Services</div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card">
                    <div>
                        <div class="stat-value" style="font-size:18px;">{{ number_format($client->total_depense ?? 0, 0) }}</div>
                        <div class="stat-label">Total MAD</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history me-2 text-primary"></i>Historique des Services
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Produits</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historiques as $h)
                        <tr>
                            <td class="text-muted">#{{ $h->id_historique }}</td>
                            <td>{{ $h->date_service->format('d/m/Y') }}</td>
                            <td>
                                @foreach($h->details->take(2) as $d)
                                    <span class="badge bg-light text-dark me-1">{{ $d->produit->nom_produit ?? '?' }}</span>
                                @endforeach
                                @if($h->details->count() > 2)
                                    <span class="text-muted">+{{ $h->details->count() - 2 }}</span>
                                @endif
                            </td>
                            <td class="money fw-semibold">{{ number_format($h->montant_total, 2) }} MAD</td>
                            <td>
                                <span class="badge bg-{{ $h->statut_badge }}-subtle text-{{ $h->statut_badge }}">
                                    {{ $h->statut_label }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('historique.show', $h) }}" class="btn btn-sm btn-light">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Aucun service enregistré pour ce client.
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
    </div>
</div>
@endsection