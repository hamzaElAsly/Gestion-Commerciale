<?php

namespace App\Http\Controllers;

use App\Models\Historique;
use App\Models\Client;
use App\Models\Produit;
use App\Models\DetailHistorique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class HistoriqueController extends Controller
{
    public function index(Request $request)
    {
        $query = Historique::with(['client', 'details'])
            ->orderBy('date_service', 'desc');

        if ($request->filled('client_id')) {
            $query->where('id_client', $request->client_id);
        }

        if ($request->filled('mois') && $request->filled('annee')) {
            $query->whereMonth('date_service', $request->mois)
                  ->whereYear('date_service', $request->annee);
        } elseif ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_service', [$request->date_debut, $request->date_fin]);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $historiques = $query->paginate(15)->withQueryString();
        $clients = Client::orderBy('nom')->get();

        $totalFiltré = $query->sum('montant_total');

        return view('historique.index', compact('historiques', 'clients', 'totalFiltré'));
    }

    public function create()
    {
        $clients = Client::orderBy('nom')->get();
        $produits = Produit::with('categorie')
            ->where('quantite_stock', '>', 0)
            ->orderBy('nom_produit')
            ->get();

        return view('historique.create', compact('clients', 'produits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_client' => 'required|exists:clients,id_client',
            'date_service' => 'required|date',
            'remarque' => 'nullable|string',
            'statut' => 'required|in:en_cours,termine,annule',
            'produits' => 'nullable|array',
            'produits.*.id_produit' => 'required_with:produits|exists:produits,id_produit',
            'produits.*.quantite' => 'required_with:produits|integer|min:1',
            'charges' => 'numeric|min:0',
        ], [
            'id_client.required' => 'Veuillez sélectionner un client.',
            'date_service.required' => 'La date du service est obligatoire.',
            'charges.numeric' => 'Les frais doivent être un nombre.',
            'charges.min' => 'Les frais doivent être positifs.',
        ]);

        DB::beginTransaction();

        try {
            // ✔ produits optionnel
            $produits = $validated['produits'] ?? [];
            // ✅ Vérification stock
            if (!empty($produits)) {
                foreach ($produits as $item) {
                    $produit = Produit::findOrFail($item['id_produit']);

                    if ($produit->quantite_stock < $item['quantite']) {
                        throw new \Exception(
                            "Stock insuffisant pour : {$produit->nom_produit} (Disponible: {$produit->quantite_stock})"
                        );
                    }
                }
            }

            // ✅ Création historique
            $historique = Historique::create([
                'id_client' => $validated['id_client'],
                'date_service' => $validated['date_service'],
                'charges' => $validated['charges'],
                'remarque' => $validated['remarque'] ?? null,
                'statut' => $validated['statut'],
                'montant_total' => 0,
            ]);
            $montantTotal = 0;

            // ✅ Ajout produits (optionnel)
            if (!empty($produits)) {
                foreach ($produits as $item) {
                    $produit = Produit::findOrFail($item['id_produit']);
                    $prixTotal = $produit->prix_vente * $item['quantite'];
                    DetailHistorique::create([
                        'id_historique' => $historique->id_historique,
                        'id_produit' => $produit->id_produit,
                        'quantite_utilisee' => $item['quantite'],
                        'prix_vente' => $produit->prix_vente,
                        'prix_total' => $prixTotal,
                    ]);
                    // Décrément stock
                    $produit->decrementerStock(
                        $item['quantite'],
                        $historique->id_historique,
                        "Service client : {$historique->client->nom}"
                    );
                    $montantTotal += $prixTotal;
                }
            }

            // ✅ Ajouter charges même sans produits
            $montantTotal += $validated['charges'];
            // ✅ Mise à jour total
            $historique->update([
                'montant_total' => $montantTotal
            ]);
            DB::commit();
            return redirect()
                ->route('historique.show', $historique)
                ->with('success', 'Service enregistré avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(Historique $historique)
    {
        $historique->load(['client', 'details.produit.categorie', 'mouvementsStock.produit']);
        $historique->loadSum('details', 'prix_total');
        return view('historique.show', compact('historique'));
    }

    public function edit(Historique $historique)
    {
        $historique->load(['client', 'details.produit']);
        $clients = Client::orderBy('nom')->get();
        $produits = Produit::with('categorie')->orderBy('nom_produit')->get();

        return view('historique.edit', compact('historique', 'clients', 'produits'));
    }

    public function update(Request $request, Historique $historique)
    {
        $validated = $request->validate([
            'remarque' => 'nullable|string',
            'statut' => 'required|in:en_cours,termine,annule',
            'produits' => 'nullable|array',
            'produits.*.id_produit' => 'required_with:produits|exists:produits,id_produit',
            'produits.*.quantite' => 'required_with:produits|integer|min:1',
            'charges' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $produits = $validated['produits'] ?? [];
            // 📌 1. Récupérer anciens produits
            $anciensDetails = $historique->details()->get()->keyBy('id_produit');
            // 📌 2. Remettre stock (rollback total ancien)
            foreach ($anciensDetails as $detail) {
                $produit = Produit::find($detail->id_produit);
                if ($produit) {
                    $produit->increment('quantite_stock', $detail->quantite_utilisee);
                }
            }
            // 📌 3. Supprimer anciens détails
            $historique->details()->delete();
            $montantTotal = 0;

            // 📌 4. Ajouter nouveaux produits
            if (!empty($produits)) {
                foreach ($produits as $item) {
                    $produit = Produit::findOrFail($item['id_produit']);
                    // 🔴 Vérifier stock
                    if ($produit->quantite_stock < $item['quantite']) {
                        throw new \Exception("Stock insuffisant pour : {$produit->nom_produit}");
                    }
                    $prixTotal = $produit->prix_vente * $item['quantite'];
                    DetailHistorique::create([
                        'id_historique' => $historique->id_historique,
                        'id_produit' => $produit->id_produit,
                        'quantite_utilisee' => $item['quantite'],
                        'prix_vente' => $produit->prix_vente,
                        'prix_total' => $prixTotal,
                    ]);
                    // 🔻 Décrément stock
                    $produit->decrement('quantite_stock', $item['quantite']);
                    $montantTotal += $prixTotal;
                }
            }

            // 📌 5. Mettre à jour historique
            $historique->update([
                'remarque' => $validated['remarque'] ?? null,
                'statut' => $validated['statut'],
                'charges' => $validated['charges'],
                'montant_total' => $montantTotal + ($validated['charges'] ?? 0),
            ]);

            DB::commit();

            return redirect()->route('historique.show', $historique)
                ->with('success', 'Service modifié avec succès.');

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
    // public function update(Request $request, Historique $historique)
    // {
    //     $validated = $request->validate([
    //         'remarque' => 'nullable|string',
    //         'statut' => 'required|in:en_cours,termine,annule',
    //     ]);

    //     $historique->update($validated);

    //     return redirect()->route('historique.show', $historique)
    //         ->with('success', 'Service modifié avec succès.');
    // }

    public function destroy(Historique $historique)
    {
        DB::beginTransaction();
        try {
            // Restaurer le stock pour chaque produit
            foreach ($historique->details as $detail) {
                $produit = $detail->produit;
                $produit->increment('quantite_stock', $detail->quantite_utilisee);

                // Enregistrer le mouvement de retour
                $produit->mouvementsStock()->create([
                    'type_mouvement' => 'ENTREE',
                    'quantite' => $detail->quantite_utilisee,
                    'description' => "Annulation du service #{$historique->id_historique}",
                ]);
            }

            $historique->delete();
            DB::commit();

            return redirect()->route('historique.index')
                ->with('success', 'Service supprimé. Le stock a été restauré.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    public function imprimerFacture(Historique $historique)
    {
        $historique->load(['client', 'details.produit.categorie']);
        $pdf = Pdf::loadView('pdf.facture', compact('historique'))->setPaper('a4', 'portrait');
        return $pdf->download("facture-service-{$historique->id_historique}.pdf");
    }

    public function imprimerMensuel(Request $request)
    {
        $mois  = (int) $request->get('mois', now()->month);
        $annee = (int) $request->get('annee', now()->year);
        $idClient = $request->get('id_client');

        $query = Historique::with(['client', 'details.produit'])
            ->whereMonth('date_service', $mois)
            ->whereYear('date_service', $annee)
            ->orderBy('date_service');

        if ($idClient) {
            $query->where('id_client', $idClient);
        }

        $historiques = $query->get();
        $totalMois = $historiques->sum('montant_total');
        $nomMois = \Carbon\Carbon::create()->month($mois)->locale('fr')->monthName;
        $clients = Client::orderBy('nom')->get();

        if ($request->has('export_pdf')) {
            $pdf = Pdf::loadView('pdf.historique-mensuel', compact(
                'historiques', 'totalMois', 'nomMois', 'annee', 'mois'
            ))->setPaper('a4', 'portrait');
            return $pdf->download("historique-{$nomMois}-{$annee}.pdf");
        }

        return view('historique.mensuel', compact(
            'historiques', 'totalMois', 'nomMois', 'annee', 'mois', 'clients'
        ));
    }

    public function getProduitInfo(Produit $produit)
    {
        return response()->json([
            'id_produit' => $produit->id_produit,
            'nom_produit' => $produit->nom_produit,
            'prix_vente' => $produit->prix_vente,
            'quantite_stock' => $produit->quantite_stock,
            'statut_stock' => $produit->statut_stock,
        ]);
    }
}