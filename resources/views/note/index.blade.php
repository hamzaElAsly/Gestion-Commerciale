@extends('layouts.app')
@section('title', 'Notes')
@section('page-title', 'Notes')

@section('content')

{{-- ── En-tête ── --}}
<div class="page-header">
    <div>
        <h1>Notes</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}" class="text-muted">Accueil</a>
                </li>
                <li class="breadcrumb-item active text-muted">Notes</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('note.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nouvelle Note
    </a>
</div>

{{-- ── Tableau ── --}}
<div class="card">

    {{-- Header avec compteur --}}
    <div class="card-header d-flex align-items-center justify-content-between">
        <span>
            <i class="bi bi-sticky-fill me-2 text-warning"></i>
            {{ $notes->total() }} note(s)
        </span>
    </div>

    {{-- Liste des notes --}}
    @if($notes->count())

    <div class="row g-0">
        @foreach($notes as $note)
        {{-- Chaque note = une ligne cliquable --}}
        <div class="col-12 border-bottom note-row">
            <div class="d-flex align-items-start gap-3 px-4 py-3">

                {{-- Icône décorative --}}
                <div class="note-dot mt-1" style="flex-shrink:0;">
                    <div style="width:38px;height:38px;border-radius:11px;
                                background: {{ $note->nom_client ? '#eff6ff' : '#f5f3ff' }};
                                display:flex;align-items:center;justify-content:center;
                                font-size:17px;color:{{ $note->nom_client ? '#2563eb' : '#7c3aed' }};">
                        <i class="bi bi-{{ $note->nom_client ? 'person-lines-fill' : 'journal-text' }}"></i>
                    </div>
                </div>

                {{-- Contenu principal --}}
                <div class="flex-grow-1 overflow-hidden">
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <span class="fw-bold" style="font-size:14.5px;color:#0f172a;">
                            {{ $note->title }}
                        </span>
                        @if($note->nom_client)
                            <span class="badge"
                                  style="background:#eff6ff;color:#1d4ed8;font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;">
                                <i class="bi bi-person-fill me-1"></i>{{ $note->nom_client }}
                            </span>
                        @endif
                    </div>

                    @if($note->description)
                    <p class="mb-0 text-muted"
                       style="font-size:13px;line-height:1.55;
                              display:-webkit-box;-webkit-line-clamp:2;
                              -webkit-box-orient:vertical;overflow:hidden;">
                        {{ $note->description }}
                    </p>
                    @else
                        <span class="text-muted" style="font-size:12.5px;font-style:italic;">
                            Aucune description
                        </span>
                    @endif

                    <div class="mt-1" style="font-size:11.5px;color:#94a3b8;">
                        <i class="bi bi-clock me-1"></i>
                        {{ $note->created_at->locale('fr')->diffForHumans() }}
                        &nbsp;·&nbsp;
                        {{ $note->created_at->format('d/m/Y à H:i') }}
                        @if($note->updated_at->ne($note->created_at))
                            &nbsp;·&nbsp;
                            <i class="bi bi-pencil me-1"></i>modifiée {{ $note->updated_at->locale('fr')->diffForHumans() }}
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="d-flex gap-1 align-items-center" style="flex-shrink:0;">
                    <a href="{{ route('note.show', $note->id_note) }}"
                       class="btn btn-sm btn-light" title="Voir">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('note.edit', $note->id_note) }}"
                       class="btn btn-sm btn-light" title="Modifier">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form method="POST" action="{{ route('note.destroy', $note->id_note) }}"
                          onsubmit="return confirm('Supprimer cette note ?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-light text-danger" title="Supprimer">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>

            </div>
        </div>
        @endforeach
    </div>

    @else
    {{-- État vide --}}
    <div class="text-center py-5 text-muted">
        <i class="bi bi-journal-x"
           style="font-size:3rem;display:block;opacity:.2;margin-bottom:14px;"></i>
        <div style="font-size:15px;font-weight:600;color:#475569;">
            Aucune note enregistrée
        </div>
        <p style="font-size:13.5px;margin-top:6px;">
            Commencez par créer votre première note.
        </p>
        <a href="{{ route('note.create') }}" class="btn btn-primary btn-sm mt-2">
            <i class="bi bi-plus-lg me-1"></i>Créer une note
        </a>
    </div>
    @endif

    {{-- Pagination --}}
    @if($notes->hasPages())
    <div class="card-body border-top py-3">
        {{ $notes->links() }}
    </div>
    @endif

</div>

@endsection

@push('styles')
<style>
.note-row { transition: background .12s; }
.note-row:hover { background: #f8fafc; }
.note-row:last-child { border-bottom: none !important; }
</style>
@endpush