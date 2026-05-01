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
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-box-seam-fill me-2 text-primary"></i> Informations du Devis
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('devis.store') }}">
                    @csrf
                    <!-- Nom client -->
                    <div class="mb-3">
                        <label>Nom Client</label>
                        <input type="text" name="nom_client" class="form-control" required>
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
                        <tbody></tbody>
                    </table>

                    <button type="button" class="btn btn-primary" onclick="addRow()">+ Ajouter produit</button>

                    <h4 class="mt-3">Total: <span id="grand-total">0</span> MAD</h4>

                    <button type="submit" class="btn btn-success mt-3">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let index = 0;

// produits depuis Laravel
const produits = @json($produits);

function addRow() {
    let row = `
    <tr>
        <td>
            <select name="produits[${index}][id_produit]" class="form-control" onchange="updatePrice(this)">
                <option value="">-- Choisir --</option>
                ${produits.map(p => `<option value="${p.id_produit}" data-price="${p.prix_vente}">${p.nom_produit}</option>`).join('')}
            </select>
        </td>

        <td>
            <input type="text" class="form-control prix" readonly>
        </td>

        <td>
            <input type="number" name="produits[${index}][quantite]" class="form-control quantite" value="1" min="1" oninput="calculateRow(this)">
        </td>

        <td>
            <input type="text" class="form-control total" readonly>
        </td>

        <td>
            <button type="button" class="btn btn-danger" onclick="removeRow(this)">X</button>
        </td>
    </tr>
    `;

    document.querySelector('#produits-table tbody').insertAdjacentHTML('beforeend', row);
    index++;
}

function removeRow(btn) {
    btn.closest('tr').remove();
    calculateTotal();
}

function updatePrice(select) {
    let price = select.selectedOptions[0].dataset.price || 0;
    let row = select.closest('tr');

    row.querySelector('.prix').value = price;
    calculateRow(select);
}

function calculateRow(element) {
    let row = element.closest('tr');

    let price = parseFloat(row.querySelector('.prix').value) || 0;
    let qty = parseInt(row.querySelector('.quantite').value) || 0;

    let total = price * qty;

    row.querySelector('.total').value = total.toFixed(2);

    calculateTotal();
}

function calculateTotal() {
    let total = 0;

    document.querySelectorAll('.total').forEach(el => {
        total += parseFloat(el.value) || 0;
    });

    document.getElementById('grand-total').innerText = total.toFixed(2);
}
</script>
@endsection