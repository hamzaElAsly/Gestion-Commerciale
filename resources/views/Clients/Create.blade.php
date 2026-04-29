@extends('layouts.app')
@section('title', 'Nouveau Client')
@section('page-title', 'Clients')

@section('content')
<div class="page-header">
    <div>
        <h1>Nouveau Client</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted">Accueil</a></li>
                <li class="breadcrumb-item"><a href="{{ route('clients.index') }}" class="text-muted">Clients</a></li>
                <li class="breadcrumb-item active text-muted">Nouveau</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-plus-fill me-2 text-primary"></i> Informations du client
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('clients.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label">Nom complet <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                               value="{{ old('nom') }}" placeholder="Ex: Mohamed Alami" autofocus required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">ICE</label>
                        <input type="text" name="ICE" class="form-control @error('ICE') is-invalid @enderror"
                               value="{{ old('ICE') }}" placeholder="Ex: ICE123456">
                        @error('ICE')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Téléphone</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="text" name="telephone" class="form-control @error('telephone') is-invalid @enderror"
                                   value="{{ old('telephone') }}" placeholder="Ex: 0612345678">
                        </div>
                        @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Adresse</label>
                        <textarea name="adresse" class="form-control @error('adresse') is-invalid @enderror"
                                  rows="3" placeholder="Adresse complète...">{{ old('adresse') }}</textarea>
                        @error('adresse')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2 pt-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Enregistrer
                        </button>
                        <a href="{{ route('clients.index') }}" class="btn btn-light">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection