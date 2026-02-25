<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Routine extends Model
{

    protected $fillable = [
        'name',
        'is_active',
    ];

    public function routineExercises()
    {
        return $this->hasMany(RoutineExercise::class)
            ->orderBy('order');
    }

    // relação ao pivot
    public function exercises()
    {
        return $this->hasMany(RoutineExercise::class);
    }

}
