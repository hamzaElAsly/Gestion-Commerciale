@extends('layouts.app')
@section('title', 'Modifier Vente #' . str_pad($vente->id_vente, 4, '0', STR_PAD_LEFT))
@section('page-title', 'Ventes')

@section('content')
<div class="page-header">
    <div>
        <h1>Modifier Vente #{{ str_pad($vente->id_vente, 4, '0', STR_PAD_LEFT) }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('vente.index') }}" class="text-muted">Ventes</a></li>
                <li class="breadcrumb-item">
                    <a href="{{ route('vente.show', $vente) }}" class="text-muted">
                        #{{ str_pad($vente->id_vente, 4, '0', STR_PAD_LEFT) }}
                    </a>
                </li>
                <li class="breadcrumb-item active text-muted">Modifier</li>
            </ol>
        </nav>
    </div>
</div>

{{-- Alerte info update --}}
<div class="alert d-flex gap-2 mb-4"
     style="background:#fefce8;border:1px solid #fde68a;border-radius:10px;color:#92400e;font-size:13.5px;">
    <i class="bi bi-info-circle-fill" style="flex-shrink:0;font-size:16px;margin-top:1px;"></i>
    <div>
        <strong>Modification du stock :</strong>
        L'ancien stock sera <strong>restauré</strong> automatiquement avant d'appliquer les nouvelles quantités.
    </div>
</div>

<form method="POST" action="{{ route('vente.update', $vente) }}" id="form-vente">
@csrf @method('PUT')

