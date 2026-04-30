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
        Schema::create('historiques', function (Blueprint $table) {
            $table->id('id_historique');
            $table->foreignId('id_client')->constrained('clients', 'id_client')->onDelete('cascade');
            $table->timestamp('date_service')->useCurrent();
            $table->decimal('charges', 10, 2)->default(0);
            $table->decimal('montant_total', 10, 2)->default(0);
            $table->text('remarque')->nullable();
            $table->enum('statut', ['en_cours', 'termine', 'annule'])->default('termine');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historiques');
    }
};
