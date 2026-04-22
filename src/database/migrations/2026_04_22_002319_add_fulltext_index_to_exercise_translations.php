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
        Schema::table('exercise_translations', function (Blueprint $table) {
            $table->fullText('name');
        });
    }

    public function down(): void
    {
        Schema::table('exercise_translations', function (Blueprint $table) {
            $table->dropFullText(['name']);
        });
    }
};
