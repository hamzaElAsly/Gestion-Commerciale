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
        Schema::create('detail_ventes', function (Blueprint $table) {
            $table->id('id_detail');
            $table->foreignId('id_vente')->constrained('ventes', 'id_vente')->cascadeOnDelete();
            $table->foreignId('id_produit')->constrained('produits', 'id_produit')->onDelete('restrict');
            $table->string('nom_produit', 150);
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
        Schema::dropIfExists('detail_ventes');
    }
};
