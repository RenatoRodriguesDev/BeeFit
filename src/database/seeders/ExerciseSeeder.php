<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exercise;
use App\Models\ExerciseTranslation;
use App\Models\MuscleTranslation;
use App\Models\EquipmentTranslation;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        $chest = MuscleTranslation::where('name', 'Chest')->where('locale', 'en')->first()->muscle;
        $abdominals = MuscleTranslation::where('name', 'Abdominals')->where('locale', 'en')->first()->muscle;
        $shoulders = MuscleTranslation::where('name', 'Shoulders')->where('locale', 'en')->first()->muscle;
        $biceps = MuscleTranslation::where('name', 'Biceps')->where('locale', 'en')->first()->muscle;
        $triceps = MuscleTranslation::where('name', 'Triceps')->where('locale', 'en')->first()->muscle;
        $forearms = MuscleTranslation::where('name', 'Forearms')->where('locale', 'en')->first()->muscle;
        $quadriceps = MuscleTranslation::where('name', 'Quadriceps')->where('locale', 'en')->first()->muscle;
        $hamstrings = MuscleTranslation::where('name', 'Hamstrings')->where('locale', 'en')->first()->muscle;
        $calves = MuscleTranslation::where('name', 'Calves')->where('locale', 'en')->first()->muscle;
        $glutes = MuscleTranslation::where('name', 'Glutes')->where('locale', 'en')->first()->muscle;
        $abductors = MuscleTranslation::where('name', 'Abductors')->where('locale', 'en')->first()->muscle;
        $adductors = MuscleTranslation::where('name', 'Adductors')->where('locale', 'en')->first()->muscle;
        $lats = MuscleTranslation::where('name', 'Lats')->where('locale', 'en')->first()->muscle;
        $upperBack = MuscleTranslation::where('name', 'Upper Back')->where('locale', 'en')->first()->muscle;
        $traps = MuscleTranslation::where('name', 'Traps')->where('locale', 'en')->first()->muscle;
        $lowerBack = MuscleTranslation::where('name', 'Lower Back')->where('locale', 'en')->first()->muscle;
        $cardio = MuscleTranslation::where('name', 'Cardio')->where('locale', 'en')->first()->muscle;
        $neck = MuscleTranslation::where('name', 'Neck')->where('locale', 'en')->first()->muscle;
        $fullBody = MuscleTranslation::where('name', 'Full Body')->where('locale', 'en')->first()->muscle;
        $otherMuscle = MuscleTranslation::where('name', 'Other')->where('locale', 'en')->first()->muscle;

        // Se quiseres manter estes agrupados:
        $back = MuscleTranslation::where('name', 'Upper Back')->where('locale', 'en')->first()->muscle;
        $legs = MuscleTranslation::where('name', 'Quadriceps')->where('locale', 'en')->first()->muscle;
        $arms = MuscleTranslation::where('name', 'Biceps')->where('locale', 'en')->first()->muscle;

        $barbell = EquipmentTranslation::where('name', 'Barbell')->where('locale', 'en')->first()->equipment;
        $dumbbell = EquipmentTranslation::where('name', 'Dumbbell')->where('locale', 'en')->first()->equipment;
        $kettlebell = EquipmentTranslation::where('name', 'Kettlebell')->where('locale', 'en')->first()->equipment;
        $plate = EquipmentTranslation::where('name', 'Plate')->where('locale', 'en')->first()->equipment;
        $resistanceBand = EquipmentTranslation::where('name', 'Resistance Band')->where('locale', 'en')->first()->equipment;
        $suspension = EquipmentTranslation::where('name', 'Suspension')->where('locale', 'en')->first()->equipment;
        $machine = EquipmentTranslation::where('name', 'Machine')->where('locale', 'en')->first()->equipment;
        $otherEquipment = EquipmentTranslation::where('name', 'Other')->where('locale', 'en')->first()->equipment;
        $noneEquipment = EquipmentTranslation::where('name', 'None')->where('locale', 'en')->first()->equipment;
        // Array de exercícios
        $exercises = [
            [
                'en' => 'Sit-up',
                'es' => 'Abdominal',
                'pt' => 'Abdominal',
                'equipment' => $noneEquipment,
                'muscle' => $legs,
                'image' => 'images/exercises/00011201-3-4-Sit-up_Waist_thumbnail@3x.jpg',
                'video' => 'videos/exercises/00011201-3-4-Sit-up_Waist.mp4',
            ],
            [
                'en' => 'Side Bend',
                'es' => 'Flexión lateral',
                'pt' => 'Flexão lateral',
                'equipment' => $noneEquipment,
                'muscle' => $legs,
                'image' => 'images/exercises/00021201-45-Side-Bend_Waist_thumbnail@3x.jpg',
                'video' => 'videos/exercises/00021201-45-Side-Bend_Waist.mp4',
            ],
            [
                'en' => 'Air Bike',
                'es' => 'Bicicleta de aire',
                'pt' => 'Bicicleta no ar',
                'equipment' => $otherEquipment,
                'muscle' => $legs,
                'image' => 'images/exercises/00031201-air-bike-m_waist_thumbnail@3x.jpg',
                'video' => 'videos/exercises/00031201-air-bike-m_waist.mp4',
            ],
            [
                'en' => 'Alternate Heel Touchers',
                'es' => 'Toques alternos de talón',
                'pt' => 'Toques alternados de calcanhar',
                'equipment' => $otherEquipment,
                'muscle' => $legs,
                'image' => 'images/exercises/00061101-Alternate-Heel-Touchers_waist-FIX_small_thumbnail@3x.jpg',
                'video' => 'videos/exercises/00061101-Alternate-Heel-Touchers_waist-FIX_small.mp4',
            ],
        ];

        foreach ($exercises as $ex) {
            $exercise = Exercise::create([
                'equipment_id' => $ex['equipment']->id,
                'primary_muscle_id' => $ex['muscle']->id,
                'thumbnail_path' => $ex['image'],
                'video_path' => $ex['video'],
            ]);

            ExerciseTranslation::insert([
                ['exercise_id' => $exercise->id, 'locale' => 'en', 'name' => $ex['en']],
                ['exercise_id' => $exercise->id, 'locale' => 'es', 'name' => $ex['es']],
                ['exercise_id' => $exercise->id, 'locale' => 'pt', 'name' => $ex['pt']],
            ]);
        }
    }
}