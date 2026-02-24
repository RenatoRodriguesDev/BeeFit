<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTranslations;

class Muscle extends Model
{
    use HasTranslations;

    protected function translationModel(): string
    {
        return MuscleTranslation::class;
    }
}
