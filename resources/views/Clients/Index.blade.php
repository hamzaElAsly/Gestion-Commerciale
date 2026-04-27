@extends('layouts.app')
@section('title', 'Clients')
@section('page-title', 'Clients')

@section('content')
<div class="page-header">
    <div>
        <h1>Clients</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted">Accueil</a></li>
                <li class="breadcrumb-item active text-muted">Clients</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('clients.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nouveau Client
    </a>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" class="d-flex gap-2 align-items-center">
            <div class="search-box flex-grow-1" style="max-width: 340px;">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control" placeholder="Rechercher par nom, téléphone, adresse..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-primary">Rechercher</button>
            @if(request('search'))
                <a href="{{ route('clients.index') }}" class="btn btn-light">Réinitialiser</a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Client</th>
                    <th>Téléphone</th>
                    <th>Adresse</th>
                    <th class="text-center">Services</th>
                    <th class="text-end">Total dépensé</th>
                    <th>Depuis le</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                <tr>
                    <td class="text-muted">{{ $client->id_client }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar" style="width:32px;height:32px;font-size:13px;">
                                {{ strtoupper(substr($client->nom, 0, 1)) }}
                            </div>
                            <a href="{{ route('clients.show', $client) }}" class="text-decoration-none fw-semibold text-dark">
                                {{ $client->nom }}
                            </a>
                        </div>
                    </td>
                    <td>
                        @if($client->telephone)
                            <a href="tel:{{ $client->telephone }}" class="text-decoration-none text-muted">
                                <i class="bi bi-telephone me-1"></i>{{ $client->telephone }}
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-muted" style="max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $client->adresse ?? '—' }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-primary-subtle text-primary">{{ $client->historiques_count }}</span>
                    </td>
                    <td class="text-end money fw-semibold">
                        {{ number_format($client->total_depense ?? 0, 2) }} MAD
                    </td>
                    <td class="text-muted">{{ $client->date_creation->format('d/m/Y') }}</td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-light" title="Voir">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-light" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('clients.destroy', $client) }}"
                                  onsubmit="return confirm('Supprimer ce client ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-light text-danger" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-people" style="font-size:2.5rem;display:block;opacity:.3;margin-bottom:10px;"></i>
                        Aucun client trouvé.
                        <a href="{{ route('clients.create') }}" class="d-block mt-2">Ajouter le premier client</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($clients->hasPages())
    <div class="card-body py-3 border-top">
        {{ $clients->links() }}
    </div>
    @endif
</div>
@endsection