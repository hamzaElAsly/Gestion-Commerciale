@extends('layouts.app')
@section('title', 'Nouvelle Note')
@section('page-title', 'Notes')

@section('content')

{{-- ── En-tête ── --}}
<div class="page-header">
    <div>
        <h1>Nouvelle Note</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}" class="text-muted">Accueil</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('note.index') }}" class="text-muted">Notes</a>
                </li>
                <li class="breadcrumb-item active text-muted">Nouvelle</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">

        <div class="card">
            <div class="card-header">
                <i class="bi bi-journal-plus me-2 text-primary"></i>
                Créer une nouvelle note
            </div>
            <div class="card-body">

                <form method="POST" action="{{ route('note.store') }}">
                    @csrf

                    {{-- Titre --}}
                    <div class="mb-4">
                        <label class="form-label">
                            Titre <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-type-h1 text-muted"></i>
                            </span>
                            <input type="text"
                                   name="title"
                                   class="form-control @error('title') is-invalid @enderror"
                                   placeholder="Titre de la note…"
                                   value="{{ old('title') }}"
                                   autofocus required>
                        </div>
                        @error('title')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nom client (optionnel) --}}
                    <div class="mb-4">
                        <label class="form-label">
                            Client associé
                            <span class="text-muted fw-normal ms-1" style="font-size:12px;">
                                (optionnel)
                            </span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-person text-muted"></i>
                            </span>
                            <input type="text"
                                   name="nom_client"
                                   class="form-control @error('nom_client') is-invalid @enderror"
                                   placeholder="Nom du client concerné…"
                                   value="{{ old('nom_client') }}">
                        </div>
                        @error('nom_client')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Laissez vide si la note n'est pas liée à un client particulier.
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mb-4">
                        <label class="form-label">
                            Description
                            <span class="text-muted fw-normal ms-1" style="font-size:12px;">
                                (optionnel)
                            </span>
                        </label>
                        <textarea name="description"
                                  id="description"
                                  class="form-control @error('description') is-invalid @enderror"
                                  rows="7"
                                  placeholder="Contenu de la note, remarques, observations…">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                        @enderror
                        <div class="d-flex justify-content-end mt-1">
                            <span id="char-count" style="font-size:11.5px;color:#94a3b8;">0 caractère(s)</span>
                        </div>
                    </div>

                    {{-- Boutons --}}
                    <div class="d-flex gap-2 pt-1">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Enregistrer
                        </button>
                        <a href="{{ route('note.index') }}" class="btn btn-light">
                            Annuler
                        </a>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    // Compteur de caractères pour la description
    const ta    = document.getElementById('description');
    const count = document.getElementById('char-count');
    function updateCount() {
        const n = ta.value.length;
        count.textContent = n.toLocaleString('fr') + ' caractère' + (n > 1 ? 's' : '');
    }
    ta.addEventListener('input', updateCount);
    updateCount();
</script>
@endpush