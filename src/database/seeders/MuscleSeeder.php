<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Muscle;
use App\Models\MuscleTranslation;

class MuscleSeeder extends Seeder
{
    public function run(): void
    {
        $muscles = [
            [
                'en' => 'Chest',
                'es' => 'Pecho',
                'pt' => 'Peito',
            ],
            [
                'en' => 'Back',
                'es' => 'Espalda',
                'pt' => 'Costas',
            ],
            [
                'en' => 'Legs',
                'es' => 'Piernas',
                'pt' => 'Pernas',
            ],
            [
                'en' => 'Shoulders',
                'es' => 'Hombros',
                'pt' => 'Ombros',
            ],
            [
                'en' => 'Arms',
                'es' => 'Brazos',
                'pt' => 'Braços',
            ],
        ];

        foreach ($muscles as $data) {

            $muscle = Muscle::create();

            foreach ($data as $locale => $name) {
                MuscleTranslation::create([
                    'muscle_id' => $muscle->id,
                    'locale' => $locale,
                    'name' => $name,
                ]);
            }
        }
    }
}