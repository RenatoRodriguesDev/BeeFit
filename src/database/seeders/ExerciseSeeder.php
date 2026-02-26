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
        $back = MuscleTranslation::where('name', 'Back')->where('locale', 'en')->first()->muscle;
        $legs = MuscleTranslation::where('name', 'Legs')->where('locale', 'en')->first()->muscle;
        $shoulders = MuscleTranslation::where('name', 'Shoulders')->where('locale', 'en')->first()->muscle;
        $arms = MuscleTranslation::where('name', 'Arms')->where('locale', 'en')->first()->muscle;

        $barbell = EquipmentTranslation::where('name', 'Barbell')->where('locale', 'en')->first()->equipment;
        $dumbbell = EquipmentTranslation::where('name', 'Dumbbell')->where('locale', 'en')->first()->equipment;
        $machine = EquipmentTranslation::where('name', 'Machine')->where('locale', 'en')->first()->equipment;
        $cable = EquipmentTranslation::where('name', 'Cable')->where('locale', 'en')->first()->equipment;
        $bodyweight = EquipmentTranslation::where('name', 'Bodyweight')->where('locale', 'en')->first()->equipment;
        // Array de exercícios
        $exercises = [
            [
                'en' => 'Sit-up',
                'es' => 'Abdominal',
                'pt' => 'Abdominal',
                'equipment' => $bodyweight,
                'muscle' => $legs,
                'image' => 'images/exercises/00011201-3-4-Sit-up_Waist_thumbnail@3x.jpg',
                'video' => 'videos/exercises/00011201-3-4-Sit-up_Waist.mp4',
            ],
            [
                'en' => 'Side Bend',
                'es' => 'Flexión lateral',
                'pt' => 'Flexão lateral',
                'equipment' => $dumbbell,
                'muscle' => $legs,
                'image' => 'images/exercises/00021201-45-Side-Bend_Waist_thumbnail@3x.jpg',
                'video' => 'videos/exercises/00021201-45-Side-Bend_Waist.mp4',
            ],
            [
                'en' => 'Air Bike',
                'es' => 'Bicicleta de aire',
                'pt' => 'Bicicleta no ar',
                'equipment' => $bodyweight,
                'muscle' => $legs,
                'image' => 'images/exercises/00031201-air-bike-m_waist_thumbnail@3x.jpg',
                'video' => 'videos/exercises/00031201-air-bike-m_waist.mp4',
            ],
            [
                'en' => 'Alternate Heel Touchers',
                'es' => 'Toques alternos de talón',
                'pt' => 'Toques alternados de calcanhar',
                'equipment' => $bodyweight,
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