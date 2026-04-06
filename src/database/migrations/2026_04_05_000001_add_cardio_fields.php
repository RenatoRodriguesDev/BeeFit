<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tipo de exercício
        Schema::table('exercises', function (Blueprint $table) {
            $table->enum('exercise_type', ['strength', 'cardio', 'bodyweight'])
                ->default('strength')
                ->after('is_custom');
        });

        // Métricas de cardio nos sets
        Schema::table('workout_sets', function (Blueprint $table) {
            $table->unsignedInteger('duration_seconds')->nullable()->after('reps');
            $table->float('distance_meters')->nullable()->after('duration_seconds');
        });

        Schema::table('routine_sets', function (Blueprint $table) {
            $table->unsignedInteger('duration_seconds')->nullable()->after('reps');
            $table->float('distance_meters')->nullable()->after('duration_seconds');
        });

        // Personal records de cardio
        Schema::table('personal_records', function (Blueprint $table) {
            $table->float('max_distance')->nullable()->after('estimated_1rm');
            $table->unsignedInteger('max_duration')->nullable()->after('max_distance');
            $table->unsignedInteger('best_pace')->nullable()->after('max_duration'); // segundos por km
        });
    }

    public function down(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->dropColumn('exercise_type');
        });
        Schema::table('workout_sets', function (Blueprint $table) {
            $table->dropColumn(['duration_seconds', 'distance_meters']);
        });
        Schema::table('routine_sets', function (Blueprint $table) {
            $table->dropColumn(['duration_seconds', 'distance_meters']);
        });
        Schema::table('personal_records', function (Blueprint $table) {
            $table->dropColumn(['max_distance', 'max_duration', 'best_pace']);
        });
    }
};
