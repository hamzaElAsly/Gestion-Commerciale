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
        Schema::create('gestion_stocks', function (Blueprint $table) {
            $table->id('id_stock');
            $table->foreignId('id_produit')->constrained('produits', 'id_produit')->onDelete('cascade');
            $table->enum('type_mouvement', ['ENTREE', 'SORTIE']);
            $table->integer('quantite');
            $table->timestamp('date_mouvement')->useCurrent();
            $table->text('description')->nullable();
            $table->foreignId('id_historique')->nullable()->constrained('historiques', 'id_historique')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gestion_stocks');
    }
};
