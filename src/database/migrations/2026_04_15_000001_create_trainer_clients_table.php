<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainer_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['invited', 'active', 'rejected', 'suspended'])->default('invited');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->unique(['trainer_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainer_clients');
    }
};
