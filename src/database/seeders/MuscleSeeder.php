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
                'en' => 'Abdominals',
                'es' => 'Abdominales',
                'pt' => 'Abdominais',
            ],
            [
                'en' => 'Shoulders',
                'es' => 'Hombros',
                'pt' => 'Ombros',
            ],
            [
                'en' => 'Biceps',
                'es' => 'Bíceps',
                'pt' => 'Bíceps',
            ],
            [
                'en' => 'Triceps',
                'es' => 'Tríceps',
                'pt' => 'Tríceps',
            ],
            [
                'en' => 'Forearms',
                'es' => 'Antebrazos',
                'pt' => 'Antebraços',
            ],
            [
                'en' => 'Quadriceps',
                'es' => 'Cuádriceps',
                'pt' => 'Quadríceps',
            ],
            [
                'en' => 'Hamstrings',
                'es' => 'Isquiotibiales',
                'pt' => 'Isquiotibiais',
            ],
            [
                'en' => 'Calves',
                'es' => 'Pantorrillas',
                'pt' => 'Gémeos',
            ],
            [
                'en' => 'Glutes',
                'es' => 'Glúteos',
                'pt' => 'Glúteos',
            ],
            [
                'en' => 'Abductors',
                'es' => 'Abductores',
                'pt' => 'Abdutores',
            ],
            [
                'en' => 'Adductors',
                'es' => 'Aductores',
                'pt' => 'Adutores',
            ],
            [
                'en' => 'Lats',
                'es' => 'Dorsales',
                'pt' => 'Dorsais',
            ],
            [
                'en' => 'Upper Back',
                'es' => 'Espalda superior',
                'pt' => 'Costas superiores',
            ],
            [
                'en' => 'Traps',
                'es' => 'Trapecios',
                'pt' => 'Trapézios',
            ],
            [
                'en' => 'Lower Back',
                'es' => 'Espalda baja',
                'pt' => 'Lombar',
            ],
            [
                'en' => 'Cardio',
                'es' => 'Cardio',
                'pt' => 'Cardio',
            ],
            [
                'en' => 'Neck',
                'es' => 'Cuello',
                'pt' => 'Pescoço',
            ],
            [
                'en' => 'Full Body',
                'es' => 'Cuerpo completo',
                'pt' => 'Corpo inteiro',
            ],
            [
                'en' => 'Other',
                'es' => 'Otro',
                'pt' => 'Outro',
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