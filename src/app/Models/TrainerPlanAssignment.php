<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainerPlanAssignment extends Model
{
    protected $fillable = [
        'trainer_plan_id',
        'client_id',
        'trainer_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function plan()
    {
        return $this->belongsTo(TrainerPlan::class, 'trainer_plan_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
}
