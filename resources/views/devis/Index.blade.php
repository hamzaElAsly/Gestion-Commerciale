@extends('layouts.app')
@section('title', 'Produits')
@section('page-title', 'Produits')

@section('content')
<div class="container mt-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>📄 Liste des Devis</h3>

        <a href="{{ route('devis.create') }}" class="btn btn-primary">
            + Nouveau Devis
        </a>
    </div>

    <!-- SUCCESS MESSAGE -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- TABLE -->
    <div class="card">
        <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Titre</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($devis as $d)
                <tr>
                    <td>{{ $d->id_devis }}</td>
                    <td>{{ $d->titre }}</td>
                    <td>{{ $d->nom_client }}</td>
                    <td>{{ $d->created_at->format('d/m/Y') }}</td>
                    <td>
                        <strong class="text-success">
                            {{ number_format($d->montant_total, 2) }} MAD
                        </strong>
                    </td>

                    <td>
                        <a href="{{ route('devis.show', $d->id_devis) }}" class="btn btn-sm btn-light" title="Voir">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('devis.edit', $d->id_devis) }}" class="btn btn-sm btn-light" title="Modifier">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="{{ route('devis.print', $d->id_devis) }}" class="btn btn-sm btn-light" title="Imprimer facture" 
                            target="_blank"> <i class="bi bi-printer"></i>
                        </a>
                        <form action="{{ route('devis.destroy', $d->id_devis) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-light text-danger" title="Supprimer"
                                onclick="return confirm('Supprimer ce devis ?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Aucun devis trouvé</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- PAGINATION -->
        <div class="mt-3">
            {{ $devis->links() }}
        </div>
        </div>

    </div>
</div>
@endsection