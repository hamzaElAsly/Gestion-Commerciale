@extends('layouts.app')
@section('title', 'Modifier la Note')
@section('page-title', 'Notes')

@section('content')

{{-- ── En-tête ── --}}
<div class="page-header">
    <div>
        <h1>Modifier la Note</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}" class="text-muted">Accueil</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('note.index') }}" class="text-muted">Notes</a>
                </li>
                <li class="breadcrumb-item text-muted">
                    {{ \Illuminate\Support\Str::limit($note->title, 30) }}
                </li>
                <li class="breadcrumb-item active text-muted">Modifier</li>
            </ol>
        </nav>
    </div>
    {{-- Actions rapides --}}
    <div class="d-flex gap-2">
        <form method="POST" action="{{ route('note.destroy', $note->id_note) }}"
              onsubmit="return confirm('Supprimer définitivement cette note ?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-light text-danger">
                <i class="bi bi-trash me-1"></i>Supprimer
            </button>
        </form>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">

        {{-- Badge info modification --}}
        <div class="alert d-flex align-items-center gap-2 mb-4"
             style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;
                    color:#166534;font-size:13px;">
            <i class="bi bi-pencil-square" style="font-size:15px;flex-shrink:0;"></i>
            <div>
                <strong>Modification</strong> — Créée le {{ $note->created_at->format('d/m/Y à H:i') }}
                @if($note->updated_at->ne($note->created_at))
                    · Dernière modification {{ $note->updated_at->locale('fr')->diffForHumans() }}
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil-square me-2 text-primary"></i>
                Modifier : <span class="text-muted fw-normal">{{ $note->title }}</span>
            </div>
            <div class="card-body">

                <form method="POST" action="{{ route('note.update', $note->id_note) }}">
                    @csrf @method('PUT')

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
                                   value="{{ old('title', $note->title) }}"
                                   required>
                        </div>
                        @error('title')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nom client (optionnel) --}}
                    <div class="mb-4">
                        <label class="form-label">
                            Client associé
                            <span class="text-muted fw-normal ms-1" style="font-size:12px;">(optionnel)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-person text-muted"></i>
                            </span>
                            <input type="text"
                                   name="nom_client"
                                   class="form-control @error('nom_client') is-invalid @enderror"
                                   placeholder="Nom du client concerné…"
                                   value="{{ old('nom_client', $note->nom_client) }}">
                            @if($note->nom_client)
                                <button type="button" class="btn btn-light border"
                                        title="Effacer le client"
                                        onclick="document.querySelector('[name=nom_client]').value=''">
                                    <i class="bi bi-x-lg text-muted"></i>
                                </button>
                            @endif
                        </div>
                        @error('nom_client')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="mb-4">
                        <label class="form-label">
                            Description
                            <span class="text-muted fw-normal ms-1" style="font-size:12px;">(optionnel)</span>
                        </label>
                        <textarea name="description"
                                  id="description"
                                  class="form-control @error('description') is-invalid @enderror"
                                  rows="8"
                                  placeholder="Contenu de la note…">{{ old('description', $note->description) }}</textarea>
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
                            <i class="bi bi-check-lg me-1"></i>Mettre à jour
                        </button>
                        <a href="{{ route('note.show', $note->id_note) }}" class="btn btn-light">
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