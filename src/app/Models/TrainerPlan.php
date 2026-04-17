<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainerPlan extends Model
{
    protected $fillable = [
        'trainer_id',
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function planRoutines()
    {
        return $this->hasMany(TrainerPlanRoutine::class)
            ->orderBy('week_number')
            ->orderBy('order');
    }

    public function assignments()
    {
        return $this->hasMany(TrainerPlanAssignment::class);
    }

    public function activeAssignments()
    {
        return $this->assignments()->where('is_active', true);
    }

    /** IDs of clients this plan is actively assigned to */
    public function assignedClientIds(): array
    {
        return $this->activeAssignments()->pluck('client_id')->toArray();
    }

    /** Total weeks in this plan */
    public function totalWeeks(): int
    {
        return $this->planRoutines()->max('week_number') ?? 1;
    }
}
