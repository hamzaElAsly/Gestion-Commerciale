@extends('layouts.app')
@section('title', 'Nouveau Service')
@section('page-title', 'Historique des Services')

@section('content')
<div class="page-header">
    <div>
        <h1>Enregistrer un Service</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('historique.index') }}" class="text-muted">Historique</a></li>
                <li class="breadcrumb-item active text-muted">Nouveau Service</li>
            </ol>
        </nav>
    </div>
</div>

<form method="POST" action="{{ route('historique.store') }}" id="service-form">
@csrf

<div class="row g-4">

    <!-- ===== INFOS GÉNÉRALES ===== -->
    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-info-circle-fill me-2 text-primary"></i> Informations du service
            </div>
            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label">Client <span class="text-danger">*</span></label>
                    <select name="id_client" class="form-select @error('id_client') is-invalid @enderror" required>
                        <option value="">— Sélectionner un client —</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id_client }}"
                                {{ (old('id_client', request('client')) == $client->id_client) ? 'selected' : '' }}>
                                {{ $client->nom }}
                                @if($client->telephone) — {{ $client->telephone }}@endif
                            </option>
                        @endforeach
                    </select>
                    @error('id_client')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Date du service <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="date_service" class="form-control @error('date_service') is-invalid @enderror"
                           value="{{ old('date_service', now()->format('Y-m-d\TH:i')) }}" required>
                    @error('date_service')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="termine" {{ old('statut') == 'termine' ? 'selected' : '' }}>✅ Terminé</option>
                        <option value="en_cours" {{ old('statut') == 'en_cours' ? 'selected' : '' }}>🔄 En cours</option>
                        <option value="annule" {{ old('statut') == 'annule' ? 'selected' : '' }}>❌ Annulé</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Remarque</label>
                    <textarea name="remarque" class="form-control" rows="3"
                              placeholder="Notes optionnelles sur le service...">{{ old('remarque') }}</textarea>
                </div>
            </div>
        </div>

        <!-- ===== RÉCAPITULATIF ===== -->
        <div class="card" style="border: 2px solid var(--primary); background: var(--primary-light);">
            <div class="card-body">
                <div class="fw-bold text-primary mb-3"><i class="bi bi-calculator me-2"></i>Récapitulatif</div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Nombre de produits :</span>
                    <span class="fw-bold" id="recap-nb-produits">0</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Montant total :</span>
                    <span class="fw-bold text-primary money" id="recap-total">0.00 MAD</span>
                </div>
                <hr>
                <button type="submit" class="btn btn-primary w-100" id="btn-submit">
                    <i class="bi bi-check-circle-fill me-2"></i>Enregistrer le Service
                </button>
            </div>
        </div>
    </div>

    <!-- ===== PRODUITS UTILISÉS ===== -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-box-seam-fill me-2 text-primary"></i>Produits Utilisés</span>
                <button type="button" class="btn btn-primary btn-sm" onclick="ajouterProduit()">
                    <i class="bi bi-plus-lg me-1"></i> Ajouter un produit
                </button>
            </div>
            <div class="card-body">
                <div id="produits-container"></div>
                <div id="empty-msg" class="text-center text-muted py-5">
                    <i class="bi bi-box-seam" style="font-size:2.5rem;display:block;opacity:.3;margin-bottom:10px;"></i>
                    Aucun produit ajouté. Cliquez sur "Ajouter un produit".
                </div>
                @error('produits')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

</div>
</form>

<!-- Template produit (caché) -->
<template id="produit-template">
    <div class="produit-row" data-index="__INDEX__">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="fw-semibold" style="font-size:13px;color:#64748b;">Produit #<span class="num-row">__NUM__</span></div>
            <button type="button" class="btn btn-sm btn-light text-danger p-1" onclick="supprimerProduit(this)">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label">Produit</label>
                <select name="produits[__INDEX__][id_produit]" class="form-select produit-select" required onchange="onProduitChange(this)">
                    <option value="">— Choisir —</option>
                    @foreach($produits as $p)
                        <option value="{{ $p->id_produit }}"
                                data-prix="{{ $p->prix_unitaire }}"
                                data-stock="{{ $p->quantite_stock }}"
                                data-statut="{{ $p->statut_stock }}">
                            {{ $p->nom_produit }} (stock: {{ $p->quantite_stock }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Quantité</label>
                <input type="number" name="produits[__INDEX__][quantite]" class="form-control produit-qte"
                       min="1" value="1" required onchange="calculerLigne(this)">
                <div class="stock-info mt-1" style="font-size:11.5px;"></div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Prix total</label>
                <div class="form-control bg-light text-end money fw-semibold ligne-total">0.00</div>
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
let compteur = 0;

function ajouterProduit() {
    const template = document.getElementById('produit-template').innerHTML;
    const idx = compteur++;
    const html = template.replace(/__INDEX__/g, idx).replace(/__NUM__/g, document.querySelectorAll('.produit-row').length + 1);
    
    const container = document.getElementById('produits-container');
    const div = document.createElement('div');
    div.innerHTML = html;
    container.appendChild(div.firstElementChild);
    
    document.getElementById('empty-msg').style.display = 'none';
    mettreAJourRecap();
}

function supprimerProduit(btn) {
    btn.closest('.produit-row').remove();
    renuméroterLignes();
    if (document.querySelectorAll('.produit-row').length === 0) {
        document.getElementById('empty-msg').style.display = '';
    }
    mettreAJourRecap();
}

function renuméroterLignes() {
    document.querySelectorAll('.produit-row').forEach((row, i) => {
        row.querySelector('.num-row').textContent = i + 1;
    });
}

function onProduitChange(select) {
    const option = select.selectedOptions[0];
    const row = select.closest('.produit-row');
    const stockInfo = row.querySelector('.stock-info');
    const qteInput = row.querySelector('.produit-qte');
    
    if (option.value) {
        const stock = parseInt(option.dataset.stock);
        const statut = option.dataset.statut;
        const colors = { normal: 'text-success', faible: 'text-warning', rupture: 'text-danger' };
        const icons = { normal: '✓', faible: '⚠', rupture: '✗' };
        
        stockInfo.innerHTML = `<span class="${colors[statut] || ''}">
            ${icons[statut] || ''} Stock disponible : <strong>${stock}</strong>
        </span>`;
        
        qteInput.max = stock;
        if (stock === 0) {
            qteInput.value = 0;
            qteInput.disabled = true;
        } else {
            qteInput.disabled = false;
        }
    } else {
        stockInfo.innerHTML = '';
    }
    
    calculerLigne(qteInput);
}

function calculerLigne(input) {
    const row = input.closest('.produit-row');
    const select = row.querySelector('.produit-select');
    const qte = parseFloat(input.value) || 0;
    const option = select.selectedOptions[0];
    const prix = option ? parseFloat(option.dataset.prix || 0) : 0;
    const total = qte * prix;
    
    row.querySelector('.ligne-total').textContent = total.toFixed(2);
    mettreAJourRecap();
}

function mettreAJourRecap() {
    const rows = document.querySelectorAll('.produit-row');
    let grandTotal = 0;
    
    rows.forEach(row => {
        const val = parseFloat(row.querySelector('.ligne-total').textContent) || 0;
        grandTotal += val;
    });
    
    document.getElementById('recap-nb-produits').textContent = rows.length;
    document.getElementById('recap-total').textContent = grandTotal.toFixed(2) + ' MAD';
}

// Ajouter une ligne par défaut
document.addEventListener('DOMContentLoaded', function() {
    ajouterProduit();
});
</script>
@endpush