<?php

namespace App\Http\Controllers;

use App\Models\DetailVente;
use App\Models\Produit;
use App\Models\Vente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VenteController extends Controller
{
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $produits = Produit::where('quantite_stock', '>', 0)->get();
        return view('vente.create', compact('produits'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_client' => 'required|string|max:150',
            'produits' => 'required|array|min:1',
            'produits.*.id_produit' => 'required|exists:produits,id_produit',
            'produits.*.quantite' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {

            $vente = Vente::create([
                'nom_client' => $validated['nom_client'],
                'montant_total' => 0
            ]);

            $total = 0;

            foreach ($validated['produits'] as $item) {

                $produit = Produit::findOrFail($item['id_produit']);

                $qty = (int)$item['quantite'];

                if ($produit->quantite_stock < $qty) {
                    throw new \Exception(
                        "Stock insuffisant pour {$produit->nom_produit}"
                    );
                }

                $prix = $produit->prix_vente;

                $prixTotal = $prix * $qty;

                DetailVente::create([
                    'id_vente' => $vente->id_vente,
                    'id_produit' => $produit->id_produit,
                    'nom_produit' => $produit->nom_produit,
                    'quantite' => $qty,
                    'prix_vente' => $prix,
                    'prix_total' => $prixTotal
                ]);

                $produit->decrement('quantite_stock', $qty);
                $total += $prixTotal;
            }

            $vente->update([ 'montant_total' => $total ]);
            DB::commit();
            return redirect()->route('vente.show', $vente->id_vente)->with('success', 'Vente enregistrée');
        }
        catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $vente = Vente::with('details')->findOrFail($id);
        return view('vente.show', compact('vente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $vente = Vente::with('details')->findOrFail($id);
        $produits = Produit::where('quantite_stock', '>', 0)->get();
        return view('vente.edit', compact('vente', 'produits'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vente = Vente::findOrFail($id);
        $vente->delete();
        return redirect()->route('vente.index')->with('success', 'Vente supprimée');
    }
}
