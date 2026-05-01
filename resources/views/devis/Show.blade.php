@extends('layouts.app')
@section('title', $devis->nom_client)
@section('page-title', 'Devis #' . $devis->id_devis)

@section('content')
<div class="page-header">
    <div class="container mt-4">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>📄 Devis #{{ $devis->id_devis }}</h3>

            <div>
                <a href="{{ route('devis.print', $devis->id_devis) }}" class="btn btn-danger">
                    🧾 PDF
                </a>
                <a href="{{ route('devis.index') }}" class="btn btn-secondary">
                    ← Retour
                </a>
            </div>
        </div>

        <!-- INFO -->
        <div class="card p-3 mb-3">
            <div class="row">
                <div class="col-md-6">
                    <h5>Client</h5>
                    <p><strong>{{ $devis->nom_client }}</strong></p>
                </div>

                <div class="col-md-6 text-end">
                    <h5>Date</h5>
                    <p>{{ $devis->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <!-- TABLE PRODUITS -->
        <div class="card p-3">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Produit</th>
                        <th>Prix</th>
                        <th>Quantité</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($devis->details as $index => $detail)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $detail->produit->nom_produit ?? 'N/A' }}</td>
                        <td>{{ number_format($detail->prix_vente, 2) }} MAD</td>
                        <td>{{ $detail->quantite }}</td>
                        <td><strong>{{ number_format($detail->prix_total, 2) }} MAD</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- TOTAL -->
        <div class="card p-3 mt-3 text-end">
            <h4>Total : <span class="text-success">{{ number_format($devis->montant_total, 2) }} MAD</span></h4>
        </div>
    </div>
</div>
@endsection