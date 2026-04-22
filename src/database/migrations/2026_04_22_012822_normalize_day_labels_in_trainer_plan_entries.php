<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $map = [
        'lunes'      => 'monday',    'martes'    => 'tuesday',
        'miércoles'  => 'wednesday', 'miercoles' => 'wednesday',
        'jueves'     => 'thursday',  'viernes'   => 'friday',
        'sábado'     => 'saturday',  'sabado'    => 'saturday',
        'domingo'    => 'sunday',
        'segunda'    => 'monday',    'terca'     => 'tuesday',
        'terça'      => 'tuesday',   'quarta'    => 'wednesday',
        'quinta'     => 'thursday',  'sexta'     => 'friday',
    ];

    public function up(): void
    {
        $entries = DB::table('trainer_plan_routines')
            ->whereNotNull('day_label')
            ->get(['id', 'day_label']);

        foreach ($entries as $entry) {
            $normalized = $this->map[strtolower(trim($entry->day_label))] ?? null;
            if ($normalized && $normalized !== $entry->day_label) {
                DB::table('trainer_plan_routines')
                    ->where('id', $entry->id)
                    ->update(['day_label' => $normalized]);
            }
        }
    }

    public function down(): void {}
};
