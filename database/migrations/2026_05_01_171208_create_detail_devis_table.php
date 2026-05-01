<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_devis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_devis')->constrained('devis', 'id_devis')->cascadeOnDelete();
            $table->foreignId('id_produit')->constrained('produits', 'id_produit');
            $table->integer('quantite');
            $table->decimal('prix_vente', 10, 2);
            $table->decimal('prix_total', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_devis');
    }
};
