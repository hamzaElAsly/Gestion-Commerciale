<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            if (Schema::hasColumn('devis', 'tva')) {
                $table->decimal('tva', 5, 2)->default(0)->change();
            } else {
                $table->decimal('tva', 5, 2)->default(0)->after('nom_client');
            }
        });

        Schema::table('historiques', function (Blueprint $table) {
            $table->decimal('tva', 5, 2)->default(0)->after('charges');
        });
    }

    public function down(): void
    {
        Schema::table('historiques', function (Blueprint $table) {
            $table->dropColumn('tva');
        });

        Schema::table('devis', function (Blueprint $table) {
            $table->decimal('tva', 10, 2)->default(0)->change();
        });
    }
};
