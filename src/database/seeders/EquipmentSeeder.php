<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Equipment;
use App\Models\EquipmentTranslation;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $equipments = [
            [
                'en' => 'Barbell',
                'es' => 'Barra',
                'pt' => 'Barra',
            ],
            [
                'en' => 'Dumbbell',
                'es' => 'Mancuerna',
                'pt' => 'Halter',
            ],
            [
                'en' => 'Machine',
                'es' => 'Máquina',
                'pt' => 'Máquina',
            ],
            [
                'en' => 'Cable',
                'es' => 'Polea',
                'pt' => 'Polia',
            ],
            [
                'en' => 'Bodyweight',
                'es' => 'Peso corporal',
                'pt' => 'Peso corporal',
            ],
        ];

        foreach ($equipments as $data) {

            $equipment = Equipment::create();

            foreach ($data as $locale => $name) {
                EquipmentTranslation::create([
                    'equipment_id' => $equipment->id,
                    'locale' => $locale,
                    'name' => $name,
                ]);
            }
        }
    }
}