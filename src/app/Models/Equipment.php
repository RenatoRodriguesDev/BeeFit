<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTranslations;

class Equipment extends Model
{
    use HasTranslations;

    protected function translationModel(): string
    {
        return EquipmentTranslation::class;
    }
}
