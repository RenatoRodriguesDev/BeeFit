<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('personal_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workout_id')->constrained()->cascadeOnDelete();

            // Peso máximo levantado
            $table->float('max_weight')->nullable();
            $table->integer('reps_at_max_weight')->nullable();

            // Maior volume num único set (peso × reps)
            $table->float('max_volume_set')->nullable();

            // Maior número de reps (independente do peso)
            $table->integer('max_reps')->nullable();
            $table->float('weight_at_max_reps')->nullable();

            // Melhor 1RM estimado (Epley: peso × (1 + reps/30))
            $table->float('estimated_1rm')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'exercise_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_records');
    }
};