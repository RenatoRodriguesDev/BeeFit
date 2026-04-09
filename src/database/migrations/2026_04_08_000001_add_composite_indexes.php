<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table("posts", function (Blueprint $table) {
            $table->index(["user_id", "created_at"]);
        });

        Schema::table("post_comments", function (Blueprint $table) {
            $table->index(["post_id", "created_at"]);
        });
    }

    public function down(): void
    {
        Schema::table("posts", function (Blueprint $table) {
            $table->dropIndex(["user_id", "created_at"]);
        });

        Schema::table("post_comments", function (Blueprint $table) {
            $table->dropIndex(["post_id", "created_at"]);
        });
    }
};
