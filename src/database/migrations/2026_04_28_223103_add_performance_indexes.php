<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function indexExists(string $table, string $index): bool
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            return DB::select(
                "SELECT 1 FROM pg_indexes WHERE tablename = ? AND indexname = ?",
                [$table, $index]
            ) !== [];
        }

        // MySQL / SQLite
        try {
            $indexes = collect(DB::select("SHOW INDEX FROM `{$table}`"))
                ->pluck('Key_name')->unique()->toArray();
            return in_array($index, $indexes);
        } catch (\Throwable) {
            return false;
        }
    }

    public function up(): void
    {
        Schema::table('follows', function (Blueprint $table) {
            if (!$this->indexExists('follows', 'follows_follower_id_status_index')) {
                $table->index(['follower_id', 'status']);
            }
            if (!$this->indexExists('follows', 'follows_following_id_status_index')) {
                $table->index(['following_id', 'status']);
            }
        });

        Schema::table('notifications', function (Blueprint $table) {
            if (!$this->indexExists('notifications', 'notifications_notifiable_id_notifiable_type_read_at_index')) {
                $table->index(['notifiable_id', 'notifiable_type', 'read_at']);
            }
        });

        Schema::table('posts', function (Blueprint $table) {
            if (!$this->indexExists('posts', 'posts_user_id_created_at_index')) {
                $table->index(['user_id', 'created_at']);
            }
        });

        Schema::table('workouts', function (Blueprint $table) {
            if (!$this->indexExists('workouts', 'workouts_user_id_status_index')) {
                $table->index(['user_id', 'status']);
            }
        });
    }

    public function down(): void {}
};
