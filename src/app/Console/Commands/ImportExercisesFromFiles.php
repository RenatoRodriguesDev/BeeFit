<?php

namespace App\Console\Commands;

use App\Models\Equipment;
use App\Models\EquipmentTranslation;
use App\Models\Exercise;
use App\Models\ExerciseTranslation;
use App\Models\Muscle;
use App\Models\MuscleTranslation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportExercisesFromFiles extends Command
{
    protected $signature = 'app:import-exercises-from-files
                            {--fresh : Delete all non-custom exercises before importing}
                            {--skip-translate : Skip translation, use English name for all locales}';

    protected $description = 'Import exercises from local video/image files in public/videos/exercises and public/images/exercises';

    private array $muscleMap    = [];
    private array $equipmentMap = [];

    // Maps filename suffix → [en, pt, es] names + local DB id (filled at runtime)
    private array $muscleDefinitions = [
        'chest'      => ['en' => 'Chest',        'pt' => 'Peitoral',         'es' => 'Pecho'],
        'waist'      => ['en' => 'Abdominals',   'pt' => 'Abdominais',       'es' => 'Abdominales'],
        'shoulders'  => ['en' => 'Shoulders',    'pt' => 'Ombros',           'es' => 'Hombros'],
        'upper arms' => ['en' => 'Triceps',      'pt' => 'Tríceps',          'es' => 'Tríceps'],
        'biceps'     => ['en' => 'Biceps',        'pt' => 'Bíceps',           'es' => 'Bíceps'],
        'triceps'    => ['en' => 'Triceps',      'pt' => 'Tríceps',          'es' => 'Tríceps'],
        'forearms'   => ['en' => 'Forearms',     'pt' => 'Antebraços',       'es' => 'Antebrazos'],
        'forearm'    => ['en' => 'Forearms',     'pt' => 'Antebraços',       'es' => 'Antebrazos'],
        'back'       => ['en' => 'Back',         'pt' => 'Costas',           'es' => 'Espalda'],
        'lats'       => ['en' => 'Lats',         'pt' => 'Grande dorsal',    'es' => 'Dorsales'],
        'traps'      => ['en' => 'Traps',        'pt' => 'Trapézio',         'es' => 'Trapecio'],
        'hips'       => ['en' => 'Glutes',       'pt' => 'Glúteos',          'es' => 'Glúteos'],
        'glutes'     => ['en' => 'Glutes',       'pt' => 'Glúteos',          'es' => 'Glúteos'],
        'thighs'     => ['en' => 'Quadriceps',   'pt' => 'Quadríceps',       'es' => 'Cuádriceps'],
        'hamstrings' => ['en' => 'Hamstrings',   'pt' => 'Isquiotibiais',    'es' => 'Isquiotibiales'],
        'calves'     => ['en' => 'Calves',       'pt' => 'Gémeos',           'es' => 'Gemelos'],
        'neck'       => ['en' => 'Neck',         'pt' => 'Pescoço',          'es' => 'Cuello'],
        'cardio'     => ['en' => 'General',      'pt' => 'Geral',            'es' => 'General'],
        'plyometrics'=> ['en' => 'General',      'pt' => 'Geral',            'es' => 'General'],
        'other'      => ['en' => 'General',      'pt' => 'Geral',            'es' => 'General'],
    ];

    private array $equipmentDefinitions = [
        'barbell'    => ['en' => 'Barbell',         'pt' => 'Barra olímpica',     'es' => 'Barra olímpica'],
        'dumbbell'   => ['en' => 'Dumbbell',        'pt' => 'Haltere',            'es' => 'Mancuerna'],
        'kettlebell' => ['en' => 'Kettlebell',      'pt' => 'Kettlebell',         'es' => 'Kettlebell'],
        'cable'      => ['en' => 'Cable',           'pt' => 'Cabo / Polia',       'es' => 'Cable / Polea'],
        'lever'      => ['en' => 'Machine',         'pt' => 'Máquina',            'es' => 'Máquina'],
        'smith'      => ['en' => 'Machine',         'pt' => 'Máquina',            'es' => 'Máquina'],
        'machine'    => ['en' => 'Machine',         'pt' => 'Máquina',            'es' => 'Máquina'],
        'assisted'   => ['en' => 'Machine',         'pt' => 'Máquina',            'es' => 'Máquina'],
        'band'       => ['en' => 'Bands',           'pt' => 'Elásticos',          'es' => 'Bandas elásticas'],
        'none'       => ['en' => 'Body Weight',     'pt' => 'Peso corporal',      'es' => 'Peso corporal'],
    ];

    public function handle(): int
    {
        if ($this->option('fresh')) {
            $this->warn('Deleting all non-custom exercises...');
            // Delete in dependency order to satisfy foreign key constraints (works on both MySQL and PostgreSQL)
            ExerciseTranslation::whereHas('exercise', fn($q) => $q->where('is_custom', false))->delete();
            Exercise::where('is_custom', false)->delete();
            MuscleTranslation::query()->delete();
            Muscle::query()->delete();
            EquipmentTranslation::query()->delete();
            Equipment::query()->delete();
            $this->info('Cleared.');
        }

        $this->seedMuscles();
        $this->seedEquipment();

        $videoPath = public_path('videos/exercises');
        $imagePath = public_path('images/exercises');

        $videos = collect(scandir($videoPath))->filter(fn($f) => str_ends_with($f, '.mp4'));
        $images = collect(scandir($imagePath))->filter(fn($f) => str_ends_with($f, '.jpg'));

        $total   = $videos->count() + $images->count();
        $bar     = $this->output->createProgressBar($total);
        $bar->start();

        $imported = 0;

        // Import videos (+ matching thumbnail if exists)
        foreach ($videos as $file) {
            $baseName   = str_replace('.mp4', '', $file);
            $thumbName  = $baseName . '_thumbnail@3x.jpg';
            $thumbExists = $images->contains($thumbName);

            if ($this->importFromVideo($file, $thumbExists ? $thumbName : null)) {
                $imported++;
            }
            $bar->advance();
        }

        // Import images that have no matching video
        foreach ($images as $image) {
            $baseName  = str_replace('_thumbnail@3x.jpg', '', $image);
            $videoFile = $baseName . '.mp4';

            if ($videos->contains($videoFile)) {
                $bar->advance();
                continue; // already handled above
            }

            if ($this->importFromImage($image)) {
                $imported++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Done! {$imported} exercises imported.");

        return self::SUCCESS;
    }

    // ── Seed muscles ──────────────────────────────────────────────────────────

    private function seedMuscles(): void
    {
        $this->info('Seeding muscles...');

        // Deduplicate by EN name (multiple keys can map to same muscle, e.g. triceps/upper arms)
        $seen = [];
        foreach ($this->muscleDefinitions as $key => $names) {
            $enName = $names['en'];
            if (isset($seen[$enName])) {
                $this->muscleMap[$key] = $seen[$enName];
                continue;
            }

            $existing = MuscleTranslation::where('locale', 'en')->where('name', $enName)->first();
            if ($existing) {
                $id = $existing->muscle_id;
            } else {
                $muscle = Muscle::create([]);
                MuscleTranslation::insert([
                    ['muscle_id' => $muscle->id, 'locale' => 'en', 'name' => $names['en'], 'created_at' => now(), 'updated_at' => now()],
                    ['muscle_id' => $muscle->id, 'locale' => 'pt', 'name' => $names['pt'], 'created_at' => now(), 'updated_at' => now()],
                    ['muscle_id' => $muscle->id, 'locale' => 'es', 'name' => $names['es'], 'created_at' => now(), 'updated_at' => now()],
                ]);
                $id = $muscle->id;
            }

            $seen[$enName]     = $id;
            $this->muscleMap[$key] = $id;
        }

        $this->line('  ✓ ' . count($seen) . ' muscles ready');
    }

    // ── Seed equipment ────────────────────────────────────────────────────────

    private function seedEquipment(): void
    {
        $this->info('Seeding equipment...');

        $seen = [];
        foreach ($this->equipmentDefinitions as $key => $names) {
            $enName = $names['en'];
            if (isset($seen[$enName])) {
                $this->equipmentMap[$key] = $seen[$enName];
                continue;
            }

            $existing = EquipmentTranslation::where('locale', 'en')->where('name', $enName)->first();
            if ($existing) {
                $id = $existing->equipment_id;
            } else {
                $equipment = Equipment::create([]);
                EquipmentTranslation::insert([
                    ['equipment_id' => $equipment->id, 'locale' => 'en', 'name' => $names['en'], 'created_at' => now(), 'updated_at' => now()],
                    ['equipment_id' => $equipment->id, 'locale' => 'pt', 'name' => $names['pt'], 'created_at' => now(), 'updated_at' => now()],
                    ['equipment_id' => $equipment->id, 'locale' => 'es', 'name' => $names['es'], 'created_at' => now(), 'updated_at' => now()],
                ]);
                $id = $equipment->id;
            }

            $seen[$enName]        = $id;
            $this->equipmentMap[$key] = $id;
        }

        $this->line('  ✓ ' . count($seen) . ' equipment types ready');
    }

    // ── Parse filename ────────────────────────────────────────────────────────

    private function parseFilename(string $filename): array
    {
        // Remove extension markers like _thumbnail@3x, _thumbnail, etc.
        $filename = preg_replace('/_thumbnail(@\d+x)?/', '', $filename);

        // Split on first underscore to separate name + muscle
        if (str_contains($filename, '_')) {
            [$left, $muscleRaw] = explode('_', $filename, 2);
        } else {
            $left      = $filename;
            $muscleRaw = 'other';
        }

        // Remove numeric code prefix (e.g. 00251201-)
        $left = preg_replace('/^\d+-/', '', $left);

        // Split by hyphen to get parts
        $parts = explode('-', $left);

        $equipmentKey = $this->detectEquipmentKey($parts);
        $name         = $this->buildName($parts);
        $muscleKey    = $this->normalizeMuscleKey($muscleRaw);

        return [$name, $muscleKey, $equipmentKey];
    }

    private function detectEquipmentKey(array $parts): string
    {
        foreach ($parts as $part) {
            $p = strtolower($part);
            if (array_key_exists($p, $this->equipmentDefinitions)) {
                return $p;
            }
        }
        return 'none';
    }

    private function buildName(array $parts): string
    {
        $filtered = array_filter($parts, fn($p) =>
            ! preg_match('/^\d+$/', $p) &&
            ! in_array(strtolower($p), ['m', 'fix', 'small', 'fix', 'version'])
        );

        $name = implode(' ', $filtered);
        $name = preg_replace('/\([^)]*\)/', '', $name); // remove parentheses
        $name = preg_replace('/\s+/', ' ', $name);

        return ucfirst(trim($name));
    }

    private function normalizeMuscleKey(string $raw): string
    {
        $raw = strtolower(str_replace(['-', '_'], ' ', $raw));
        $raw = trim(preg_replace('/\s+/', ' ', $raw));

        foreach (array_keys($this->muscleDefinitions) as $key) {
            if (str_contains($raw, $key)) {
                return $key;
            }
        }

        return 'other';
    }

    // ── Import from video ─────────────────────────────────────────────────────

    private function importFromVideo(string $file, ?string $thumbFile): bool
    {
        $videoPath = "videos/exercises/{$file}";

        if (Exercise::where('video_path', $videoPath)->exists()) {
            return false;
        }

        [$name, $muscleKey, $equipmentKey] = $this->parseFilename(str_replace('.mp4', '', $file));

        if (! $name) return false;

        $muscleId    = $this->muscleMap[$muscleKey]    ?? $this->muscleMap['other'];
        $equipmentId = $this->equipmentMap[$equipmentKey] ?? $this->equipmentMap['none'];

        $thumbPath = $thumbFile ? "images/exercises/{$thumbFile}" : null;

        $exercise = Exercise::create([
            'equipment_id'      => $equipmentId,
            'primary_muscle_id' => $muscleId,
            'video_path'        => $videoPath,
            'thumbnail_path'    => $thumbPath,
            'is_custom'         => false,
            'exercise_type'     => $this->resolveType($muscleKey, $equipmentKey),
        ]);

        $this->insertTranslations($exercise->id, $name);

        return true;
    }

    // ── Import from image only ────────────────────────────────────────────────

    private function importFromImage(string $file): bool
    {
        $imagePath = "images/exercises/{$file}";

        if (Exercise::where('thumbnail_path', $imagePath)->exists()) {
            return false;
        }

        [$name, $muscleKey, $equipmentKey] = $this->parseFilename(str_replace('.jpg', '', $file));

        if (! $name) return false;

        $muscleId    = $this->muscleMap[$muscleKey]       ?? $this->muscleMap['other'];
        $equipmentId = $this->equipmentMap[$equipmentKey] ?? $this->equipmentMap['none'];

        $exercise = Exercise::create([
            'equipment_id'      => $equipmentId,
            'primary_muscle_id' => $muscleId,
            'video_path'        => null,
            'thumbnail_path'    => $imagePath,
            'is_custom'         => false,
            'exercise_type'     => $this->resolveType($muscleKey, $equipmentKey),
        ]);

        $this->insertTranslations($exercise->id, $name);

        return true;
    }

    // ── Translations ──────────────────────────────────────────────────────────

    private function insertTranslations(int $exerciseId, string $nameEn): void
    {
        if ($this->option('skip-translate')) {
            $namePt = $nameEn;
            $nameEs = $nameEn;
        } else {
            $namePt = $this->translateGoogle($nameEn, 'pt') ?? $nameEn;
            $nameEs = $this->translateGoogle($nameEn, 'es') ?? $nameEn;
        }

        ExerciseTranslation::insert([
            ['exercise_id' => $exerciseId, 'locale' => 'en', 'name' => $nameEn, 'created_at' => now(), 'updated_at' => now()],
            ['exercise_id' => $exerciseId, 'locale' => 'pt', 'name' => $namePt, 'created_at' => now(), 'updated_at' => now()],
            ['exercise_id' => $exerciseId, 'locale' => 'es', 'name' => $nameEs, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    private function translateGoogle(string $text, string $target): ?string
    {
        try {
            $response = Http::timeout(5)->get('https://translate.googleapis.com/translate_a/single', [
                'client' => 'gtx',
                'sl'     => 'en',
                'tl'     => $target,
                'dt'     => 't',
                'q'      => $text,
            ]);

            return $response->json('0.0.0') ?? null;
        } catch (\Throwable) {
            return null;
        }
    }

    // ── Exercise type ─────────────────────────────────────────────────────────

    private function resolveType(string $muscleKey, string $equipmentKey): string
    {
        if (in_array($muscleKey, ['cardio', 'plyometrics'])) {
            return 'cardio';
        }

        if ($equipmentKey === 'none') {
            return 'bodyweight';
        }

        return 'strength';
    }
}
