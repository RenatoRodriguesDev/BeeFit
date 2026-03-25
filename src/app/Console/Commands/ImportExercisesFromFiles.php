<?php

namespace App\Console\Commands;

use App\Models\EquipmentTranslation;
use App\Models\Exercise;
use App\Models\ExerciseTranslation;
use App\Models\MuscleTranslation;
use Illuminate\Console\Command;

class ImportExercisesFromFiles extends Command
{
    protected $signature = 'app:import-exercises-from-files';
    protected $description = 'Import exercises from local files';

    public function handle()
    {
        $path = public_path('videos/exercises');
        $files = scandir($path);

        $imagePath = public_path('images/exercises');
        $images = scandir($imagePath);

        foreach ($files as $file) {
            if (!str_ends_with($file, '.mp4'))
                continue;

            try {
                $this->processFile($file);
            } catch (\Exception $e) {
                $this->error("Erro em $file: " . $e->getMessage());
            }
        }

        foreach ($images as $image) {

            if (!str_ends_with($image, '.jpg')) {
                continue;
            }

            // converter nome da imagem para nome base
            $baseName = str_replace('_thumbnail@3x.jpg', '', $image);

            $videoFile = $baseName . '.mp4';

            // 🔥 se já existe vídeo → já foi importado
            if (file_exists($path . '/' . $videoFile)) {
                continue;
            }

            try {
                $this->processImage($image);
            } catch (\Exception $e) {
                $this->error("Erro em $image: " . $e->getMessage());
            }
        }

        $this->info('Importação concluída 🚀');
    }

    private function processImage($image)
    {
        $filename = str_replace('_thumbnail@3x.jpg', '', $image);

        if (!str_contains($filename, '_')) {
            $left = $filename;
            $muscleRaw = 'other';
        } else {
            $parts = explode('_', $filename);
            $left = $parts[0];
            $muscleRaw = $parts[1] ?? 'other';
        }

        $parts = explode('-', $left);
        array_shift($parts);

        $equipmentRaw = $this->detectEquipment($parts);
        $exerciseName = $this->cleanName($parts);

        $muscleName = $this->mapMuscle($muscleRaw);
        $equipmentName = $this->mapEquipment($equipmentRaw);

        $this->storeExerciseImageOnly(
            $exerciseName,
            $muscleName,
            $equipmentName,
            $image
        );
    }

    private function processFile($file)
    {
        $filename = str_replace('.mp4', '', $file);

        // dividir por músculo
        if (!str_contains($filename, '_')) {
            $left = $filename;
            $muscleRaw = 'other';
        } else {
            $parts = explode('_', $filename);
            $left = $parts[0];
            $muscleRaw = $parts[1] ?? 'other';
        }

        // separar nome
        $parts = explode('-', $left);
        array_shift($parts); // remove ID

        // 🔥 DETECTAR EQUIPMENT CORRETAMENTE
        $equipmentRaw = $this->detectEquipment($parts);

        // nome exercício
        $exerciseName = $this->cleanName($parts);

        // mappings
        $muscleName = $this->mapMuscle($muscleRaw);
        $equipmentName = $this->mapEquipment($equipmentRaw);

        $this->storeExercise($exerciseName, $muscleName, $equipmentName, $file);
    }

    private function detectEquipment($parts)
    {
        foreach ($parts as $part) {
            $p = strtolower($part);

            if (
                in_array($p, [
                    'barbell',
                    'dumbbell',
                    'kettlebell',
                    'cable',
                    'lever',
                    'smith',
                    'band',
                    'machine',
                    'assisted'
                ])
            ) {
                return $p;
            }
        }

        return 'none';
    }

    private function cleanName($parts)
    {
        $filtered = array_filter($parts, function ($part) {
            return !preg_match('/^\d+$/', $part)
                && !in_array(strtolower($part), ['m', 'fix', 'small']);
        });

        $name = implode(' ', $filtered);

        // remover coisas entre ()
        $name = preg_replace('/\([^)]*\)/', '', $name);

        return ucfirst(trim($name));
    }

