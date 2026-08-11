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
        return view('devis.create');
    }

    // 💾 STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:150',
            'nom_client' => 'required|string|max:150',
            'produits' => 'required|array|min:1',
            'produits.*.nom_produit' => 'required|string|max:255',
            'produits.*.prix' => 'required|numeric|min:0',
            'produits.*.quantite' => 'required|integer|min:1',
            'tva' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $tva = (float) ($validated['tva'] ?? 0);

        DB::beginTransaction();

        try {

            $devis = Devis::create([
                'titre' => $validated['titre'],
                'nom_client' => $validated['nom_client'],
                'tva' => $tva,
                'montant_total' => 0
            ]);
            $total = 0;
            foreach ($validated['produits'] as $item) {
                $prix = (float) $item['prix'];
                $qty  = (int) $item['quantite'];
                $prixTotal = $prix * $qty;
                DetailDevis::create([
                    'id_devis' => $devis->id_devis,
                    'nom_produit' => $item['nom_produit'],
                    'quantite' => $qty,
                    'prix_vente' => $prix,
                    'prix_total' => $prixTotal,
                ]);
                $total += $prixTotal;
            }
            $montantTva = round($total * $tva / 100, 2);
            $devis->update(['montant_total' => round($total + $montantTva, 2)]);

            DB::commit();

            return redirect()->route('devis.show', $devis)->with('success', 'Devis créé avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // 👁️ SHOW
    public function show($id)
    {
        $devis = Devis::with('details')->findOrFail($id);
        return view('devis.show', compact('devis'));
    }

    // ✏️ EDIT
    public function edit($id)
    {
        $devis = Devis::with('details')->findOrFail($id);
        return view('devis.edit', compact('devis'));
    }

    // 🔄 UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:150',
            'nom_client' => 'required|string|max:150',
            'produits' => 'required|array|min:1',
            'produits.*.nom_produit' => 'required|string|max:255',
            'produits.*.prix' => 'required|numeric|min:0',
            'produits.*.quantite' => 'required|integer|min:1',
            'tva' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $tva = (float) ($validated['tva'] ?? 0);

        DB::beginTransaction();

        try {
            $devis = Devis::findOrFail($id);
            $devis->update([ 
                'titre' => $validated['titre'],
                'nom_client' => $validated['nom_client'],
                'tva' => $tva,
            ]);
            DetailDevis::where('id_devis', $devis->id_devis)->delete();
            $total = 0;
            foreach ($validated['produits'] as $item) {
                $prix = (float) $item['prix'];
                $qty  = (int) $item['quantite'];
                $prixTotal = $prix * $qty;
                DetailDevis::create([
                    'id_devis' => $devis->id_devis,
                    'nom_produit' => $item['nom_produit'],
                    'quantite' => $qty,
                    'prix_vente' => $prix,
                    'prix_total' => $prixTotal,
                ]);
                $total += $prixTotal;
            }
            $montantTva = round($total * $tva / 100, 2);
            $devis->update(['montant_total' => round($total + $montantTva, 2)]);

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
        $devis = Devis::with('details')->findOrFail($id);
        $devis->delete();

        return redirect()->route('devis.index')
            ->with('success', 'Devis supprimé');
    }

    // 🧾 PDF
    public function print($id)
    {
        $devis = Devis::findOrFail($id);
        $pdf = Pdf::loadView('pdf.devis', compact('devis'))->setPaper('a4', 'portrait');
        return $pdf->download("devis-{$devis->id_devis}.pdf");
    }
}
