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

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="alert alert-warning d-flex gap-2">
            <i class="bi bi-info-circle-fill"></i>
            <div>
                <strong>Note :</strong> Seuls le statut et la remarque peuvent être modifiés.
                Pour modifier les produits utilisés, veuillez supprimer et recréer le service.
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil-square me-2 text-primary"></i> Modifier le Service
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('historique.update', $historique) }}">
                    @csrf @method('PUT')

                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="text-muted mb-1"><strong>Client :</strong> {{ $historique->client->nom }}</div>
                        <div class="text-muted mb-1"><strong>Date :</strong> {{ $historique->date_service->format('d/m/Y H:i') }}</div>
                        <div class="text-muted"><strong>Montant :</strong> {{ number_format($historique->montant_total, 2) }} MAD</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Statut</label>
                        <select name="statut" class="form-select">
                            <option value="termine" {{ old('statut', $historique->statut) == 'termine' ? 'selected' : '' }}>✅ Terminé</option>
                            <option value="en_cours" {{ old('statut', $historique->statut) == 'en_cours' ? 'selected' : '' }}>🔄 En cours</option>
                            <option value="annule" {{ old('statut', $historique->statut) == 'annule' ? 'selected' : '' }}>❌ Annulé</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Remarque</label>
                        <textarea name="remarque" class="form-control" rows="4">{{ old('remarque', $historique->remarque) }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Mettre à jour
                        </button>
                        <a href="{{ route('historique.show', $historique) }}" class="btn btn-light">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection