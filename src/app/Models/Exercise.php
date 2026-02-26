<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTranslations;

class Exercise extends Model
{
    use HasTranslations;

    protected function translationModel(): string
    {
        return ExerciseTranslation::class;
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function primaryMuscle()
    {
        return $this->belongsTo(Muscle::class, 'primary_muscle_id');
    }

    public function getHasVideoAttribute(): bool
    {
        return $this->video_path && file_exists(public_path($this->video_path));
    }
}