<div class="row g-4">

    {{-- ── Colonne gauche ── --}}
    <div class="col-lg-4">

        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-person-fill me-2 text-primary"></i>Informations
            </div>
            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label">Nom du client</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="nom_client"
                               class="form-control @error('nom_client') is-invalid @enderror"
                               value="{{ old('nom_client', $vente->nom_client) }}">
                    </div>
                    @error('nom_client')<div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Frais / service (MAD)</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-cash"></i></span>
                        <input type="number" name="charges" id="charges"
                               class="form-control @error('charges') is-invalid @enderror"
                               min="0" step="0.01"
                               value="{{ old('charges', $vente->charges) }}"
                               oninput="recalcTotal()">
                        <span class="input-group-text">MAD</span>
                    </div>
                    @error('charges')<div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>@enderror
                </div>

            </div>
        </div>

        {{-- Récapitulatif --}}
        <div class="card" style="border:2px solid var(--primary);background:var(--primary-light);">
            <div class="card-body">
                <div class="fw-bold text-primary mb-3">
                    <i class="bi bi-calculator me-2"></i>Récapitulatif
                </div>
                <div class="d-flex justify-content-between mb-2" style="font-size:13.5px;">
                    <span class="text-muted">Lignes :</span>
                    <span class="fw-semibold" id="recap-nb">0</span>
                </div>
                <div class="d-flex justify-content-between mb-2" style="font-size:13.5px;">
                    <span class="text-muted">Sous-total produits :</span>
                    <span class="fw-semibold money" id="recap-sous-total">0.00 MAD</span>
                </div>
                <div class="d-flex justify-content-between mb-3" style="font-size:13.5px;">
                    <span class="text-muted">Charges :</span>
                    <span class="fw-semibold money" id="recap-charges">0.00 MAD</span>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between">
                    <span class="fw-bold text-primary">Total TTC :</span>
                    <span class="fw-bold text-primary money" id="recap-total" style="font-size:16px;">0.00 MAD</span>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-3">
                    <i class="bi bi-check-circle-fill me-2"></i>Mettre à jour
                </button>
                <a href="{{ route('vente.show', $vente) }}" class="btn btn-light w-100 mt-2">
                    Annuler
                </a>
            </div>
        </div>

    </div>

    {{-- ── Colonne droite : lignes produits ── --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span>
                    <i class="bi bi-box-seam-fill me-2 text-primary"></i>
                    Produits  <span class="text-danger">*</span>
                    @error('produits')<div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>@enderror
                    @error('produits.*.quantite')<div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>@enderror
                    {{-- <span class="badge bg-light text-muted ms-1" style="font-size:11px;">Optionnel</span> --}}
                </span>
                <button type="button" class="btn btn-primary btn-sm" onclick="ajouterLigne()">
                    <i class="bi bi-plus-lg me-1"></i>Ajouter
                </button>
            </div>

            <div class="card-body">

                @if(session('error'))
                <div class="alert alert-danger d-flex gap-2 mb-3">
                    <i class="bi bi-x-circle-fill"></i> {{ session('error') }}
                </div>
                @endif

                <div id="lignes-container"></div>

                <div id="empty-msg" class="text-center py-4 text-muted" style="display:none;">
                    <i class="bi bi-box-seam" style="font-size:2rem;display:block;opacity:.25;margin-bottom:8px;"></i>
                    Aucun produit — vente de charges uniquement.
                </div>

            </div>
        </div>
    </div>

</div>
</form>

{{-- Template ligne --}}
<template id="tpl-ligne">
    <div class="produit-row border rounded-3 p-3 mb-2 bg-white" data-idx="__IDX__">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-semibold text-muted" style="font-size:12px;">
                Ligne <span class="num-ligne">__NUM__</span>
            </span>
            <button type="button" class="btn btn-sm btn-light text-danger p-1 lh-1"
                    onclick="supprimerLigne(this)">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label">Produit</label>
                <select name="produits[__IDX__][id_produit]"
                        class="form-select select-produit"
                        onchange="onSelectProduit(this)" required>
                    <option value="">— Choisir —</option>
                    @foreach($produits as $p)
                    <option value="{{ $p->id_produit }}"
                            data-prix="{{ $p->prix_vente }}"
                            data-stock="{{ $p->quantite_stock }}"
                            data-statut="{{ $p->statut_stock }}"
                            data-selected-stock="{{ $p->quantite_stock }}">
                        {{ $p->nom_produit }}
                        @if($p->quantite_stock == 0) — ❌ Rupture
                        @elseif($p->stock_faible)    — ⚠ {{ $p->quantite_stock }} dispo
                        @else                         — {{ $p->quantite_stock }} dispo
                        @endif
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Quantité</label>
                <input type="number" name="produits[__IDX__][quantite]"
                       class="form-control input-qte"
                       min="1" value="1" oninput="recalcLigne(this)" required>
                <div class="stock-hint mt-1" style="font-size:11px;"></div>
            </div>
            <div class="col-md-2">
                <label class="form-label">Prix unit.</label>
                {{-- <div class="form-control bg-light text-end money input-prix">—</div> --}}
                <input type="number" name="produits[__IDX__][prixVente]"
                       class="form-control input-prix"
                       min="0" step="0.01" value="0" oninput="recalcLigne(this)" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Total</label>
                <div class="form-control bg-light fw-bold text-end money text-primary input-total">0.00</div>
            </div>
        </div>
    </div>
</template>
@endsection

@php
    $existingLines = $vente->details->map(function ($d) {
        return [
            'id_produit'  => $d->id_produit,
            'quantite'    => $d->quantite,
            'prix_vente'  => $d->prix_vente,
            'prix_total'  => $d->prix_total,
            'nom_produit' => $d->nom_produit,
        ];
    })->values()->toArray();
@endphp

@push('scripts')
<script>

    const existingLines = {!! json_encode(
        $vente->details->map(function ($d) {
            return [
                'id_produit'  => $d->id_produit,
                'nom_produit' => $d->nom_produit,
                'quantite'    => $d->quantite,
                'prix_vente'  => $d->prix_vente,
                'prix_total'  => $d->prix_total,
            ];
        })->values()
    ) !!};

    let compteur = 0;

    /* ───────────────────────────── */
    /* Ajouter ligne */
    /* ───────────────────────────── */

    function ajouterLigne(idProduit = null, qte = 1, prixVente = null)
    {
        const tpl = document.getElementById('tpl-ligne').innerHTML;

        const idx = compteur++;

        const num = document.querySelectorAll('.produit-row').length + 1;

        const html = tpl
            .replaceAll('__IDX__', idx)
            .replaceAll('__NUM__', num);

        const wrap = document.createElement('div');

        wrap.innerHTML = html;

        const row = wrap.firstElementChild;

        document
            .getElementById('lignes-container')
            .appendChild(row);

        document.getElementById('empty-msg').style.display = 'none';

        if (idProduit) {

            const sel = row.querySelector('.select-produit');

            sel.value = idProduit;

            onSelectProduit(sel);

            row.querySelector('.input-qte').value = qte;

            if (prixVente !== null) {
                row.querySelector('.input-prix').value = prixVente;
            }

            recalcLigne(row.querySelector('.input-qte'));
        }

        recalcTotal();
    }

    /* ───────────────────────────── */
    /* Supprimer ligne */
    /* ───────────────────────────── */

    function supprimerLigne(btn)
    {
        btn.closest('.produit-row').remove();

        renumeroter();

        if (!document.querySelectorAll('.produit-row').length) {
            document.getElementById('empty-msg').style.display = '';
        }

        recalcTotal();
    }

    /* ───────────────────────────── */

    function renumeroter()
    {
        document
            .querySelectorAll('.produit-row .num-ligne')
            .forEach((el, i) => {
                el.textContent = i + 1;
            });
    }

    /* ───────────────────────────── */
    /* Choix produit */
    /* ───────────────────────────── */

    function onSelectProduit(sel)
    {
        const row = sel.closest('.produit-row');

        const opt = sel.selectedOptions[0];

        const hint = row.querySelector('.stock-hint');

        const qteEl = row.querySelector('.input-qte');

        const prixEl = row.querySelector('.input-prix');

        if (!opt.value) {

            hint.innerHTML = '';

            prixEl.value = '';

            row.querySelector('.input-total').textContent = '0.00';

            recalcTotal();

            return;
        }

        const stock  = parseInt(opt.dataset.stock || 0);

        const prix   = parseFloat(opt.dataset.prix || 0);

        const statut = opt.dataset.statut;

        const colors = {
            normal: '#059669',
            faible: '#d97706',
            rupture: '#dc2626'
        };

        const icons = {
            normal: '✓',
            faible: '⚠',
            rupture: '✗'
        };

        hint.innerHTML = `
            <span style="color:${colors[statut]};">
                ${icons[statut]} Stock :
                <strong>${stock}</strong>
            </span>
        `;

        if (stock === 0) {

            qteEl.value = 0;
            qteEl.disabled = true;
            qteEl.max = 0;

        } else {

            qteEl.disabled = false;
            qteEl.max = stock;

            qteEl.value = Math.min(
                parseInt(qteEl.value) || 1,
                stock
            );
        }

        /* remplir prix seulement si vide */
        if (!prixEl.value || parseFloat(prixEl.value) === 0) {
            prixEl.value = prix.toFixed(2);
        }

        recalcLigne(prixEl);
    }

    /* ───────────────────────────── */
    /* Calcul ligne */
    /* ───────────────────────────── */

    function recalcLigne(element)
    {
        const row = element.closest('.produit-row');

        const prix =
            parseFloat(
                row.querySelector('.input-prix').value
            ) || 0;

        const qte =
            parseFloat(
                row.querySelector('.input-qte').value
            ) || 0;

        row.querySelector('.input-total').textContent =
            (prix * qte).toFixed(2);

        recalcTotal();
    }

    /* ───────────────────────────── */
    /* Calcul total */
    /* ───────────────────────────── */

    function recalcTotal()
    {
        let sousTot = 0;

        document
            .querySelectorAll('.produit-row')
            .forEach(row => {

                sousTot += parseFloat(
                    row.querySelector('.input-total').textContent
                ) || 0;

            });

        const charges =
            parseFloat(
                document.getElementById('charges').value
            ) || 0;

        const nb =
            document.querySelectorAll('.produit-row').length;

        document.getElementById('recap-nb').textContent =
            nb;

        document.getElementById('recap-sous-total').textContent =
            sousTot.toFixed(2) + ' MAD';

        document.getElementById('recap-charges').textContent =
            charges.toFixed(2) + ' MAD';

        document.getElementById('recap-total').textContent =
            (sousTot + charges).toFixed(2) + ' MAD';
    }

    /* ───────────────────────────── */
    /* Initialisation */
    /* ───────────────────────────── */

    document.addEventListener('DOMContentLoaded', () => {

        if (existingLines.length === 0) {

            document.getElementById('empty-msg').style.display = '';

        } else {

            existingLines.forEach(line => {

                ajouterLigne(
                    line.id_produit,
                    line.quantite,
                    line.prix_vente
                );

            });
        }

        recalcTotal();
    });

</script>
@endpush