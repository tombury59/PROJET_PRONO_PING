<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phase_id')->constrained()->cascadeOnDelete();
            $table->string('joueur_1');
            $table->string('joueur_2');
            $table->dateTime('date_heure');
            $table->unsignedTinyInteger('score_j1')->nullable();
            $table->unsignedTinyInteger('score_j2')->nullable();
            $table->boolean('resultat_saisi')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
