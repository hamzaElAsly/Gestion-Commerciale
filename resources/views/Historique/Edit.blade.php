@extends('layouts.app')
@section('title', 'Modifier Service #' . $historique->id_historique)
@section('page-title', 'Historique des Services')

@section('content')
<div class="page-header">
    <div>
        <h1>Modifier Service #{{ $historique->id_historique }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('historique.index') }}" class="text-muted">Historique</a></li>
                <li class="breadcrumb-item active text-muted">Modifier</li>
            </ol>
        </nav>
    </div>
</div>

<form method="POST" action="{{ route('historique.update', $historique) }}">
@csrf
@method('PUT')

<div class="row g-4">

    <!-- ===== INFOS ===== -->
    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header">
                Informations du service
            </div>
            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label">Client</label>
                    <input type="text" class="form-control"
                        value="{{ $historique->client->nom }}" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">Date</label>
                    <input type="text" class="form-control"
                        value="{{ $historique->date_service->format('d/m/Y H:i') }}" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="termine" {{ $historique->statut == 'termine' ? 'selected' : '' }}>✅ Terminé</option>
                        <option value="en_cours" {{ $historique->statut == 'en_cours' ? 'selected' : '' }}>🔄 En cours</option>
                        <option value="annule" {{ $historique->statut == 'annule' ? 'selected' : '' }}>❌ Annulé</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Remarque</label>
                    <textarea name="remarque" class="form-control" rows="3">{{ old('remarque', $historique->remarque) }}</textarea>
                </div>

                <div class="card mt-3">
                    <div class="card-body d-flex justify-content-between">
                        <strong>Total Produits:</strong>
                        <span id="recap-total">0 MAD</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ===== PRODUITS ===== -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                Produits
            </div>

            <div class="card-body">

                <!-- HEADER -->
                <div class="row fw-bold mb-2">
                    <div class="col-md-4">Produit</div>
                    <div class="col-md-3">Qté</div>
                    {{-- <div class="col-md-2">Prix</div> --}}
                    <div class="col-md-3">Total</div>
                    <div class="col-md-2"></div>
                </div>

                <!-- LOOP PRODUITS -->
                <div id="produits-container">

                    @foreach($historique->details as $index => $detail)
                    <div class="row mb-2 produit-row">

                        <!-- Produit -->
                        <div class="col-md-4">
                            <select name="produits[{{ $index }}][id_produit]" 
                                    class="form-select produit-select">
                                @foreach($produits as $produit)
                                    <option value="{{ $produit->id_produit }}"
                                            data-prix="{{ $produit->prix_vente }}"
                                            {{ $produit->id_produit == $detail->id_produit ? 'selected' : '' }}>
                                        {{ $produit->nom_produit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Quantité -->
                        <div class="col-md-3">
                            <input type="number"
                                name="produits[{{ $index }}][quantite]"
                                class="form-control produit-quantite"
                                value="{{ $detail->quantite_utilisee }}"
                                min="1">
                        </div>

                        {{-- <!-- Prix -->
                        <div class="col-md-2">
                            <input type="text"
                                class="form-control prix-vente"
                                value="{{ $detail->prix_vente }}"
                                readonly>
                        </div> --}}

                        <!-- Total -->
                        <div class="col-md-3">
                            <input type="text"
                                class="form-control prix-total"
                                value="{{ $detail->prix_total }}"
                                readonly>
                        </div>

                        <!-- Delete -->
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-produit">❌</button>
                        </div>

                    </div>
                    @endforeach

                </div>

                <!-- ADD BTN -->
                <button type="button" class="btn btn-success mt-2" id="add-produit">
                    ➕ Ajouter produit
                </button>

            </div>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        💾 Enregistrer
    </button>

    <a href="{{ route('historique.show', $historique) }}" class="btn btn-light">
        Annuler
    </a>
</div>

</form>

<template id="produit-template">
    <div class="row mb-2 produit-row">

        <div class="col-md-4">
            <select class="form-select produit-select">
                <option value="">__ Choisir __</option>
                @foreach($produits as $produit)
                    <option value="{{ $produit->id_produit }}"
                            data-prix="{{ $produit->prix_vente }}">
                        {{ $produit->nom_produit }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <input type="number" class="form-control produit-quantite" value="1" min="1">
        </div>

        <div class="col-md-3">
            <input type="text" class="form-control prix-total" readonly>
        </div>

        <div class="col-md-2">
            <button type="button" class="btn btn-danger remove-produit">❌</button>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
    let index = {{ count($historique->details) }};

    // ================== UPDATE ROW ==================
    function updateRow(row) {
        let select = row.querySelector('.produit-select');
        let quantite = row.querySelector('.produit-quantite');
        let prixTotal = row.querySelector('.prix-total');

        if (!select || !quantite || !prixTotal) return;

        let selectedOption = select.options[select.selectedIndex];
        let prix = parseFloat(selectedOption?.dataset?.prix || 0);
        let qte = parseInt(quantite.value || 0);

        let total = prix * qte;

        prixTotal.value = total.toFixed(2);

        mettreAJourRecap();
    }

    // ================== TOTAL GLOBAL ==================
    function mettreAJourRecap() {
        let total = 0;

        document.querySelectorAll('.prix-total').forEach(input => {
            total += parseFloat(input.value || 0);
        });

        document.getElementById('recap-total').textContent = total.toFixed(2) + ' MAD';
    }

    // ================== ADD PRODUIT ==================
    document.getElementById('add-produit')?.addEventListener('click', function () {

        let template = document.getElementById('produit-template').content.cloneNode(true);
        let row = template.querySelector('.produit-row');

        let select = row.querySelector('.produit-select');
        let input = row.querySelector('.produit-quantite');

        select.name = `produits[${index}][id_produit]`;
        input.name = `produits[${index}][quantite]`;

        document.getElementById('produits-container').appendChild(row);

        updateRow(row); // init

        index++;
    });

    // ================== EVENTS ==================
    document.addEventListener('change', function(e){
        if (
            e.target.classList.contains('produit-select') ||
            e.target.classList.contains('produit-quantite')
        ) {
            let row = e.target.closest('.produit-row');
            if (row) updateRow(row);
        }
    });

    // supprimer produit
    document.addEventListener('click', function(e){
        if (e.target.classList.contains('remove-produit')) {
            let row = e.target.closest('.produit-row');
            if (row) {
                row.remove();
                mettreAJourRecap();
            }
        }
    });

    // ================== INIT ==================
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.produit-row').forEach(row => {
            updateRow(row);
        });
    });
</script>
@endpush