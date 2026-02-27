<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workout extends Model
{
    protected $fillable = [
        'user_id',
        'routine_id',
        'started_at',
        'finished_at',
        'status',
        'paused_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'paused_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function routine()
    {
        return $this->belongsTo(Routine::class);
    }

    public function exercises()
    {
        return $this->hasMany(WorkoutExercise::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}