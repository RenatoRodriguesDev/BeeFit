<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainerClient extends Model
{
    protected $fillable = [
        'trainer_id',
        'client_id',
        'status',
        'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
        ];
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isInvited(): bool
    {
        return $this->status === 'invited';
    }
}
