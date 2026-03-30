<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        'uuid',
    ];

    protected static function booted(): void
    {
        static::creating(function (Workout $workout) {
            if (empty($workout->uuid)) {
                $workout->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

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