<?php
namespace App\Http\Controllers;

use App\Models\DetailDevis;
use App\Models\Devis;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DevisController extends Controller
{
    // 📄 LIST
    public function index()
    {
        $devis = Devis::latest()->paginate(10);
        return view('devis.index', compact('devis'));
    }

    // ➕ CREATE
    public function create()
    {
        $produits = Produit::all();
        return view('devis.create', compact('produits'));
    }

    // 💾 STORE
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
            $devis = Devis::create([
                'nom_client' => $validated['nom_client'],
                'montant_total' => 0
            ]);

            $total = 0;

            foreach ($validated['produits'] as $item) {
                $produit = Produit::findOrFail($item['id_produit']);

                // $prixTotal = $produit->prix_vente * $item['quantite'];
                $prix = (float) $produit->prix_vente;
                $qty  = (int) $item['quantite'];
                $prixTotal = $prix * $qty;
                // dd($devis->nom_client, $prix, $qty, $prixTotal);

                DetailDevis::create([
                    'id_devis' => $devis->id_devis,
                    'id_produit' => $produit->id_produit,
                    'quantite' => $qty,
                    'prix_vente' => $prix,
                    'prix_total' => $prixTotal,
                ]);
                // ❌ NO STOCK DECREMENT (comme demandé)
                $total += $prixTotal;
            }
            $devis->update(['montant_total' => $total]);

            DB::commit();

            return redirect()->route('devis.show', $devis)
                ->with('success', 'Devis créé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // 👁️ SHOW
    public function show($id)
    {
        $devis = Devis::with('details.produit')->findOrFail($id);
        return view('devis.show', compact('devis'));
    }

    // ✏️ EDIT
    public function edit($id)
    {
        $devis = Devis::with('details')->findOrFail($id);
        $produits = Produit::all();
        return view('devis.edit', compact('devis', 'produits'));
    }

    // 🔄 UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nom_client' => 'required|string|max:150',

            'produits' => 'required|array|min:1',
            'produits.*.id_produit' => 'required|exists:produits,id_produit',
            'produits.*.quantite' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $devis = Devis::findOrFail($id);
            $devis->update([ 'nom_client' => $validated['nom_client'] ]);
            // حذف القديم
            DetailDevis::where('id_devis', $devis->id_devis)->delete();
            $total = 0;
            foreach ($validated['produits'] as $item) {
                $produit = Produit::findOrFail($item['id_produit']);

                $prix = (float) $produit->prix_vente;
                $qty  = (int) $item['quantite'];
                $prixTotal = $prix * $qty;

                DetailDevis::create([
                    'id_devis' => $devis->id_devis,
                    'id_produit' => $produit->id_produit,
                    'quantite' => $qty,
                    'prix_vente' => $prix,
                    'prix_total' => $prixTotal,
                ]);
                // dd($prix, $qty, $prixTotal);
                $total += $prixTotal;
            }

            $devis->update(['montant_total' => $total]);

            DB::commit();

            return redirect()->route('devis.show', $devis)
                ->with('success', 'Devis mis à jour');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // 🗑 DELETE
    public function destroy($id)
    {
        $devis = Devis::findOrFail($id);
        $devis->delete();

        return redirect()->route('devis.index')
            ->with('success', 'Devis supprimé');
    }

    // 🧾 PDF
    public function print($id)
    {
        $devis = Devis::with('details.produit')->findOrFail($id);
        $pdf = Pdf::loadView('pdf.devis', compact('devis'))->setPaper('a4', 'portrait');
        return $pdf->download("devis-{$devis->id_devis}.pdf");
    }
}