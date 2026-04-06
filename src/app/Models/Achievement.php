<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = ['key', 'icon', 'xp_reward'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_achievements')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }

    public function getNameAttribute(): string
    {
        return __('achievements.' . $this->key . '.name');
    }

    public function getDescriptionAttribute(): string
    {
        return __('achievements.' . $this->key . '.description');
    }
}
