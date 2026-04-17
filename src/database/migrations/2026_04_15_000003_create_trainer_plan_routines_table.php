<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainer_plan_routines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_plan_id')->constrained('trainer_plans')->cascadeOnDelete();
            $table->foreignId('routine_id')->constrained('routines')->cascadeOnDelete();
            $table->unsignedSmallInteger('week_number')->default(1);
            $table->string('day_label')->nullable(); // e.g. "Seg", "Ter" or "Dia 1"
            $table->unsignedSmallInteger('order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainer_plan_routines');
    }
};
