<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Role do utilizador
            $table->enum('role', ['user', 'premium', 'trainer', 'admin'])
                  ->default('user')
                  ->after('locale');

            // Plano de subscrição
            $table->enum('plan', ['free', 'premium', 'trainer'])
                  ->default('free')
                  ->after('role');

            // Stripe
            $table->string('stripe_customer_id')->nullable()->after('plan');
            $table->string('stripe_subscription_id')->nullable()->after('stripe_customer_id');
            $table->enum('subscription_status', [
                'active', 'trialing', 'past_due', 'canceled', 'incomplete', 'none'
            ])->default('none')->after('stripe_subscription_id');
            $table->timestamp('subscription_ends_at')->nullable()->after('subscription_status');

            // Perfil
            $table->string('avatar_path')->nullable()->after('subscription_ends_at');
            $table->date('birthdate')->nullable()->after('avatar_path');
            $table->unsignedSmallInteger('height_cm')->nullable()->after('birthdate');
            $table->decimal('weight_kg', 5, 2)->nullable()->after('height_cm');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('weight_kg');

            // Preferências de tema (premium)
            $table->string('theme_color', 7)->nullable()->after('gender'); // hex ex: #FFD91A
            $table->enum('theme_mode', ['dark', 'light'])->default('dark')->after('theme_color');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'plan',
                'stripe_customer_id', 'stripe_subscription_id',
                'subscription_status', 'subscription_ends_at',
                'avatar_path', 'birthdate', 'height_cm', 'weight_kg', 'gender',
                'theme_color', 'theme_mode',
            ]);
        });
    }
};