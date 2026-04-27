<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Produit;
use App\Models\Historique;
use App\Models\GestionStock;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
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

        // Services du mois en cours
        $servicesParMois = Historique::selectRaw('
            MONTH(date_service) as mois,
            YEAR(date_service) as annee,
            COUNT(*) as nombre,
            SUM(montant_total) as total
        ')
        ->whereYear('date_service', now()->year)
        ->groupBy('mois', 'annee')
        ->orderBy('mois')
        ->get();

        // Derniers services
        $derniersServices = Historique::with(['client', 'details.produit'])
            ->orderBy('date_service', 'desc')
            ->limit(5)
            ->get();

        // Produits en alerte
        $alertesStock = Produit::with('categorie')
            ->stockFaible()
            ->orderBy('quantite_stock')
            ->limit(10)
            ->get();

        // Top clients
        $topClients = Client::withCount('historiques')
            ->withSum('historiques as total_depense', 'montant_total')
            ->orderByDesc('total_depense')
            ->limit(5)
            ->get();

        // Mouvements récents
        $derniersMouvements = GestionStock::with('produit')
            ->orderBy('date_mouvement', 'desc')
            ->limit(8)
            ->get();

        return view('dashboard.index', compact(
            'stats',
            'servicesParMois',
            'derniersServices',
            'alertesStock',
            'topClients',
            'derniersMouvements'
        ));
    }
}