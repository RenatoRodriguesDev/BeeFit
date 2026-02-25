<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutineSet extends Model
{
    protected $fillable = [
        'routine_exercise_id',
        'set_number',
        'weight',
        'reps',
    ];
    public function routineExercise()
    {
        return $this->belongsTo(RoutineExercise::class);
    }
}
