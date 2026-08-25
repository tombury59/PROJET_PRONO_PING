<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pronostics', function (Blueprint $table) {
            $table->boolean('joker')->default(false)->after('prono_score_j2');
        });
    }

    public function down(): void
    {
        Schema::table('pronostics', function (Blueprint $table) {
            $table->dropColumn('joker');
        });
    }
};
