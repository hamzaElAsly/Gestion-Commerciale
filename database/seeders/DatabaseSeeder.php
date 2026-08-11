<?php

namespace Database\Seeders;

use App\Models\Categorie;
use App\Models\Client;
use App\Models\DetailDevis;
use App\Models\DetailHistorique;
use App\Models\Devis;
use App\Models\GestionStock;
use App\Models\Historique;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        DB::transaction(function (): void {
            User::updateOrCreate(
                ['email' => 'hamza@gmail.com'],
                [
                    'name' => 'Hamza',
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ]
            );

            $categories = collect([
                ['nom_categorie' => 'Panneaux solaires', 'description' => 'Modules photovoltaïques monocristallins.'],
                ['nom_categorie' => 'Onduleurs', 'description' => 'Conversion et gestion de l’énergie solaire.'],
                ['nom_categorie' => 'Batteries', 'description' => 'Stockage d’énergie pour installations solaires.'],
                ['nom_categorie' => 'Accessoires solaires', 'description' => 'Protection, câblage et fixation.'],
            ])->mapWithKeys(function (array $data): array {
                $categorie = Categorie::firstOrCreate(
                    ['nom_categorie' => $data['nom_categorie']],
                    ['description' => $data['description']]
                );

                return [$data['nom_categorie'] => $categorie];
            });

            $produitsData = [
                ['Panneau solaire monocristallin 550 W', 'Panneaux solaires', 1450, 1790, 24, 5, 'Module photovoltaïque haute performance pour installations résidentielles et professionnelles.'],
                ['Panneau solaire monocristallin 450 W', 'Panneaux solaires', 1120, 1390, 30, 6, 'Panneau solaire compact à cellules monocristallines.'],
                ['Onduleur hybride 5 kW', 'Onduleurs', 6900, 8290, 8, 2, 'Onduleur hybride monophasé compatible avec batteries lithium.'],
                ['Onduleur réseau 10 kW triphasé', 'Onduleurs', 11800, 13900, 5, 1, 'Onduleur triphasé pour installation photovoltaïque raccordée au réseau.'],
                ['Batterie lithium LiFePO4 5,12 kWh', 'Batteries', 13200, 15700, 10, 2, 'Batterie solaire lithium avec BMS intégré.'],
                ['Batterie GEL solaire 200 Ah', 'Batteries', 2650, 3190, 18, 4, 'Batterie GEL à décharge lente pour système solaire autonome.'],
                ['Régulateur MPPT 100 A', 'Accessoires solaires', 2150, 2690, 12, 3, 'Contrôleur de charge MPPT pour batteries 12/24/48 V.'],
                ['Câble solaire 6 mm² - rouleau 100 m', 'Accessoires solaires', 980, 1290, 20, 4, 'Câble photovoltaïque résistant aux UV et aux intempéries.'],
                ['Coffret de protection DC/AC', 'Accessoires solaires', 1350, 1750, 15, 3, 'Coffret avec disjoncteurs et parafoudres pour installation solaire.'],
                ['Kit de fixation aluminium pour 4 panneaux', 'Accessoires solaires', 820, 1090, 25, 5, 'Rails, crochets et brides pour toiture inclinée.'],
            ];

            $produits = collect();
            foreach ($produitsData as [$nom, $categorie, $achat, $vente, $stock, $seuil, $description]) {
                $attributes = [
                    'prix_unitaire' => $achat,
                    'prix_vente' => $vente,
                    'quantite_stock' => $stock,
                    'seuil_alerte' => $seuil,
                    'description' => $description,
                    'date_ajout' => now()->subDays(30),
                ];

                // La migration actuelle ne crée pas encore id_categorie.
                if (Schema::hasColumn('produits', 'id_categorie')) {
                    $attributes['id_categorie'] = $categories[$categorie]->id_categorie;
                }

                $produit = Produit::updateOrCreate(['nom_produit' => $nom], $attributes);
                $produits->put($nom, $produit);
            }

            $clientsData = [
                ['Youssef El Mansouri', '001589764000031', '0612345678', 'Hay Riad, Rabat'],
                ['Salma Benjelloun', '002478965000042', '0623456789', 'Maarif, Casablanca'],
                ['Omar Alaoui', '003698741000053', '0634567890', 'Guéliz, Marrakech'],
                ['Khadija Amrani', '004789632000064', '0645678901', 'Agdal, Fès'],
            ];

            $clients = collect();
            foreach ($clientsData as [$nom, $ice, $telephone, $adresse]) {
                $client = Client::updateOrCreate(
                    ['nom' => $nom],
                    ['ICE' => $ice, 'telephone' => $telephone, 'adresse' => $adresse]
                );
                $clients->put($nom, $client);
            }

            $services = [
                ['Installation solaire résidentielle 3 kW', 'Youssef El Mansouri', 2500, 25, [['Panneau solaire monocristallin 550 W', 6], ['Onduleur hybride 5 kW', 1], ['Kit de fixation aluminium pour 4 panneaux', 2]]],
                ['Installation d’un système de pompage solaire', 'Omar Alaoui', 3200, 18, [['Panneau solaire monocristallin 450 W', 4], ['Onduleur réseau 10 kW triphasé', 1], ['Câble solaire 6 mm² - rouleau 100 m', 1]]],
                ['Ajout d’un stockage lithium', 'Salma Benjelloun', 1200, 12, [['Batterie lithium LiFePO4 5,12 kWh', 1], ['Coffret de protection DC/AC', 1]]],
                ['Maintenance préventive d’une installation photovoltaïque', 'Khadija Amrani', 850, 7, [['Câble solaire 6 mm² - rouleau 100 m', 1], ['Coffret de protection DC/AC', 1]]],
                ['Installation solaire autonome pour maison', 'Youssef El Mansouri', 2800, 3, [['Panneau solaire monocristallin 450 W', 4], ['Batterie GEL solaire 200 Ah', 2], ['Régulateur MPPT 100 A', 1]]],
            ];

            foreach ($services as [$remarque, $clientNom, $charges, $jours, $lignes]) {
                $dateService = now()->subDays($jours)->setTime(10, 0);
                $historique = Historique::updateOrCreate(
                    ['id_client' => $clients[$clientNom]->id_client, 'remarque' => $remarque],
                    ['date_service' => $dateService, 'charges' => $charges, 'montant_total' => 0, 'statut' => 'termine']
                );

                $historique->details()->delete();
                GestionStock::where('id_historique', $historique->id_historique)->delete();
                $total = (float) $charges;

                foreach ($lignes as [$produitNom, $quantite]) {
                    $produit = $produits[$produitNom];
                    $prixTotal = (float) $produit->prix_vente * $quantite;

                    DetailHistorique::create([
                        'id_historique' => $historique->id_historique,
                        'id_produit' => $produit->id_produit,
                        'quantite_utilisee' => $quantite,
                        'prix_vente' => $produit->prix_vente,
                        'prix_total' => $prixTotal,
                    ]);

                    GestionStock::create([
                        'id_produit' => $produit->id_produit,
                        'type_mouvement' => 'SORTIE',
                        'quantite' => $quantite,
                        'date_mouvement' => $dateService,
                        'description' => $remarque,
                        'id_historique' => $historique->id_historique,
                    ]);

                    $total += $prixTotal;
                }

                $historique->update(['montant_total' => $total]);
            }

            // Recalcul déterministe du stock après les cinq services, même si le seeder est rejoué.
            foreach ($produitsData as [$nom, , , , $stockInitial]) {
                $sorties = GestionStock::where('id_produit', $produits[$nom]->id_produit)
                    ->where('type_mouvement', 'SORTIE')
                    ->sum('quantite');
                $produits[$nom]->update(['quantite_stock' => max(0, $stockInitial - $sorties)]);
            }

            $devisData = [
                ['Installation solaire villa 5 kW', 'Youssef El Mansouri', 20, [['Panneau solaire monocristallin 550 W', 10], ['Onduleur hybride 5 kW', 1], ['Kit de fixation aluminium pour 4 panneaux', 3]]],
                ['Système autonome avec batterie lithium', 'Salma Benjelloun', 20, [['Panneau solaire monocristallin 450 W', 6], ['Batterie lithium LiFePO4 5,12 kWh', 2], ['Régulateur MPPT 100 A', 1], ['Coffret de protection DC/AC', 1]]],
                ['Centrale solaire professionnelle 10 kW', 'Omar Alaoui', 20, [['Panneau solaire monocristallin 550 W', 18], ['Onduleur réseau 10 kW triphasé', 1], ['Câble solaire 6 mm² - rouleau 100 m', 2]]],
            ];

            foreach ($devisData as [$titre, $clientNom, $tva, $lignes]) {
                $devis = Devis::updateOrCreate(
                    ['titre' => $titre, 'nom_client' => $clientNom],
                    ['tva' => $tva, 'montant_total' => 0]
                );
                $devis->details()->delete();
                $sousTotal = 0;

                foreach ($lignes as [$produitNom, $quantite]) {
                    $produit = $produits[$produitNom];
                    $prixTotal = (float) $produit->prix_vente * $quantite;
                    DetailDevis::create([
                        'id_devis' => $devis->id_devis,
                        'nom_produit' => $produitNom,
                        'quantite' => $quantite,
                        'prix_vente' => $produit->prix_vente,
                        'prix_total' => $prixTotal,
                    ]);
                    $sousTotal += $prixTotal;
                }

                $devis->update(['montant_total' => $sousTotal * (1 + $tva / 100)]);
            }
        });
    }
}
