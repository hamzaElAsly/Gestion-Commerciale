<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Historique;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoriqueTvaTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_sans_tva_conserve_le_total_ht(): void
    {
        [$historique, $produit] = $this->creerService(null);

        $this->assertSame('0.00', $historique->tva);
        $this->assertSame('1100.00', $historique->montant_total);
        $this->assertSame(8, $produit->fresh()->quantite_stock);
    }

    public function test_service_avec_tva_calcule_le_total_ttc_sans_affecter_davantage_le_stock(): void
    {
        [$historique, $produit] = $this->creerService(20);

        $this->assertSame('1320.00', $historique->montant_total);
        $this->assertSame(8, $produit->fresh()->quantite_stock);
    }

    public function test_modification_du_taux_et_des_lignes_recalcule_le_ttc_et_le_stock(): void
    {
        [$historique, $produit, $client] = $this->creerService(20);

        $this->actingAs(User::factory()->create())->put(route('historique.update', $historique), [
            'remarque' => 'Service modifié',
            'statut' => 'termine',
            'charges' => 100,
            'tva' => 10,
            'produits' => [
                ['id_produit' => $produit->id_produit, 'quantite' => 3],
            ],
        ])->assertRedirect(route('historique.show', $historique));

        $historique->refresh();
        $this->assertSame($client->id_client, $historique->id_client);
        $this->assertSame('10.00', $historique->tva);
        $this->assertSame('1760.00', $historique->montant_total);
        $this->assertSame(7, $produit->fresh()->quantite_stock);
    }

    /** @return array{Historique, Produit, Client} */
    private function creerService(float|int|null $tva): array
    {
        $client = Client::create(['nom' => 'Ahmed Alaoui']);
        $produit = Produit::create([
            'nom_produit' => 'Panneau solaire 550 W',
            'prix_unitaire' => 300,
            'prix_vente' => 500,
            'quantite_stock' => 10,
            'seuil_alerte' => 2,
        ]);
        $payload = [
            'id_client' => $client->id_client,
            'date_service' => '2026-08-11 10:00:00',
            'statut' => 'termine',
            'charges' => 100,
            'produits' => [
                ['id_produit' => $produit->id_produit, 'quantite' => 2],
            ],
        ];
        if ($tva !== null) {
            $payload['tva'] = $tva;
        }

        $this->actingAs(User::factory()->create())
            ->post(route('historique.store'), $payload)
            ->assertSessionHasNoErrors();

        return [Historique::latest('id_historique')->firstOrFail(), $produit, $client];
    }
}
