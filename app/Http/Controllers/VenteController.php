<?php

namespace App\Http\Controllers;

use App\Models\DetailVente;
use App\Models\GestionStock;
use App\Models\Produit;
use App\Models\Vente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class VenteController extends Controller
{
    public function index(Request $request)
    {
        $query = Vente::with('details')->latest();

        // Filtre par nom client
        if ($request->filled('search')) {
            $query->where('nom_client', 'like', '%' . $request->search . '%');
        }

        // Filtre par période
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('created_at', [
                $request->date_debut . ' 00:00:00',
                $request->date_fin   . ' 23:59:59',
            ]);
        }

        // Filtre mois / année
        if ($request->filled('mois') && $request->filled('annee')) {
            $query->whereMonth('created_at', $request->mois)
                  ->whereYear('created_at',  $request->annee);
        }

        $ventes      = $query->paginate(15)->withQueryString();
        $totalFiltré = $query->sum('montant_total');

        return view('vente.index', compact('ventes', 'totalFiltré'));
    }

    // ─────────────────────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────────────────────
    public function create()
    {
        // On charge TOUS les produits (pas seulement ceux en stock)
        // pour que l'admin voie aussi les ruptures et prenne une décision.
        // On trie : stock disponible en premier, rupture ensuite.
        $produits = Produit::orderByDesc('quantite_stock')
                           ->orderBy('nom_produit')
                           ->get();

        return view('vente.create', compact('produits'));
    }

    // ─────────────────────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'nom_client'                  => 'required|string|max:150',
            'charges'                     => 'required|numeric|min:0',
            // produits = optionnel (nullable)
            'produits'                    => 'nullable|array',
            'produits.*.id_produit'       => 'required_with:produits|exists:produits,id_produit',
            'produits.*.quantite'         => 'required_with:produits|integer|min:1',
        ], [
            'nom_client.required'         => 'Le nom du client est obligatoire.',
            'charges.required'            => 'Les frais de service sont obligatoires.',
            'charges.numeric'             => 'Les frais doivent être un nombre valide.',
            'charges.min'                 => 'Les frais doivent être ≥ 0.',
            'produits.*.id_produit'       => 'Produit invalide.',
            'produits.*.quantite'         => 'Quantité invalide (minimum 1).',
        ]);

        DB::beginTransaction();
        try {
            /* ── 1. Vérification stock avant toute écriture ── */
            $lignesProduits = array_values(
                array_filter($request->input('produits', []), fn($l) => !empty($l['id_produit']))
            );

            foreach ($lignesProduits as $item) {
                $produit = Produit::findOrFail($item['id_produit']);
                if ($produit->quantite_stock < (int) $item['quantite']) {
                    throw new \Exception(
                        "Stock insuffisant pour « {$produit->nom_produit} ». "
                        . "Disponible : {$produit->quantite_stock} — Demandé : {$item['quantite']}"
                    );
                }
            }

            /* ── 2. Création de la vente ── */
            $vente = Vente::create([
                'nom_client'    => $request->nom_client,
                'charges'       => $request->charges,
                'montant_total' => 0,
            ]);

            /* ── 3. Lignes produits (optionnel) ── */
            $total = 0;

            foreach ($lignesProduits as $item) {
                $produit  = Produit::findOrFail($item['id_produit']);
                $qty      = (int) $item['quantite'];
                $prix     = $produit->prix_vente;
                $prixLigne = $prix * $qty;

                DetailVente::create([
                    'id_vente'    => $vente->id_vente,
                    'id_produit'  => $produit->id_produit,
                    'nom_produit' => $produit->nom_produit,   // snapshot
                    'quantite'    => $qty,
                    'prix_vente'  => $prix,
                    'prix_total'  => $prixLigne,
                ]);

                // Décrémenter stock + mouvement
                $produit->decrement('quantite_stock', $qty);
                GestionStock::create([
                    'id_produit'     => $produit->id_produit,
                    'type_mouvement' => 'SORTIE',
                    'quantite'       => $qty,
                    'description'    => "Vente #{$vente->id_vente} — {$vente->nom_client}",
                ]);

                $total += $prixLigne;
            }

            /* ── 4. Montant total = produits + charges ── */
            $vente->update(['montant_total' => $total + $vente->charges]);

            DB::commit();

            return redirect()
                ->route('vente.show', $vente->id_vente)
                ->with('success', 'Vente enregistrée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────────────────────
    public function show(string $id)
    {
        $vente = Vente::with('details')->findOrFail($id);
        return view('vente.show', compact('vente'));
    }

    // ─────────────────────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────────────────────
    public function edit(string $id)
    {
        $vente    = Vente::with('details')->findOrFail($id);
        $produits = Produit::orderByDesc('quantite_stock')
                           ->orderBy('nom_produit')
                           ->get();

        return view('vente.edit', compact('vente', 'produits'));
    }

    // ─────────────────────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────────────────────
    public function update(Request $request, string $id)
    {
        $vente = Vente::with('details')->findOrFail($id);

        $request->validate([
            'nom_client'                  => 'required|string|max:150',
            'charges'                     => 'required|numeric|min:0',
            'produits'                    => 'nullable|array',
            'produits.*.id_produit'       => 'required_with:produits|exists:produits,id_produit',
            'produits.*.quantite'         => 'required_with:produits|integer|min:1',
        ], [
            'nom_client.required'   => 'Le nom du client est obligatoire.',
            'charges.required'      => 'Les frais sont obligatoires.',
        ]);

        DB::beginTransaction();
        try {
            /* ── 1. Restaurer le stock des anciennes lignes ── */
            foreach ($vente->details as $detail) {
                $produit = Produit::find($detail->id_produit);
                if ($produit) {
                    $produit->increment('quantite_stock', $detail->quantite);
                    GestionStock::create([
                        'id_produit'     => $produit->id_produit,
                        'type_mouvement' => 'ENTREE',
                        'quantite'       => $detail->quantite,
                        'description'    => "Correction vente #{$vente->id_vente}",
                    ]);
                }
                $detail->delete();
            }

            /* ── 2. Vérification stock nouvelles lignes ── */
            $lignesProduits = array_values(
                array_filter($request->input('produits', []), fn($l) => !empty($l['id_produit']))
            );

            foreach ($lignesProduits as $item) {
                $produit = Produit::findOrFail($item['id_produit']);
                if ($produit->quantite_stock < (int) $item['quantite']) {
                    throw new \Exception(
                        "Stock insuffisant pour « {$produit->nom_produit} ». "
                        . "Disponible : {$produit->quantite_stock}"
                    );
                }
            }

            /* ── 3. Recréer les nouvelles lignes ── */
            $total = 0;

            foreach ($lignesProduits as $item) {
                $produit   = Produit::findOrFail($item['id_produit']);
                $qty       = (int) $item['quantite'];
                $prix      = $produit->prix_vente;
                $prixLigne = $prix * $qty;

                DetailVente::create([
                    'id_vente'    => $vente->id_vente,
                    'id_produit'  => $produit->id_produit,
                    'nom_produit' => $produit->nom_produit,
                    'quantite'    => $qty,
                    'prix_vente'  => $prix,
                    'prix_total'  => $prixLigne,
                ]);

                $produit->decrement('quantite_stock', $qty);
                GestionStock::create([
                    'id_produit'     => $produit->id_produit,
                    'type_mouvement' => 'SORTIE',
                    'quantite'       => $qty,
                    'description'    => "Mise à jour vente #{$vente->id_vente} — {$vente->nom_client}",
                ]);

                $total += $prixLigne;
            }

            /* ── 4. Mise à jour de la vente ── */
            $vente->update([
                'nom_client'    => $request->nom_client,
                'charges'       => $request->charges,
                'montant_total' => $total + $request->charges,
            ]);

            DB::commit();

            return redirect()
                ->route('vente.show', $vente->id_vente)
                ->with('success', 'Vente mise à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────────────────────
    public function destroy(string $id)
    {
        $vente = Vente::with('details')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Restaurer le stock
            foreach ($vente->details as $detail) {
                $produit = Produit::find($detail->id_produit);
                if ($produit) {
                    $produit->increment('quantite_stock', $detail->quantite);
                    GestionStock::create([
                        'id_produit'     => $produit->id_produit,
                        'type_mouvement' => 'ENTREE',
                        'quantite'       => $detail->quantite,
                        'description'    => "Annulation vente #{$vente->id_vente}",
                    ]);
                }
            }

            $vente->delete();
            DB::commit();

            return redirect()
                ->route('vente.index')
                ->with('success', 'Vente supprimée. Stock restauré automatiquement.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────
    // IMPRESSION PDF
    // ─────────────────────────────────────────────────────────────
    public function imprimerTicket(string $id)
    {
        $vente = Vente::with('details')->findOrFail($id);
        $entreprise = config('entreprise');

        $pdf = Pdf::loadView('pdf.vente', compact('vente', 'entreprise'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download("vente-{$vente->id_vente}.pdf");
    }

    // ─────────────────────────────────────────────────────────────
    // API — Info produit (AJAX)
    // ─────────────────────────────────────────────────────────────
    public function getProduitInfo(Produit $produit)
    {
        return response()->json([
            'id_produit'     => $produit->id_produit,
            'nom_produit'    => $produit->nom_produit,
            'prix_vente'     => $produit->prix_vente,
            'quantite_stock' => $produit->quantite_stock,
            'statut_stock'   => $produit->statut_stock,
        ]);
    }
}