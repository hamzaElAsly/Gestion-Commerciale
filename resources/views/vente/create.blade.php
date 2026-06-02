@extends('layouts.app')
@section('title', 'Nouvelle Vente')
@section('page-title', 'Ventes')

@section('content')
<div class="page-header">
    <div>
        <h1>Nouvelle Vente</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('vente.index') }}" class="text-muted">Ventes</a></li>
                <li class="breadcrumb-item active text-muted">Nouvelle</li>
            </ol>
        </nav>
    </div>
</div>

<form method="POST" action="{{ route('vente.store') }}" id="form-vente">
@csrf

<div class="row g-4">

    {{-- ── Colonne gauche : infos générales + récapitulatif ── --}}
    <div class="col-lg-4">

        {{-- Infos générales --}}
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
                               placeholder="Tapez nom du client"
                               value="{{ old('nom_client') }}" autofocus>
                    </div>
                    @error('nom_client')<div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Frais / Service (MAD)
                        <i class="bi bi-info-circle text-muted ms-1"
                           title="Frais de déplacement, main d'œuvre, etc."
                           data-bs-toggle="tooltip"></i>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-cash"></i></span>
                        <input type="number" name="charges" id="charges"
                               class="form-control @error('charges') is-invalid @enderror"
                               min="0" step="0.01" placeholder="0.00"
                               value="{{ old('charges', 0) }}"
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
                    <span class="text-muted">Lignes produits :</span>
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
                    <i class="bi bi-check-circle-fill me-2"></i>Enregistrer la vente
                </button>
            </div>
        </div>

    </div>

    {{-- ── Colonne droite : lignes produits (optionnelles) ── --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span>
                    <i class="bi bi-box-seam-fill me-2 text-primary"></i>
                    Produits utilisés <span class="text-danger">*</span> 
                    @error('produits')<div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>@enderror
                    @error('produits.*.quantite')<div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>@enderror
                    {{-- <span class="badge bg-light text-muted ms-2" style="font-size:11px;">Optionnel</span> --}}
                </span>
                <button type="button" class="btn btn-primary btn-sm" onclick="ajouterLigne()">
                    <i class="bi bi-plus-lg me-1"></i>Ajouter un produit
                </button>
            </div>

            <div class="card-body">

                {{-- Message erreur global --}}
                @if(session('error'))
                <div class="alert alert-danger d-flex gap-2 mb-3">
                    <i class="bi bi-x-circle-fill"></i> {{ session('error') }}
                </div>
                @endif

                {{-- Zone lignes --}}
                <div id="lignes-container"></div>

                {{-- Placeholder vide --}}
                <div id="empty-msg" class="text-center py-5 text-muted">
                    <i class="bi bi-box-seam" style="font-size:2.5rem;display:block;opacity:.25;margin-bottom:10px;"></i>
                    <div style="font-size:13.5px;">Aucun produit ajouté.</div>
                    <div style="font-size:12px;margin-top:4px;color:#94a3b8;">
                        La vente ne sera pas enregistrée avec les charges uniquement.
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
</form>

{{-- ═══ TEMPLATE LIGNE (masqué) ═══ --}}
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
            {{-- Sélecteur produit --}}
            <div class="col-md-6">
                <label class="form-label">Produit</label>
                <select name="produits[__IDX__][id_produit]"
                        class="form-select select-produit"
                        onchange="onSelectProduit(this)" required>
                    <option value="">— Choisir un produit —</option>
                    @foreach($produits as $p)
                    <option value="{{ $p->id_produit }}"
                            data-prix="{{ $p->prix_vente }}"
                            data-stock="{{ $p->quantite_stock }}"
                            data-statut="{{ $p->statut_stock }}"
                            {{ $p->quantite_stock == 0 ? 'data-rupture=1' : '' }}>
                        {{ $p->nom_produit }}
                        @if($p->quantite_stock == 0) — ❌ Rupture
                        @elseif($p->stock_faible)    — ⚠ Stock faible ({{ $p->quantite_stock }})
                        @else                        — ({{ $p->quantite_stock }} dispo)
                        @endif
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Quantité --}}
            <div class="col-md-2">
                <label class="form-label">Quantité</label>
                <input type="number" name="produits[__IDX__][quantite]"
                       class="form-control input-qte"
                       min="1" value="1"
                       oninput="recalcLigne(this)" required>
                <div class="stock-hint mt-1" style="font-size:11px;"></div>
            </div>

            {{-- Prix unitaire --}}
            <div class="col-md-2">
                <label class="form-label">Prix unit.</label>
                <div class="form-control bg-light text-end money input-prix">—</div>
            </div>

            {{-- Total ligne --}}
            <div class="col-md-2">
                <label class="form-label">Total ligne</label>
                <div class="form-control bg-light fw-bold text-end money text-primary input-total">0.00</div>
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
/* ═══════════════════════════════════════════
   GESTION DYNAMIQUE DES LIGNES PRODUITS
   ═══════════════════════════════════════════ */
