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
                        <label class="form-label" for="tva">TVA (%)</label>
                        <input type="number" id="tva" name="tva" value="{{ old('tva', $devis->tva ?? 0) }}"
                            class="form-control @error('tva') is-invalid @enderror"
                            placeholder="TVA (%)" min="0" max="100" step="0.01">
                        @error('tva')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                      <div class="w-25 mb-3 float-end">
                        <div class="mt-3 text-end">
                            <div>Total HT : <strong><span id="total-ht">0.00</span> MAD</strong></div>
                            <div>TVA (<span id="tva-taux">0.00</span> %) : <strong><span id="montant-tva">0.00</span> MAD</strong></div>
                            <h4>Total TTC : <span id="grand-total">{{ number_format($devis->montant_total, 2) }}</span> MAD</h4>
                            <noscript>
                                <small class="text-danger">(Calculé sans JavaScript)</small>
                            </noscript>
                        </div>
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

  function removeRow(btn) {
      btn.closest('tr').remove();
      calculateTotal();
  }
  document.getElementById('tva').addEventListener('input', calculateTotal);
  document.addEventListener('DOMContentLoaded', calculateTotal);
</script>
@endsection
