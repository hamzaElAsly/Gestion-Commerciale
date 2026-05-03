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
            'produits' => 'required|array|min:1',
            'charges' => 'required|numeric|min:0',
            'produits.*.id_produit' => 'required|exists:produits,id_produit',
            'produits.*.quantite' => 'required|integer|min:1',
        ], [
            'id_client.required' => 'Veuillez sélectionner un client.',
            'date_service.required' => 'La date du service est obligatoire.',
            'produits.required' => 'Veuillez ajouter au moins un produit.',
            'produits.min' => 'Veuillez ajouter au moins un produit.',
            'charges.required' => 'Les frais de service sont obligatoires.',
            'charges.numeric' => 'Les frais de service doivent être un nombre valide.',
            'charges.min' => 'Les frais de service doivent être un nombre positif.',
        ]);

        DB::beginTransaction();

        try {
            // Vérifier le stock disponible pour chaque produit
            foreach ($validated['produits'] as $item) {
                $produit = Produit::findOrFail($item['id_produit']);
                if ($produit->quantite_stock < $item['quantite']) {
                    throw new \Exception("Stock insuffisant pour le produit : {$produit->nom_produit}. Stock disponible : {$produit->quantite_stock}");
                }
            }

            // Créer l'historique
            $historique = Historique::create([
                'id_client' => $validated['id_client'],
                'date_service' => $validated['date_service'],
                'charges' => $validated['charges'],
                'remarque' => $validated['remarque'] ?? null,
                'statut' => $validated['statut'],
                'montant_total' => 0,
            ]);

            $montantTotal = 0;

            // Créer les détails et décrémenter le stock
            foreach ($validated['produits'] as $item) {
                $produit = Produit::findOrFail($item['id_produit']);
                $prixTotal = ($produit->prix_vente * $item['quantite']) + $validated['charges'];
                // dd($produit->prix_vente, $validated['charges'], $prixTotal);
                DetailHistorique::create([
                    'id_historique' => $historique->id_historique,
                    'id_produit' => $produit->id_produit,
                    'quantite_utilisee' => $item['quantite'],
                    'prix_vente' => $produit->prix_vente,
                    'prix_total' => $prixTotal,
                ]);

                // Décrémenter le stock automatiquement
                $produit->decrementerStock(
                    $item['quantite'],
                    $historique->id_historique,
                    "Service client : {$historique->client->nom}"
                );

                $montantTotal += $prixTotal;
            }

            // Mettre à jour le montant total
            $historique->update(['montant_total' => $montantTotal]);

            DB::commit();

            return redirect()->route('historique.show', $historique)
                ->with('success', 'Service enregistré avec succès. Stock mis à jour automatiquement.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
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
        ]);

        $historique->update($validated);

        return redirect()->route('historique.show', $historique)
            ->with('success', 'Service modifié avec succès.');
    }

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