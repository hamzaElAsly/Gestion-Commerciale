<?php

namespace App\Http\Controllers;

use App\Models\GestionStock;
use App\Models\Produit;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = GestionStock::with(['produit.categorie', 'historique.client'])
            ->orderBy('date_mouvement', 'desc');

        if ($request->filled('produit_id')) {
            $query->where('id_produit', $request->produit_id);
        }

        if ($request->filled('type')) {
            $query->where('type_mouvement', $request->type);
        }

        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_mouvement', [$request->date_debut, $request->date_fin]);
        }

        $mouvements = $query->paginate(20)->withQueryString();
        $produits = Produit::orderBy('nom_produit')->get();

        $totaux = [
            'entrees' => GestionStock::entrees()->sum('quantite'),
            'sorties' => GestionStock::sorties()->sum('quantite'),
        ];

        return view('stock.index', compact('mouvements', 'produits', 'totaux'));
    }

    public function etatStock(Request $request)
    {
        $query = Produit::with('categorie');

        if ($request->filled('categorie')) {
            $query->where('id_categorie', $request->categorie);
        }

        $produits = $query->orderBy('nom_produit')->get();

        $stats = [
            'total_produits' => $produits->count(),
            'stock_normal' => $produits->where('statut_stock', 'normal')->count(),
            'stock_faible' => $produits->where('statut_stock', 'faible')->count(),
            'stock_rupture' => $produits->where('statut_stock', 'rupture')->count(),
            'valeur_totale' => $produits->sum(fn($p) => $p->prix_vente * $p->quantite_stock),
        ];

        return view('stock.etat', compact('produits', 'stats'));
    }

    public function entreeStock(Request $request)
    {
        $validated = $request->validate([
            'id_produit' => 'required|exists:produits,id_produit',
            'quantite' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $produit = Produit::findOrFail($validated['id_produit']);
        $produit->incrementerStock(
            $validated['quantite'],
            $validated['description'] ?? 'Entrée de stock manuelle'
        );

        return redirect()->back()
            ->with('success', "Stock mis à jour : +{$validated['quantite']} {$produit->nom_produit}");
    }
}