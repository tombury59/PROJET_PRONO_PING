<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions_bonus', function (Blueprint $table) {
            $table->text('description')->nullable()->after('question');
        });
    }

    public function down(): void
    {
        Schema::table('questions_bonus', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