let compteur = 0;

function ajouterLigne() {
    const tpl  = document.getElementById('tpl-ligne').innerHTML;
    const idx  = compteur++;
    const num  = document.querySelectorAll('.produit-row').length + 1;
    const html = tpl.replaceAll('__IDX__', idx).replaceAll('__NUM__', num);

    const wrap = document.createElement('div');
    wrap.innerHTML = html;
    document.getElementById('lignes-container').prepend(wrap.firstElementChild);

    document.getElementById('empty-msg').style.display = 'none';
    recalcTotal();
}

function supprimerLigne(btn) {
    btn.closest('.produit-row').remove();
    renuméroter();
    const restant = document.querySelectorAll('.produit-row').length;
    if (restant === 0) document.getElementById('empty-msg').style.display = '';
    recalcTotal();
}

function renuméroter() {
    document.querySelectorAll('.produit-row .num-ligne').forEach((el, i) => {
        el.textContent = i + 1;
    });
}

/* ─── Sélection d'un produit ─── */
function onSelectProduit(sel) {
    const row   = sel.closest('.produit-row');
    const opt   = sel.selectedOptions[0];
    const hint  = row.querySelector('.stock-hint');
    const qteEl = row.querySelector('.input-qte');
    const prixEl= row.querySelector('.input-prix');

    if (!opt.value) {
        hint.innerHTML = '';
        prixEl.textContent = '—';
        row.querySelector('.input-total').textContent = '0.00';
        recalcTotal();
        return;
    }

    const stock  = parseInt(opt.dataset.stock);
    const prix   = parseFloat(opt.dataset.prix);
    const statut = opt.dataset.statut;

    // Afficher le stock disponible
    const couleurs = { normal: '#059669', faible: '#d97706', rupture: '#dc2626' };
    const icones   = { normal: '✓', faible: '⚠', rupture: '✗' };
    hint.innerHTML = `<span style="color:${couleurs[statut]||'#64748b'};">
        ${icones[statut]||''} Stock : <strong>${stock}</strong>
    </span>`;

    // Bloquer si rupture
    if (stock === 0) {
        qteEl.value    = 0;
        qteEl.disabled = true;
        qteEl.max      = 0;
    } else {
        qteEl.disabled = false;
        qteEl.max      = stock;
        qteEl.value    = Math.min(parseInt(qteEl.value) || 1, stock);
    }

    prixEl.textContent = prix.toFixed(2);
    recalcLigne(qteEl);
}

/* ─── Recalcul d'une ligne ─── */
function recalcLigne(qteInput) {
    const row   = qteInput.closest('.produit-row');
    const sel   = row.querySelector('.select-produit');
    const opt   = sel.selectedOptions[0];
    const prix  = opt && opt.value ? parseFloat(opt.dataset.prix) : 0;
    const qte   = parseFloat(qteInput.value) || 0;
    row.querySelector('.input-total').textContent = (prix * qte).toFixed(2);
    recalcTotal();
}

/* ─── Recalcul du récapitulatif ─── */
function recalcTotal() {
    let sousTot = 0;
    document.querySelectorAll('.produit-row').forEach(row => {
        sousTot += parseFloat(row.querySelector('.input-total').textContent) || 0;
    });

    const charges = parseFloat(document.getElementById('charges').value) || 0;
    const total   = sousTot + charges;
    const nb      = document.querySelectorAll('.produit-row').length;

    document.getElementById('recap-nb').textContent         = nb;
    document.getElementById('recap-sous-total').textContent = sousTot.toFixed(2) + ' MAD';
    document.getElementById('recap-charges').textContent    = charges.toFixed(2) + ' MAD';
    document.getElementById('recap-total').textContent      = total.toFixed(2) + ' MAD';
}

// Init tooltips Bootstrap
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
    });
    recalcTotal();
});
</script>
@endpush