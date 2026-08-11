<?php

namespace Tests\Feature;

use App\Models\Devis;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DevisTvaTest extends TestCase
{
    use RefreshDatabase;

    public function test_devis_sans_tva_enregistre_le_total_ht_comme_ttc(): void
    {
        $devis = $this->creerDevis(null);

        $this->assertSame('0.00', $devis->tva);
        $this->assertSame('1500.00', $devis->montant_total);
    }

    public function test_devis_avec_tva_de_20_pourcent_calcule_le_ttc_cote_serveur(): void
    {
        $devis = $this->creerDevis(20);

        $this->assertSame('1800.00', $devis->montant_total);
    }

    public function test_devis_accepte_une_tva_decimale(): void
    {
        $devis = $this->creerDevis(20.5);

        $this->assertSame('20.50', $devis->tva);
        $this->assertSame('1807.50', $devis->montant_total);
    }

    public function test_modification_du_taux_recalcule_le_total_ttc(): void
    {
        $devis = $this->creerDevis(20);

        $this->actingAs(User::factory()->create())->put(route('devis.update', $devis), [
            'titre' => 'Installation mise à jour',
            'nom_client' => 'Client Test',
            'tva' => 10,
            'produits' => [
                ['nom_produit' => 'Panneau solaire', 'prix' => 750, 'quantite' => 2],
            ],
        ])->assertRedirect(route('devis.show', $devis));

        $devis->refresh();
        $this->assertSame('10.00', $devis->tva);
        $this->assertSame('1650.00', $devis->montant_total);
    }

    private function creerDevis(float|int|null $tva): Devis
    {
        $payload = [
            'titre' => 'Installation solaire',
            'nom_client' => 'Client Test',
            'produits' => [
                ['nom_produit' => 'Panneau solaire', 'prix' => 750, 'quantite' => 2],
            ],
        ];
        if ($tva !== null) {
            $payload['tva'] = $tva;
        }

        $this->actingAs(User::factory()->create())
            ->post(route('devis.store'), $payload)
            ->assertSessionHasNoErrors();

        return Devis::latest('id_devis')->firstOrFail();
    }
}
