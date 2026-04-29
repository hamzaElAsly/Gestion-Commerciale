<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::withCount('historiques')
            ->withSum('historiques as total_depense', 'montant_total');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('telephone', 'LIKE', "%{$search}%")
                  ->orWhere('adresse', 'LIKE', "%{$search}%");
            });
        }

        $clients = $query->orderBy('nom')->paginate(15)->withQueryString();

        return view('clients.index', compact('clients'));
    }

    public function create() { return view('clients.create'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|nullable|string|max:100', //'digits:15','unique:clients,ICE'
            'ICE' => 'required|string|max:50',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string',
        ], [
            'nom.required' => 'Le nom du client est obligatoire.',
            'ICE.required' => 'L\'ICE est obligatoire.',
            'ICE.max' => 'L\'ICE ne doit pas dépasser 50 caractères.',
            'nom.max' => 'Le nom ne doit pas dépasser 100 caractères.',
        ]);
        Client::create($validated);

        return redirect()->route('clients.index')->with('success', 'Client ajouté avec succès.');
    }

    public function show(Client $client)
    {
        $client->load(['historiques.details.produit']);
        $historiques = $client->historiques()
            ->with('details.produit')
            ->orderBy('date_service', 'desc')
            ->paginate(10);

        return view('clients.show', compact('client', 'historiques'));
    }

    public function edit(Client $client) { return view('clients.edit', compact('client')); }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string',
        ]);
        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Client modifié avec succès.');
    }

    public function destroy(Client $client)
    {
        if ($client->historiques()->count() > 0) {
            return redirect()->route('clients.index')
                ->with('error', 'Impossible de supprimer ce client car il a des historiques de services.');
        }

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client supprimé avec succès.');
    }
}