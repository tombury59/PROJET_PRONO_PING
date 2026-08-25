<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Passage d'un affrontement de joueurs (avec partenaires pour les
        // doubles) à une rencontre par équipes (club contre club). Le score
        // représente le nombre de matchs gagnés par chaque équipe, sur un
        // total `nb_matchs` (18 en général, 14 en région).
        Schema::table('matches', function (Blueprint $table) {
            $table->renameColumn('joueur_1', 'equipe_1');
            $table->renameColumn('joueur_2', 'equipe_2');
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn(['joueur_1_partenaire', 'joueur_2_partenaire']);
            $table->unsignedTinyInteger('nb_matchs')->default(18)->after('equipe_2');
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn('nb_matchs');
            $table->string('joueur_1_partenaire')->nullable()->after('equipe_1');
            $table->string('joueur_2_partenaire')->nullable()->after('equipe_2');
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->renameColumn('equipe_1', 'joueur_1');
            $table->renameColumn('equipe_2', 'joueur_2');
        });
    }
};
