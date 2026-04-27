@extends('layouts.app')
@section('title', 'Modifier Client')
@section('page-title', 'Clients')

@section('content')
<div class="page-header">
    <div>
        <h1>Modifier le Client</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('clients.index') }}" class="text-muted">Clients</a></li>
                <li class="breadcrumb-item active text-muted">{{ $client->nom }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil-square me-2 text-primary"></i> Modifier : {{ $client->nom }}
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('clients.update', $client) }}">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <label class="form-label">Nom complet <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                               value="{{ old('nom', $client->nom) }}" required>
                        @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Téléphone</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="text" name="telephone" class="form-control @error('telephone') is-invalid @enderror"
                                   value="{{ old('telephone', $client->telephone) }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Adresse</label>
                        <textarea name="adresse" class="form-control" rows="3">{{ old('adresse', $client->adresse) }}</textarea>
                    </div>

                    <div class="d-flex gap-2 pt-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Mettre à jour
                        </button>
                        <a href="{{ route('clients.index') }}" class="btn btn-light">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection