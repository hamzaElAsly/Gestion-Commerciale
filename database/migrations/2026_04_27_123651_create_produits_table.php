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
        Schema::create('produits', function (Blueprint $table) {
            $table->id('id_produit');
            $table->string('nom_produit', 150);
            $table->foreignId('id_categorie')->constrained('categories', 'id_categorie')->onDelete('restrict');
            $table->decimal('prix_unitaire', 10, 2);
            $table->integer('quantite_stock')->default(0);
            $table->integer('seuil_alerte')->default(5);
            $table->timestamp('date_ajout')->useCurrent();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