    private function mapMuscle($muscle)
    {
        $muscle = strtolower(str_replace(['-', '_'], ' ', $muscle));

        return match (true) {
            str_contains($muscle, 'chest') => 'Chest',
            str_contains($muscle, 'waist') => 'Abdominals',

            str_contains($muscle, 'shoulder') => 'Shoulders',

            str_contains($muscle, 'upper arms') => 'Triceps',
            str_contains($muscle, 'biceps') => 'Biceps',
            str_contains($muscle, 'triceps') => 'Triceps',

            str_contains($muscle, 'forearm') => 'Forearms',

            str_contains($muscle, 'back') => 'Upper Back',
            str_contains($muscle, 'lats') => 'Lats',
            str_contains($muscle, 'traps') => 'Traps',

            str_contains($muscle, 'hips') => 'Glutes',

            str_contains($muscle, 'thighs') => 'Quadriceps',
            str_contains($muscle, 'hamstrings') => 'Hamstrings',

            str_contains($muscle, 'calves') => 'Calves',

            str_contains($muscle, 'cardio'),
            str_contains($muscle, 'plyometrics') => 'Cardio',

            default => 'Other',
        };
    }

    private function mapEquipment($equipment)
    {
        $equipment = strtolower($equipment);

        return match (true) {
            str_contains($equipment, 'barbell') => 'Barbell',
            str_contains($equipment, 'dumbbell') => 'Dumbbell',
            str_contains($equipment, 'kettlebell') => 'Kettlebell',
            str_contains($equipment, 'band') => 'Resistance Band',

            str_contains($equipment, 'cable'),
            str_contains($equipment, 'lever'),
            str_contains($equipment, 'smith'),
            str_contains($equipment, 'machine'),
            str_contains($equipment, 'assisted') => 'Machine',

            default => 'None',
        };
    }

    private function storeExercise($name, $muscleName, $equipmentName, $file)
    {
        $muscle = MuscleTranslation::whereRaw('LOWER(name) = ?', [strtolower($muscleName)])
            ->where('locale', 'en')
            ->first()?->muscle;

        $equipment = EquipmentTranslation::whereRaw('LOWER(name) = ?', [strtolower($equipmentName)])
            ->where('locale', 'en')
            ->first()?->equipment;

        if (!$muscle) {
            $this->warn("❌ Muscle não encontrado: $muscleName em $file");
            return;
        }

        if (!$equipment) {
            $this->warn("❌ Equipment não encontrado: $equipmentName em $file");
            return;
        }

        $videoPath = "videos/exercises/$file";
        $imagePath = "images/exercises/" . str_replace('.mp4', '_thumbnail@3x.jpg', $file);

        if (Exercise::where('video_path', $videoPath)->exists()) {
            return;
        }

        $exercise = Exercise::create([
            'equipment_id' => $equipment->id,
            'primary_muscle_id' => $muscle->id,
            'thumbnail_path' => $imagePath,
            'video_path' => $videoPath,
        ]);

        ExerciseTranslation::insert([
            ['exercise_id' => $exercise->id, 'locale' => 'en', 'name' => $name],
            ['exercise_id' => $exercise->id, 'locale' => 'pt', 'name' => $name],
            ['exercise_id' => $exercise->id, 'locale' => 'es', 'name' => $name],
        ]);

        $this->info("✅ Criado: $name");
    }

    private function storeExerciseImageOnly($name, $muscleName, $equipmentName, $image)
    {
        $muscle = MuscleTranslation::whereRaw('LOWER(name) = ?', [strtolower($muscleName)])
            ->where('locale', 'en')
            ->first()?->muscle;

        $equipment = EquipmentTranslation::whereRaw('LOWER(name) = ?', [strtolower($equipmentName)])
            ->where('locale', 'en')
            ->first()?->equipment;

        if (!$muscle || !$equipment) {
            return;
        }

        $imagePath = "images/exercises/$image";

        // evitar duplicados (IMPORTANTE)
        if (Exercise::where('thumbnail_path', $imagePath)->exists()) {
            return;
        }

        $exercise = Exercise::create([
            'equipment_id' => $equipment->id,
            'primary_muscle_id' => $muscle->id,
            'thumbnail_path' => $imagePath,
            'video_path' => null, // 👈 sem vídeo
        ]);

        ExerciseTranslation::insert([
            ['exercise_id' => $exercise->id, 'locale' => 'en', 'name' => $name],
            ['exercise_id' => $exercise->id, 'locale' => 'pt', 'name' => $name],
            ['exercise_id' => $exercise->id, 'locale' => 'es', 'name' => $name],
        ]);

        $this->info("🖼️ Criado (sem vídeo): $name");
    }
}