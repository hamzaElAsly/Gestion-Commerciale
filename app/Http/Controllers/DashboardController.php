<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Client;
use App\Models\DetailHistorique;
use App\Models\Produit;
use App\Models\Vente;
use App\Models\DetailVente;
use App\Models\GestionStock;
use App\Models\Historique;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $aujourdHui  = Carbon::today();
        $debutMois   = Carbon::now()->startOfMonth();
        $finMois     = Carbon::now()->endOfMonth();
        $debutAnnee  = Carbon::now()->startOfYear();
        $moisPrecedent = Carbon::now()->subMonth();

        // BLOC 1 — CHIFFRE D'AFFAIRES ════════════════════════════════════════════════

        // CA aujourd'hui
        $caAujourdhui = Vente::whereDate('created_at', $aujourdHui)->sum('montant_total') + Historique::where('statut', 'termine')
        ->whereDate('date_service', $aujourdHui)
        ->sum('montant_total');
        
        // CA ce mois
        $caMois = Vente::whereBetween('created_at', [$debutMois, $finMois])->sum('montant_total') 
                + Historique::where('statut', 'termine')->whereBetween('date_service', [$debutMois, $finMois]) ->sum('montant_total');
        
        // CA total all-time
        $caTotal = Vente::sum('montant_total') + Historique::where('statut', 'termine') ->sum('montant_total');
        
        // CA mois précédent (pour delta %)
        $caMoisPrecedent = Vente::whereMonth('created_at', $moisPrecedent->month)->whereYear('created_at', $moisPrecedent->year)
            ->sum('montant_total');

        // Delta % mois vs mois précédent
        $deltaCaMois = $caMoisPrecedent > 0 ? round((($caMois - $caMoisPrecedent) / $caMoisPrecedent) * 100, 1) : ($caMois > 0 ? 100 : 0);

        // BLOC 2 — PRODUITS VENDUS & ACHETÉS ═════════════════════════════════════════

        $nbVentesAujourdhui = Vente::whereDate('created_at', $aujourdHui)->count();
        $nbVentesMois       = Vente::whereBetween('created_at', [$debutMois, $finMois])->count();
        $nbVentesTotal      = Vente::count();

        $totalPrixVentes = Vente::sum('montant_total')+ Historique::where('statut', 'termine')->sum('montant_total');
        $totalAchatsVentes = DetailVente::join('produits','detail_ventes.id_produit','=','produits.id_produit')
            ->selectRaw('SUM(detail_ventes.quantite * produits.prix_unitaire) as total')
            ->value('total') ?? 0;
        $totalAchatsServices = DetailHistorique::join('produits','detail_historiques.id_produit','=','produits.id_produit')
            ->selectRaw('SUM(detail_historiques.quantite_utilisee * produits.prix_unitaire) as total')
            ->value('total') ?? 0;
        $totalPrixAchats = $totalAchatsVentes + $totalAchatsServices;
        $profit = $totalPrixVentes - $totalPrixAchats;

        // BLOC 4 — STOCK & PRODUITS ══════════════════════════════════════════════════

        $totalProduits       = Produit::count();
        $produitsEnStock     = Produit::where('quantite_stock', '>', 0)->count();
        $produitsRupture     = Produit::where('quantite_stock', '=', 0)->count();
        $produitsFaibleStock = Produit::whereColumn('quantite_stock', '<=', 'seuil_alerte')->where('quantite_stock', '>', 0)->count();

        // BLOC 5 — MOUVEMENTS STOCK (Produits vendus / achetés) ══════════════════════

        // Total unités vendues (sorties de stock)
        $totalUnitesSorties = GestionStock::where('type_mouvement', 'SORTIE')->sum('quantite');
        $totalUnitesEntrees = GestionStock::where('type_mouvement', 'ENTREE')->sum('quantite');

        // Unités vendues ce mois
        $unitesSortiesMois = GestionStock::where('type_mouvement', 'SORTIE')
            ->whereBetween('created_at', [$debutMois, $finMois])->sum('quantite');

        // Unités entrées ce mois
        $unitesEntreesMois = GestionStock::where('type_mouvement', 'ENTREE')
            ->whereBetween('created_at', [$debutMois, $finMois])
            ->sum('quantite');

        // Nombre de références vendues ce mois (produits distincts)
        $referencesVenduesMois = DetailVente::whereHas('vente', function ($q) use ($debutMois, $finMois) {
                $q->whereBetween('created_at', [$debutMois, $finMois]);
            })
            ->distinct('id_produit')
            ->count('id_produit');

        // BLOC 6 — TOP PRODUITS LES PLUS VENDUS ══════════════════════════════════════
        $topProduits = DetailVente::select(
                'nom_produit',
                'id_produit',
                DB::raw('SUM(quantite) as total_vendu'),
                DB::raw('SUM(prix_total) as chiffre_affaires')
            )
            ->groupBy('id_produit', 'nom_produit')
            ->orderByDesc('total_vendu')
            ->limit(6)
            ->get();
        // ══════════════════════════════════════════════════════════════════════════════════════════════════════

        // Statistiques générales
        $stats = [
            'total_clients' => Client::count(),
            'total_produits' => Produit::count(),
            'total_categories' => Categorie::count(),
            'total_services' => Historique::count(),
            'ca_total' => Historique::where('statut', 'termine')->sum('montant_total'),
            'ca_mois' => Historique::where('statut', 'termine')
                ->whereMonth('date_service', now()->month)
                ->whereYear('date_service', now()->year)
                ->sum('montant_total'),
            'produits_faible_stock' => Produit::stockFaible()->count(),
            'produits_rupture' => Produit::enRupture()->count(),
        ];

        // Derniers services
        $derniersServices = Historique::with(['client', 'details.produit'])->orderBy('date_service', 'desc')->limit(5)->get();

        // Produits en alerte
        $alertesStock = Produit::with('categorie')->stockFaible()->orderBy('quantite_stock')->limit(5)->get();

        // Top clients
        $topClients = Client::withCount('historiques')->withSum('historiques as total_depense', 'montant_total')
            ->orderByDesc('total_depense')->limit(5)->get();

        // Mouvements récents
        $derniersMouvements = GestionStock::with('produit')->orderBy('date_mouvement', 'desc')->limit(8)->get();

        return view('dashboard.index', compact(
            // CA
            'caAujourdhui', 'caMois', 'caMoisPrecedent', 'caTotal', 'deltaCaMois', 
            // Services
            'derniersServices',
            // Ventes
            'nbVentesAujourdhui', 'nbVentesMois', 'nbVentesTotal',
            // Produits
            'totalPrixVentes', 'totalPrixAchats', 'profit',
            // Stock
            'totalProduits', 'produitsEnStock', 'produitsRupture', 'produitsFaibleStock', 'alertesStock',
            // Mouvements
            'totalUnitesSorties', 'totalUnitesEntrees','derniersMouvements',
            'unitesSortiesMois', 'unitesEntreesMois', 'referencesVenduesMois',
            // Listes
            'topProduits', 'topClients'
        ));
    }
}