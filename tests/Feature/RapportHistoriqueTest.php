<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\DetailHistorique;
use App\Models\Historique;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RapportHistoriqueTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Client $clientA;
    private Client $clientB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->clientA = Client::create(['nom' => 'Client A']);
        $this->clientB = Client::create(['nom' => 'Client B']);
        $produit = Produit::create([
            'nom_produit' => 'Onduleur', 'prix_unitaire' => 100, 'prix_vente' => 200,
            'quantite_stock' => 20, 'seuil_alerte' => 2,
        ]);

        $this->ajouterService($this->clientA, $produit, '2026-08-11 10:00:00', 1200);
        $this->ajouterService($this->clientA, $produit, '2026-08-12 10:00:00', 600);
        $this->ajouterService($this->clientB, $produit, '2026-09-11 10:00:00', 300);
        $this->ajouterService($this->clientA, $produit, '2025-08-11 10:00:00', 900);
    }

    public function test_filtre_annee_uniquement_et_totaux(): void
    {
        $this->verifierRapport(['annee' => 2026], 3, 2100.0, 300.0);
    }

    public function test_filtre_annee_et_mois(): void
    {
        $this->verifierRapport(['annee' => 2026, 'mois' => 8], 2, 1800.0, 200.0);
    }

    public function test_filtre_annee_mois_et_jour(): void
    {
        $this->verifierRapport(['annee' => 2026, 'mois' => 8, 'jour' => 11], 1, 1200.0, 100.0);
    }

    public function test_filtre_client_et_annee(): void
    {
        $this->verifierRapport(['annee' => 2026, 'id_client' => $this->clientA->id_client], 2, 1800.0, 200.0);
    }

    public function test_filtre_client_annee_et_mois(): void
    {
        $this->verifierRapport(['annee' => 2026, 'mois' => 9, 'id_client' => $this->clientB->id_client], 1, 300.0, 100.0);
    }

    public function test_filtre_client_annee_mois_et_jour(): void
    {
        $this->verifierRapport(['annee' => 2026, 'mois' => 8, 'jour' => 12, 'id_client' => $this->clientA->id_client], 1, 600.0, 100.0);
    }

    private function verifierRapport(array $filtres, int $nombre, float $total, float $cout): void
    {
        $response = $this->actingAs($this->user)->get(route('historique.mensuel', $filtres));
        $response->assertOk();
        $response->assertViewHas('historiques', fn ($items) => $items->count() === $nombre);
        $response->assertViewHas('totalVenteMois', fn ($valeur) => abs((float) $valeur - $total) < 0.001);
        $response->assertViewHas('totalAchatMois', fn ($valeur) => abs((float) $valeur - $cout) < 0.001);
    }

    private function ajouterService(Client $client, Produit $produit, string $date, float $total): void
    {
        $historique = Historique::create([
            'id_client' => $client->id_client,
            'date_service' => $date,
            'charges' => $total - 200,
            'tva' => 0,
            'montant_total' => $total,
            'statut' => 'termine',
        ]);
        DetailHistorique::create([
            'id_historique' => $historique->id_historique,
            'id_produit' => $produit->id_produit,
            'quantite_utilisee' => 1,
            'prix_vente' => 200,
            'prix_total' => 200,
        ]);
    }
}
