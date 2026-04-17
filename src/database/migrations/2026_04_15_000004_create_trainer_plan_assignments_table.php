<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainer_plan_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_plan_id')->constrained('trainer_plans')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('trainer_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['trainer_plan_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainer_plan_assignments');
    }
};
