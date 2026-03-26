<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('follows', function (Blueprint $table) {
            $table->enum('status', ['pending', 'accepted'])->default('accepted')->after('following_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_private')->default(false)->after('theme_mode');
        });
    }

    public function down(): void
    {
        Schema::table('follows', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_private');
        });
    }
};
