@extends('layouts.app')
@section('title', 'Catégories')
@section('page-title', 'Catégories')

@section('content')
<div class="page-header">
    <div>
        <h1>Catégories</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active text-muted">Gestion des catégories de produits</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nouvelle Catégorie
    </a>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" class="d-flex gap-2">
            <div class="search-box" style="max-width:300px;">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-primary">Rechercher</button>
            @if(request('search'))
                <a href="{{ route('categories.index') }}" class="btn btn-light">Réinitialiser</a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom de la catégorie</th>
                    <th>Description</th>
                    <th class="text-center">Nombre de produits</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr>
                    <td class="text-muted">{{ $cat->id_categorie }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="stat-icon blue" style="width:34px;height:34px;border-radius:8px;font-size:14px;flex-shrink:0;">
                                <i class="bi bi-tag-fill"></i>
                            </div>
                            <span class="fw-semibold">{{ $cat->nom_categorie }}</span>
                        </div>
                    </td>
                    <td class="text-muted">{{ $cat->description ? Str::limit($cat->description, 80) : '—' }}</td>
                    <td class="text-center">
                        <span class="badge bg-primary-subtle text-primary">{{ $cat->produits_count }}</span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('categories.edit', $cat) }}" class="btn btn-sm btn-light" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('categories.destroy', $cat) }}"
                                  onsubmit="return confirm('Supprimer cette catégorie ? Impossible si elle contient des produits.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-light text-danger" title="Supprimer"
                                        {{ $cat->produits_count > 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-tags" style="font-size:2.5rem;display:block;opacity:.3;margin-bottom:10px;"></i>
                        Aucune catégorie trouvée.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())
    <div class="card-body border-top py-3">{{ $categories->links() }}</div>
    @endif
</div>
@endsection