<?php

namespace App\Http\Controllers;

use App\Models\Historique;
use App\Models\Client;
use App\Models\Produit;
use App\Models\DetailHistorique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

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
            'tva' => ['nullable', 'numeric', 'min:0', 'max:100'],
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
            $charges = (float) ($validated['charges'] ?? 0);
            $tva = (float) ($validated['tva'] ?? 0);
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
                'charges' => $charges,
                'tva' => $tva,
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
            $montantHt = $montantTotal + $charges;
            $montantTva = round($montantHt * $tva / 100, 2);
            $historique->update([
                'montant_total' => round($montantHt + $montantTva, 2),
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
            'tva' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        DB::beginTransaction();
        try {
            $produits = $validated['produits'] ?? [];
            $charges = (float) ($validated['charges'] ?? 0);
            $tva = (float) ($validated['tva'] ?? 0);
            // 📌 1. Récupérer anciens produits
            $anciensDetails = $historique->details()->get()->keyBy('id_produit');
            // 📌 2. Remettre stock (rollback total ancien)
            foreach ($anciensDetails as $detail) {
                $produit = Produit::find($detail->id_produit);
                if ($produit) {
                    $produit->increment('quantite_stock', $detail->quantite_utilisee);
                    $produit->mouvementsStock()->create([
                        'type_mouvement' => 'ENTREE',
                        'quantite' => $detail->quantite_utilisee,
                        'description' => "Restauration avant modification du service #{$historique->id_historique}",
                        'id_historique' => $historique->id_historique,
                    ]);
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
                    $produit->decrementerStock(
                        $item['quantite'],
                        $historique->id_historique,
                        "Modification du service #{$historique->id_historique}"
                    );
                    $montantTotal += $prixTotal;
                }
            }

            // 📌 5. Mettre à jour historique
            $montantHt = $montantTotal + $charges;
            $montantTva = round($montantHt * $tva / 100, 2);
            $historique->update([
                'remarque' => $validated['remarque'] ?? null,
                'statut' => $validated['statut'],
                'charges' => $charges,
                'tva' => $tva,
                'montant_total' => round($montantHt + $montantTva, 2),
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
        $filtres = $request->validate([
            'annee' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'mois' => ['nullable', 'integer', 'between:1,12', 'required_with:jour'],
            'jour' => ['nullable', 'integer', 'between:1,31'],
            'id_client' => ['nullable', 'integer', 'exists:clients,id_client'],
        ]);

        $filtres['annee'] = (int) ($filtres['annee'] ?? now()->year);
        $filtres['mois'] = isset($filtres['mois']) ? (int) $filtres['mois'] : null;
        $filtres['jour'] = isset($filtres['jour']) ? (int) $filtres['jour'] : null;

        if ($filtres['jour'] && ! checkdate($filtres['mois'], $filtres['jour'], $filtres['annee'])) {
            throw ValidationException::withMessages(['jour' => 'Le jour sélectionné est invalide pour ce mois.']);
        }

        $query = $this->appliquerFiltresRapport(
            Historique::with(['client', 'details.produit'])->orderBy('date_service'),
            $filtres
        );
        $historiques = $query->get();
        $totalVenteMois = (float) $historiques->sum('montant_total');
        $totalHt = (float) $historiques->sum(fn (Historique $historique) => $historique->total_ht);
        $totalTva = round($totalVenteMois - $totalHt, 2);

        $detailsQuery = DetailHistorique::query()
            ->join('produits', 'detail_historiques.id_produit', '=', 'produits.id_produit')
            ->whereHas('historique', fn (Builder $query) => $this->appliquerFiltresRapport($query, $filtres));
        $totalAchatMois = (float) $detailsQuery
            ->sum(DB::raw('detail_historiques.quantite_utilisee * produits.prix_unitaire'));

        $annee = $filtres['annee'];
        $mois = $filtres['mois'];
        $jour = $filtres['jour'];
        $idClient = $filtres['id_client'] ?? null;
        $nomMois = $mois ? Carbon::create()->month($mois)->locale('fr')->monthName : null;
        $titreRapport = $this->titreRapport($annee, $mois, $jour);
        $clients = Client::orderBy('nom')->get();

        if ($request->has('export_pdf')) {
            $pdf = Pdf::loadView('pdf.historique-mensuel', compact('historiques', 'totalVenteMois', 'totalHt', 'totalTva', 'nomMois', 'annee', 'mois', 'jour', 'idClient', 'totalAchatMois', 'titreRapport'))
                        ->setPaper('a4', 'portrait');
            return $pdf->download($this->nomFichierRapport($annee, $mois, $jour));
        }
        return view('historique.mensuel', compact('historiques', 'totalVenteMois', 'totalHt', 'totalTva', 'nomMois', 'annee', 'mois', 'jour', 'idClient', 'clients', 'totalAchatMois', 'titreRapport'));
    }

    private function appliquerFiltresRapport(Builder $query, array $filtres): Builder
    {
        return $query
            ->whereYear('date_service', $filtres['annee'])
            ->when($filtres['mois'] ?? null, fn (Builder $query, int $mois) => $query->whereMonth('date_service', $mois))
            ->when($filtres['jour'] ?? null, fn (Builder $query, int $jour) => $query->whereDay('date_service', $jour))
            ->when($filtres['id_client'] ?? null, fn (Builder $query, int $client) => $query->where('id_client', $client));
    }

    private function titreRapport(int $annee, ?int $mois, ?int $jour): string
    {
        if ($mois && $jour) {
            return 'Rapport journalier — '.Carbon::create($annee, $mois, $jour)->locale('fr')->translatedFormat('d F Y');
        }

        if ($mois) {
            return 'Rapport mensuel — '.ucfirst(Carbon::create()->month($mois)->locale('fr')->monthName)." {$annee}";
        }

        return "Rapport annuel — {$annee}";
    }

    private function nomFichierRapport(int $annee, ?int $mois, ?int $jour): string
    {
        $nom = "rapport-{$annee}";
        $nom .= $mois ? '-'.str_pad((string) $mois, 2, '0', STR_PAD_LEFT) : '';
        $nom .= $jour ? '-'.str_pad((string) $jour, 2, '0', STR_PAD_LEFT) : '';

        return "{$nom}.pdf";
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
