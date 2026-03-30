<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('routines', function (Blueprint $table) {
            $table->unsignedInteger('order')->default(0)->after('is_active');
        });

        $i = 1;
        DB::table('routines')->orderBy('created_at')->each(function ($routine) use (&$i) {
            DB::table('routines')->where('id', $routine->id)->update(['order' => $i++]);
        });
    }

    public function down(): void
    {
        Schema::table('routines', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
