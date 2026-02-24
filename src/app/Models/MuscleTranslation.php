<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MuscleTranslation extends Model
{
    protected $fillable = [
        'muscle_id',
        'locale',
        'name',
    ];

    public function muscle()
    {
        return $this->belongsTo(Muscle::class);
    }
}