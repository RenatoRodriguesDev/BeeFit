<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExerciseTranslation extends Model
{
    protected $fillable = [
        'exercise_id',
        'locale',
        'name',
        'description',
    ];

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}