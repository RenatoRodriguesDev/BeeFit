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
                'en' => 'Kettlebell',
                'es' => 'Pesa rusa',
                'pt' => 'Kettlebell',
            ],
            [
                'en' => 'Plate',
                'es' => 'Disco',
                'pt' => 'Disco',
            ],
            [
                'en' => 'Resistance Band',
                'es' => 'Banda de resistencia',
                'pt' => 'Banda de resistência',
            ],
            [
                'en' => 'Suspension',
                'es' => 'Suspensión',
                'pt' => 'Suspensão',
            ],
            [
                'en' => 'Other',
                'es' => 'Otro',
                'pt' => 'Outro',
            ],
            [
                'en' => 'None',
                'es' => 'Ninguno',
                'pt' => 'Nenhum',
            ],
            [
                'en' => 'Machine',
                'es' => 'Máquina',
                'pt' => 'Máquina',
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