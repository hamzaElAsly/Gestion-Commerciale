@extends('layouts.app')
@section('title', 'Nouveau Devis')
@section('page-title', 'Devis')

@section('content')
<div class="page-header">
    <div>
        <h1>Nouveau Devis</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('devis.index') }}" class="text-muted">Devis</a></li>
                <li class="breadcrumb-item active text-muted">Nouveau</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-box-seam-fill me-2 text-primary"></i> Informations du Devis
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('devis.store') }}">
                    @csrf
                    <!-- Titre Devis -->
                    <div class="row d-flex justify-content-between">
                      <div class="w-50 mb-3 float-start">
                          <label>Titre Devis</label>
                          <input type="text" name="titre" class="form-control" required>
                      </div>
                      <!-- Nom client -->
                      <div class="w-50 mb-3 float-end">
                          <label>Nom Client</label>
                          <input type="text" name="nom_client" class="form-control" required>
                      </div>
                    </div>

                    <!-- Produits -->
                    <table class="table" id="produits-table">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Prix</th>
                                <th>Quantité</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="row d-flex justify-content-between">
                      <div class="w-25 mb-3 float-start">
                        <button type="button" class="btn btn-primary" onclick="addRow()">+ Ajouter produit</button>
                      </div>
                      <div class="w-50 mb-3 float-start">
                        <label class="form-label" for="tva">TVA (%)</label>
                        <input type="number" id="tva" name="tva" value="{{ old('tva', 0) }}"
                            class="form-control @error('tva') is-invalid @enderror" placeholder="TVA (%)"
                            min="0" max="100" step="0.01">
                        @error('tva')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                      <div class="w-25 mb-3 float-end">
                        <div class="mt-3 text-end">
                            <div>Total HT : <strong><span id="total-ht">0.00</span> MAD</strong></div>
                            <div>TVA (<span id="tva-taux">0.00</span> %) : <strong><span id="montant-tva">0.00</span> MAD</strong></div>
                            <h4>Total TTC : <span id="grand-total">0.00</span> MAD</h4>
                            <noscript>
                                <small class="text-danger">(Calculé sans JavaScript)</small>
                            </noscript>
                        </div>
                      </div>
                    </div>
                    <button type="submit" class="btn btn-success mt-3">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let index = 1;
function addRow() {
    let row = `
    <tr>
        <td>
            <input type="text" name="produits[${index}][nom_produit]" class="form-control" placeholder="Nom produit" required>
        </td>
        <td>
            <input type="number" step="0.01" name="produits[${index}][prix]" class="form-control prix"
                placeholder="Prix" min="0" oninput="calculateRow(this)" required >
        </td>
        <td>
            <input type="number" name="produits[${index}][quantite]"
                class="form-control quantite" value="1" min="1"
                oninput="calculateRow(this)" required >
        </td>
        <td>
            <input type="number" step="0.01" name="produits[${index}][total]" class="form-control total" readonly >
        </td>
        <td>
            <button type="button" class="btn btn-danger" onclick="removeRow(this)"> X </button>
        </td>
    </tr>
    `;
    document.querySelector('#produits-table tbody').insertAdjacentHTML('beforeend', row);
    index++;
}

function calculateTotal() {
    let totalHt = 0;
    document.querySelectorAll('.total').forEach(el => {
        totalHt += parseFloat(el.value) || 0;
    });
    const tva = parseFloat(document.getElementById('tva').value) || 0;
    const montantTva = totalHt * tva / 100;
    document.getElementById('total-ht').innerText = totalHt.toFixed(2);
    document.getElementById('tva-taux').innerText = tva.toFixed(2);
    document.getElementById('montant-tva').innerText = montantTva.toFixed(2);
    document.getElementById('grand-total').innerText = (totalHt + montantTva).toFixed(2);
}

function calculateRow(element) {
    let row = element.closest('tr');
    let prix = parseFloat(row.querySelector('.prix').value) || 0;
    let quantite = parseInt(row.querySelector('.quantite').value) || 0;
    let total = prix * quantite;
    row.querySelector('.total').value = total.toFixed(2);
    calculateTotal();
}

function removeRow(button) {
    button.closest('tr').remove();
    calculateTotal();
}

document.getElementById('tva').addEventListener('input', calculateTotal);
document.addEventListener('DOMContentLoaded', function () {
    addRow();
    calculateTotal();
});

</script>
@endsection
