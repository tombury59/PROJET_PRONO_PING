<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reponses_bonus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_bonus_id')->constrained('questions_bonus')->cascadeOnDelete();
            $table->string('reponse');
            $table->unsignedTinyInteger('points_obtenus')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'question_bonus_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reponses_bonus');
    }
};
