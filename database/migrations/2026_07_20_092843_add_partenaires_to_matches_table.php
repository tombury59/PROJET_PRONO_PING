<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->string('joueur_1_partenaire')->nullable()->after('joueur_1');
            $table->string('joueur_2_partenaire')->nullable()->after('joueur_2');
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn(['joueur_1_partenaire', 'joueur_2_partenaire']);
        });
    }
};
