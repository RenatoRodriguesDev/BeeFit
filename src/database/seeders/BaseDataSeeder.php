<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EquipmentTranslation;
use App\Models\Equipment;

class BaseDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barbell = Equipment::create();

        EquipmentTranslation::insert([
            ['equipment_id' => $barbell->id, 'locale' => 'en', 'name' => 'Barbell'],
            ['equipment_id' => $barbell->id, 'locale' => 'es', 'name' => 'Barra'],
            ['equipment_id' => $barbell->id, 'locale' => 'pt', 'name' => 'Barra'],
        ]);
    }
}
