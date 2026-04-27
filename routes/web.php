<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\HistoriqueController;
use App\Http\Controllers\StockController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Clients
    Route::resource('clients', ClientController::class);

    // Catégories
    Route::resource('categories', CategorieController::class)->except(['show']);

    // Produits
    Route::resource('produits', ProduitController::class);
    Route::post('/produits/{produit}/ajouter-stock', [ProduitController::class, 'ajouterStock'])
        ->name('produits.ajouter-stock');

    // Historique des services
    Route::resource('historique', HistoriqueController::class);
    Route::get('/historique-mensuel', [HistoriqueController::class, 'imprimerMensuel'])
        ->name('historique.mensuel');
    Route::get('/historique/{historique}/facture', [HistoriqueController::class, 'imprimerFacture'])
        ->name('historique.facture');
    Route::get('/api/produits/{produit}/info', [HistoriqueController::class, 'getProduitInfo'])
        ->name('api.produit.info');

    // Gestion du stock
    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('/stock/etat', [StockController::class, 'etatStock'])->name('stock.etat');
    Route::post('/stock/entree', [StockController::class, 'entreeStock'])->name('stock.entree');

// });

require __DIR__.'/auth.php';