<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // workouts
        Schema::table('workouts', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('status');
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'status', 'started_at']);
        });

        // workout_exercises
        Schema::table('workout_exercises', function (Blueprint $table) {
            $table->index('workout_id');
            $table->index('exercise_id');
        });

        // workout_sets
        Schema::table('workout_sets', function (Blueprint $table) {
            $table->index('workout_exercise_id');
        });

        // personal_records
        Schema::table('personal_records', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('exercise_id');
            $table->index('updated_at');
        });

        // posts
        Schema::table('posts', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('created_at');
        });

        // follows
        Schema::table('follows', function (Blueprint $table) {
            $table->index('follower_id');
            $table->index('following_id');
            $table->index('status');
            $table->index(['follower_id', 'status']);
            $table->index(['following_id', 'status']);
        });

        // post_comments
        Schema::table('post_comments', function (Blueprint $table) {
            $table->index('post_id');
            $table->index('user_id');
        });

        // post_likes
        Schema::table('post_likes', function (Blueprint $table) {
            $table->index('post_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('workouts', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['user_id', 'status', 'started_at']);
        });

        Schema::table('workout_exercises', function (Blueprint $table) {
            $table->dropIndex(['workout_id']);
            $table->dropIndex(['exercise_id']);
        });

        Schema::table('workout_sets', function (Blueprint $table) {
            $table->dropIndex(['workout_exercise_id']);
        });

        Schema::table('personal_records', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['exercise_id']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('follows', function (Blueprint $table) {
            $table->dropIndex(['follower_id']);
            $table->dropIndex(['following_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['follower_id', 'status']);
            $table->dropIndex(['following_id', 'status']);
        });

        Schema::table('post_comments', function (Blueprint $table) {
            $table->dropIndex(['post_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('post_likes', function (Blueprint $table) {
            $table->dropIndex(['post_id']);
            $table->dropIndex(['user_id']);
        });
    }
};
