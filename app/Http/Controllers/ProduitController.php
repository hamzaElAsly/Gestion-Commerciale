<?php

namespace App\Http\Controllers;

use App\Models\Produit;
// use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduitController extends Controller
{
    public function index(Request $request)
    {
        $query = Produit::with('categorie');
        if ($request->filled('search')) { $query->where('nom_produit', 'LIKE', "%{$request->search}%"); }
        // if ($request->filled('categorie')) { $query->where('id_categorie', $request->categorie); }
        if ($request->filled('stock')) {
            match($request->stock) {
                'faible' => $query->stockFaible()->where('quantite_stock', '>', 0),
                'rupture' => $query->enRupture(),
                'normal' => $query->whereColumn('quantite_stock', '>', 'seuil_alerte'),
                default => null,
            };
        }

        $produits = $query->orderBy('nom_produit')->paginate(15)->withQueryString();
        // $categories = Categorie::orderBy('nom_categorie')->get();

        return view('produits.index', compact('produits')); //'categories'
    }

    public function create()
    {
        // $categories = Categorie::orderBy('nom_categorie')->get();
        return view('produits.create'); //compact('categories')
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_produit' => 'required|string|max:150',
            'prix_unitaire' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0|gte:prix_unitaire',
            'quantite_stock' => 'required|integer|min:0',
            'seuil_alerte' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ], [
            'nom_produit.required' => 'Le nom du produit est obligatoire.',
            'prix_unitaire.required' => 'Le prix unitaire est obligatoire.',
            'prix_unitaire.numeric' => 'Le prix doit être un nombre.',
            'prix_vente.required' => 'Le prix de vente est obligatoire.',
            'prix_vente.numeric' => 'Le prix de vente doit être un nombre.',
            'prix_vente.gte' => 'Le prix de vente doit être supérieur ou égal au prix unitaire.',
            'quantite_stock.required' => 'La quantité en stock est obligatoire.',
        ]);

        $produit = Produit::create($validated);

        // Enregistrer le mouvement d'entrée initial si stock > 0
        if ($validated['quantite_stock'] > 0) {
            $produit->mouvementsStock()->create([
                'type_mouvement' => 'ENTREE',
                'quantite' => $validated['quantite_stock'],
                'description' => 'Stock initial lors de la création du produit',
            ]);
        }
        return redirect()->route('produits.index')->with('success', 'Produit ajouté avec succès.');
    }

    public function show(Produit $produit)
    {
        $produit->load('categorie');
        $mouvements = $produit->mouvementsStock()
            ->orderBy('date_mouvement', 'desc')
            ->paginate(20);

        return view('produits.show', compact('produit', 'mouvements'));
    }

    public function edit(Produit $produit)
    {
        // $categories = Categorie::orderBy('nom_categorie')->get();
        return view('produits.edit', compact('produit'));//'categories'
    }

    public function update(Request $request, Produit $produit)
    {
        $validated = $request->validate([
            'nom_produit' => 'required|string|max:150',
            'prix_unitaire' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0|gte:prix_unitaire',
            'quantite_stock' => 'required|integer|min:0',
            'seuil_alerte' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);
        $ancienStock = $produit->quantite_stock;
        $produit->update($validated);

        // Enregistrer le mouvement si le stock a changé manuellement
        $diff = $validated['quantite_stock'] - $ancienStock;
        if ($diff !== 0) {
            $produit->mouvementsStock()->create([
                'type_mouvement' => $diff > 0 ? 'ENTREE' : 'SORTIE',
                'quantite' => abs($diff),
                'description' => 'Ajustement manuel du stock',
            ]);
        }

        return redirect()->route('produits.index')->with('success', 'Produit modifié avec succès.');
    }

    public function destroy(Produit $produit)
    {
        $produit->delete();
        return redirect()->route('produits.index')->with('success', 'Produit supprimé avec succès.');
    }

    public function ajouterStock(Request $request, Produit $produit)
    {
        $validated = $request->validate([
            'quantite' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
        ]);
        $produit->incrementerStock(
            $validated['quantite'],
            $validated['description'] ?? 'Ajout de stock'
        );
        return redirect()->back()->with('success', "Stock mis à jour : +{$validated['quantite']} unités.");
    }
}