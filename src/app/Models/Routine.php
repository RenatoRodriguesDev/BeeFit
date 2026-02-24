<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Routine extends Model
{
    public function exercises()
    {
        return $this->hasMany(RoutineExercise::class)->orderBy('order');
    }
}
