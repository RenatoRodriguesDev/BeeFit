<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Routine extends Model
{

    protected $fillable = [
        'user_id',
        'name',
        'emoji',
        'is_active',
        'share_token',
    ];

    public function shareUrl(): ?string
    {
        return $this->share_token
            ? route('routine.shared', $this->share_token)
            : null;
    }

    protected $casts = [
        'is_active'   => 'boolean',
        'share_token' => 'string',
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exercises()
    {
        return $this->hasMany(RoutineExercise::class);
    }

}
