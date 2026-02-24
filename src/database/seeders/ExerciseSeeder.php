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

        $exercises = [
            ['Bench Press (Barbell)', 'Press de banca con barra', 'Supino com barra', $barbell, $chest],
            ['Bench Press (Dumbbell)', 'Press de banca con mancuernas', 'Supino com halteres', $dumbbell, $chest],
            ['Incline Bench Press', 'Press inclinado', 'Supino inclinado', $barbell, $chest],
            ['Cable Fly', 'Aperturas en polea', 'Crossover na polia', $cable, $chest],
            ['Push Up', 'Flexiones', 'Flexões', $bodyweight, $chest],
            ['Deadlift (Barbell)', 'Peso muerto con barra', 'Peso morto com barra', $barbell, $back],
            ['Bent Over Row', 'Remo inclinado', 'Remada curvada', $barbell, $back],
            ['Lat Pulldown', 'Jalón al pecho', 'Puxada na frente', $cable, $back],
            ['Seated Row', 'Remo sentado', 'Remada sentada', $cable, $back],
            ['Squat (Barbell)', 'Sentadilla con barra', 'Agachamento com barra', $barbell, $legs],
            ['Leg Press', 'Prensa de piernas', 'Leg press', $machine, $legs],
            ['Romanian Deadlift', 'Peso muerto rumano', 'Peso morto romeno', $barbell, $legs],
            ['Leg Extension', 'Extensión de piernas', 'Extensão de pernas', $machine, $legs],
            ['Leg Curl', 'Curl femoral', 'Flexão de pernas', $machine, $legs],
            ['Overhead Press', 'Press militar', 'Press militar', $barbell, $shoulders],
            ['Lateral Raise', 'Elevaciones laterales', 'Elevação lateral', $dumbbell, $shoulders],
            ['Face Pull', 'Face pull', 'Face pull', $cable, $shoulders],
            ['Bicep Curl', 'Curl de bíceps', 'Curl de bíceps', $dumbbell, $arms],
            ['Tricep Pushdown', 'Extensión de tríceps en polea', 'Extensão de tríceps na polia', $cable, $arms],
            ['Hammer Curl', 'Curl martillo', 'Curl martelo', $dumbbell, $arms],
        ];

        foreach ($exercises as [$en, $es, $pt, $equipment, $muscle]) {

            $exercise = Exercise::create([
                'equipment_id' => $equipment->id,
                'primary_muscle_id' => $muscle->id,
            ]);

            ExerciseTranslation::insert([
                ['exercise_id' => $exercise->id, 'locale' => 'en', 'name' => $en],
                ['exercise_id' => $exercise->id, 'locale' => 'es', 'name' => $es],
                ['exercise_id' => $exercise->id, 'locale' => 'pt', 'name' => $pt],
            ]);
        }
    }
}