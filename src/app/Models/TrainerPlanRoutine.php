<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainerPlanRoutine extends Model
{
    protected $fillable = [
        'trainer_plan_id',
        'routine_id',
        'week_number',
        'day_label',
        'order',
        'notes',
    ];

    public function plan()
    {
        return $this->belongsTo(TrainerPlan::class, 'trainer_plan_id');
    }

    public function routine()
    {
        return $this->belongsTo(Routine::class)->withCount('exercises');
    }
}
