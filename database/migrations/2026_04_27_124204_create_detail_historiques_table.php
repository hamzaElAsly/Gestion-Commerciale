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
        Schema::create('detail_historiques', function (Blueprint $table) {
            $table->id('id_detail');
            $table->foreignId('id_historique')->constrained('historiques', 'id_historique')->onDelete('cascade');
            $table->foreignId('id_produit')->constrained('produits', 'id_produit')->onDelete('restrict');
            $table->integer('quantite_utilisee');
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
        Schema::dropIfExists('detail_historiques');
    }
};
