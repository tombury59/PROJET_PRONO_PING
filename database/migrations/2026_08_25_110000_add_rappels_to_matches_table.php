<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->boolean('rappel_j2_envoye')->default(false)->after('resultat_saisi');
            $table->boolean('rappel_24h_envoye')->default(false)->after('rappel_j2_envoye');
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn(['rappel_j2_envoye', 'rappel_24h_envoye']);
        });
    }
};
