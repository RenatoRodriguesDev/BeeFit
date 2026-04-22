<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Routine extends Model
{

    protected $fillable = [
        'name',
        'emoji',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];



    // relação ao pivot
    public function exercises()
    {
        return $this->hasMany(RoutineExercise::class);
    }

}
