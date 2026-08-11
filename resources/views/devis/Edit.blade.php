@extends('layouts.app')
@section('title', 'Modifier Produit')
@section('page-title', 'Produits')

@php
    $nomProduitPlaceholder = 'Nom produit';
@endphp
@section('content')
<div class="page-header">
    <div>
        <h1>Modifier le Devis</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('devis.index') }}" class="text-muted">Devis</a></li>
                <li class="breadcrumb-item active text-muted">{{ $devis->id_devis }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil-square me-2 text-primary"></i> Modifier : {{ $devis->titre }}
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('devis.update', $devis->id_devis) }}">
                    @csrf
                    @method('PUT')
                    <!-- Titre Devis -->
                    <div class="row d-flex justify-content-between">
                      <div class="w-50 mb-3 float-start">
                          <label>Titre Devis</label>
                          <input type="text" name="titre" class="form-control" value="{{ $devis->titre }}" required>
                      </div>
                      <!-- Nom client -->
                      <div class="w-50 mb-3 float-end">
                          <label>Nom Client</label>
                          <input type="text" name="nom_client" class="form-control" value="{{ $devis->nom_client }}" required>
                      </div>
                    </div>

                    <!-- Table -->
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
                            @foreach($devis->details as $i => $detail)
                            <tr>
                                <td>
                                    <input type="text" value="{{ $detail->nom_produit }}" name="produits[{{ $i }}][nom_produit]" class="form-control" placeholder="{{ $nomProduitPlaceholder }}" required>
                                </td>

                                <td>
                                    <input type="text" class="form-control prix" value="{{ $detail->prix_vente }}" 
                                    step="0.01" name="produits[{{ $i }}][prix]" placeholder="Prix" min="0" 
                                    oninput="calculateRow(this)" required>
                                </td>

                                <td>
                                    <input type="number" name="produits[{{ $i }}][quantite]"
                                        class="form-control quantite"
                                        value="{{ $detail->quantite }}"
                                        min="1" step="1" oninput="calculateRow(this)">
                                </td>

                                <td>
                                    <input type="number" step="0.01" class="form-control total"
                                        value="{{ $detail->prix_total }}" readonly>
                                </td>

                                <td>
                                    <button type="button" class="btn btn-danger" onclick="removeRow(this)">X</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="row d-flex justify-content-between">
                      <div class="w-25 mb-3 float-start">
                        <button type="button" class="btn btn-primary" onclick="addRow()">+ Ajouter</button>
                      </div>
                      <div class="w-50 mb-3">
                        <input type="number" name="tva" value="{{ $devis->tva }}" class="form-control" 
                            placeholder="TVA (%)" min="0" max="100" step="0.01" required>
                      </div>
                      <div class="w-25 mb-3 float-end">
                        <h4 class="mt-3">Total: 
                            <span id="grand-total">
                                {{ number_format($devis->details->sum('prix_total'), 2) }}
                            </span> MAD
                            <noscript>
                                <small class="text-danger">(Calculé sans JavaScript)</small>
                            </noscript>
                        </h4>
                      </div>
                    </div>

                    <button class="btn btn-success mt-3">Modifier</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
  let index = {{ count($devis->details) }};
  function addRow() {
      let row = `
      <tr>
          <td>
              <input
                  type="text"
                  name="produits[${index}][nom_produit]"
                  name="produits[{{ $i }}][nom_produit]"
                  class="form-control"
                  placeholder="{{ $nomProduitPlaceholder }}"
                  required
              >
          </td>
          <td>
              <input
                  type="number"
                  step="0.01"
                  name="produits[${index}][prix]"
                  class="form-control prix"
                  placeholder="Prix" min="0"
                  oninput="calculateRow(this)" required
              >
          </td>
          <td>
              <input
                  type="number"
                  name="produits[${index}][quantite]"
                  class="form-control quantite"
                  value="1"
                  min="1"
                  oninput="calculateRow(this)"
                  required
              >
          </td>
          <td>
              <input
                  type="number"
                  step="0.01"
                  name="produits[${index}][total]"
                  class="form-control total"
                  readonly
              >
          </td>
          <td>
              <button
                  type="button"
                  class="btn btn-danger"
                  onclick="removeRow(this)"
              >
                  X
              </button>
          </td>
      </tr>
      `;
      document
          .querySelector('#produits-table tbody')
          .insertAdjacentHTML('beforeend', row);
      index++;
  }

  function calculateRow(el) {
      let row = el.closest('tr');
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

  function removeRow(btn) {
      btn.closest('tr').remove();
      calculateTotal();
  }
  document.addEventListener('DOMContentLoaded', calculateTotal);
</script>
@endsection