<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalRecord extends Model
{
    protected $fillable = [
        'user_id',
        'exercise_id',
        'workout_id',
        'max_weight',
        'reps_at_max_weight',
        'max_volume_set',
        'max_reps',
        'weight_at_max_reps',
        'estimated_1rm',
        'max_distance',
        'max_duration',
        'best_pace',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }

    public function workout()
    {
        return $this->belongsTo(Workout::class);
    }

    /**
     * Calcula o 1RM estimado pela fórmula de Epley.
     */
    public static function epley(float $weight, int $reps): float
    {
        if ($reps === 1) return $weight;
        return round($weight * (1 + $reps / 30), 2);
    }

    /**
     * Actualiza (ou cria) o recorde pessoal de um utilizador para um exercício
     * com base nos sets de um treino acabado de completar.
     */
    public static function updateFromWorkout(int $userId, int $exerciseId, int $workoutId, \Illuminate\Support\Collection $sets): void
    {
        $exercise = Exercise::find($exerciseId);

        // Cardio: métricas de duração/distância
        if ($exercise?->isCardio()) {
            self::updateCardioFromWorkout($userId, $exerciseId, $workoutId, $sets);
            return;
        }

        // Ignora sets sem reps (peso=0 é válido para exercícios bodyweight)
        $sets = $sets->filter(fn($s) => $s->reps > 0);

        if ($sets->isEmpty()) return;

        $weightedSets = $sets->filter(fn($s) => $s->weight > 0);

        // Calcula os melhores valores deste treino
        $maxWeight       = $weightedSets->isNotEmpty() ? $weightedSets->max('weight') : 0;
        $repsAtMaxWeight = $maxWeight > 0 ? $weightedSets->where('weight', $maxWeight)->max('reps') : 0;
        $maxVolSet       = $weightedSets->isNotEmpty() ? $weightedSets->map(fn($s) => $s->weight * $s->reps)->max() : 0;
        $maxReps         = $sets->max('reps');
        $weightAtMaxReps = $sets->where('reps', $maxReps)->max('weight') ?? 0;
        $best1rm         = $weightedSets->isNotEmpty()
            ? $weightedSets->map(fn($s) => self::epley($s->weight, $s->reps))->max()
            : 0;

        $existing = self::where('user_id', $userId)
            ->where('exercise_id', $exerciseId)
            ->first();

        if (!$existing) {
            self::create([
                'user_id'            => $userId,
                'exercise_id'        => $exerciseId,
                'workout_id'         => $workoutId,
                'max_weight'         => $maxWeight,
                'reps_at_max_weight' => $repsAtMaxWeight,
                'max_volume_set'     => $maxVolSet,
                'max_reps'           => $maxReps,
                'weight_at_max_reps' => $weightAtMaxReps,
                'estimated_1rm'      => $best1rm,
            ]);
            return;
        }

        $updates = ['workout_id' => $workoutId];
        $updated = false;

        if ($maxWeight > ($existing->max_weight ?? 0)) {
            $updates['max_weight']          = $maxWeight;
            $updates['reps_at_max_weight']  = $repsAtMaxWeight;
            $updated = true;
        }

        if ($maxVolSet > ($existing->max_volume_set ?? 0)) {
            $updates['max_volume_set'] = $maxVolSet;
            $updated = true;
        }

        if ($maxReps > ($existing->max_reps ?? 0)) {
            $updates['max_reps']           = $maxReps;
            $updates['weight_at_max_reps'] = $weightAtMaxReps;
            $updated = true;
        }

        if ($best1rm > ($existing->estimated_1rm ?? 0)) {
            $updates['estimated_1rm'] = $best1rm;
            $updated = true;
        }

        if ($updated) {
            $existing->update($updates);
        }
    }

    public static function updateCardioFromWorkout(int $userId, int $exerciseId, int $workoutId, \Illuminate\Support\Collection $sets): void
    {
        $sets = $sets->filter(fn($s) => $s->duration_seconds > 0 || $s->distance_meters > 0);
        if ($sets->isEmpty()) return;

        $maxDistance = $sets->max('distance_meters') ?? 0;
        $maxDuration = $sets->max('duration_seconds') ?? 0;

        // Melhor ritmo: set com maior distância e duração (min seg/km)
        $bestPace = null;
        foreach ($sets as $s) {
            if ($s->distance_meters > 0 && $s->duration_seconds > 0) {
                $pace = (int) round($s->duration_seconds / ($s->distance_meters / 1000));
                if ($bestPace === null || $pace < $bestPace) {
                    $bestPace = $pace;
                }
            }
        }

        $existing = self::where('user_id', $userId)->where('exercise_id', $exerciseId)->first();

        if (! $existing) {
            self::create([
                'user_id'      => $userId,
                'exercise_id'  => $exerciseId,
                'workout_id'   => $workoutId,
                'max_distance' => $maxDistance,
                'max_duration' => $maxDuration,
                'best_pace'    => $bestPace,
            ]);
            return;
        }

        $updates = ['workout_id' => $workoutId];
        $updated = false;

        if ($maxDistance > ($existing->max_distance ?? 0)) { $updates['max_distance'] = $maxDistance; $updated = true; }
        if ($maxDuration > ($existing->max_duration ?? 0)) { $updates['max_duration'] = $maxDuration; $updated = true; }
        if ($bestPace !== null && ($existing->best_pace === null || $bestPace < $existing->best_pace)) {
            $updates['best_pace'] = $bestPace; $updated = true;
        }

        if ($updated) $existing->update($updates);
    }
}