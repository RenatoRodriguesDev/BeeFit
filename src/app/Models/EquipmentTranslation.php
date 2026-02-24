<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentTranslation extends Model
{
    protected $fillable = [
        'equipment_id',
        'locale',
        'name',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}