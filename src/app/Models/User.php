<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Only user-controlled inputs. Privileged fields (role, plan, stripe_*, xp)
    // are intentionally excluded -- set them explicitly via property assignment + save().
    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
        'avatar_path',
        'birthdate',
        'height_cm',
        'weight_kg',
        'gender',
        'theme_color',
        'theme_mode',
        'username',
        'is_private',
        'xp',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'subscription_ends_at' => 'datetime',
            'birthdate'            => 'date',
            'password'             => 'hashed',
            'is_private'           => 'boolean',
        ];
    }

    // ─── Role helpers ──────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTrainer(): bool
    {
        return $this->role === 'trainer';
    }

    public function isPremium(): bool
    {
        return in_array($this->role, ['premium', 'admin'])
            || $this->hasActivePlan('premium');
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    // ─── Subscription helpers ──────────────────────────────────────

    public function hasActivePlan(string $plan): bool
    {
        return $this->plan === $plan
            && in_array($this->subscription_status, ['active', 'trialing']);
    }

    public function hasActiveSubscription(): bool
    {
        return in_array($this->subscription_status, ['active', 'trialing']);
    }

    public function subscriptionIsActive(): bool
    {
        return $this->hasActiveSubscription();
    }

    // ─── Plan limits ───────────────────────────────────────────────

    /** Número máximo de rotinas permitidas no plano actual */
    public function maxRoutines(): int
    {
        return match(true) {
            $this->isPremium()  => PHP_INT_MAX, // ilimitado
            $this->isTrainer()  => PHP_INT_MAX,
            $this->isAdmin()    => PHP_INT_MAX,
            default             => 3, // free
        };
    }

    public function canCreateRoutine(): bool
    {
        return $this->routines()->count() < $this->maxRoutines();
    }

    // ─── Profile helpers ───────────────────────────────────────────

    public function age(): ?int
    {
        return $this->birthdate?->age;
    }

    public function avatarUrl(): string
    {
        if ($this->avatar_path) {
            return asset('storage/' . $this->avatar_path);
        }

        // Fallback: avatar com iniciais (gerado por UI)
        return '';
    }

    public function initials(): string
    {
        $parts = explode(' ', $this->name);
        return strtoupper(
            count($parts) >= 2
                ? $parts[0][0] . $parts[1][0]
                : $parts[0][0]
        );
    }

    // ─── Relations ─────────────────────────────────────────────────

    public function routines()
    {
        return $this->hasMany(Routine::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // ─── Follow relations ──────────────────────────────────────────

    public function following()
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }

    public function followers()
    {
        return $this->hasMany(Follow::class, 'following_id');
    }

    public function isFollowing(User $user): bool
    {
        return $this->following()
            ->where('following_id', $user->id)
            ->where('status', 'accepted')
            ->exists();
    }

    public function isPendingFollow(User $user): bool
    {
        return $this->following()
            ->where('following_id', $user->id)
            ->where('status', 'pending')
            ->exists();
    }

    public function followRequests()
    {
        return $this->followers()->where('status', 'pending')->with('follower');
    }

    // ─── Social helpers ────────────────────────────────────────────

    public function followingIds(): array
    {
        return $this->following()
            ->where('status', 'accepted')
            ->pluck('following_id')
            ->toArray();
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    // ─── Achievements ──────────────────────────────────────────────

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }

    public function hasAchievement(string $key): bool
    {
        return $this->achievements()->where('key', $key)->exists();
    }

    // ─── XP & Level ────────────────────────────────────────────────

    // XP total para atingir o início de cada nível (índice = nível)
    private static array $levelThresholds = [
        1  => 0,
        2  => 200,
        3  => 500,
        4  => 900,
        5  => 1500,
        6  => 2300,
        7  => 3300,
        8  => 4600,
        9  => 6200,
        10 => 8200,
        15 => 18000,
        20 => 35000,
        25 => 60000,
        30 => 95000,
        40 => 185000,
        50 => 320000,
    ];

    public function level(): int
    {
        $xp    = $this->xp ?? 0;
        $level = 1;

        foreach (self::$levelThresholds as $lvl => $threshold) {
            if ($xp >= $threshold) {
                $level = $lvl;
            } else {
                break;
            }
        }

        // Para níveis intermédios não na tabela, interpola
        $maxDefined = max(array_keys(self::$levelThresholds));
        if ($level === $maxDefined) {
            // Acima do máximo: cada 10000 XP = 1 nível extra
            $extra = (int) floor(($xp - self::$levelThresholds[$maxDefined]) / 10000);
            $level += $extra;
        }

        return $level;
    }

    public function xpForCurrentLevel(): int
    {
        $lvl = $this->level();
        return self::$levelThresholds[$lvl] ?? $this->xpForLevelN($lvl);
    }

    public function xpForNextLevel(): int
    {
        $lvl = $this->level() + 1;
        return self::$levelThresholds[$lvl] ?? $this->xpForLevelN($lvl);
    }

    public function xpProgress(): int
    {
        return ($this->xp ?? 0) - $this->xpForCurrentLevel();
    }

    public function xpNeeded(): int
    {
        return $this->xpForNextLevel() - $this->xpForCurrentLevel();
    }

    public function xpProgressPercent(): int
    {
        $needed = $this->xpNeeded();
        if ($needed <= 0) return 100;
        return (int) min(100, floor($this->xpProgress() / $needed * 100));
    }

    private function xpForLevelN(int $n): int
    {
        $max = max(array_keys(self::$levelThresholds));
        return self::$levelThresholds[$max] + ($n - $max) * 10000;
    }

    public function levelTitle(): string
    {
        $lvl = $this->level();
        $tier = match(true) {
            $lvl >= 50 => 'immortal',
            $lvl >= 40 => 'legend',
            $lvl >= 30 => 'champion',
            $lvl >= 20 => 'warrior',
            $lvl >= 15 => 'athlete',
            $lvl >= 10 => 'veteran',
            $lvl >= 5  => 'trainee',
            default    => 'beginner',
        };

        return __('levels.' . $tier);
    }

    public function levelBadgeColor(): string
    {
        return match(true) {
            $this->level() >= 50 => 'from-red-500 to-orange-400',
            $this->level() >= 30 => 'from-yellow-400 to-amber-500',
            $this->level() >= 20 => 'from-violet-500 to-purple-600',
            $this->level() >= 10 => 'from-blue-500 to-cyan-400',
            $this->level() >= 5  => 'from-green-500 to-emerald-400',
            default               => 'from-zinc-500 to-zinc-400',
        };
    }

    public function addXp(int $amount): void
    {
        $this->increment('xp', $amount);
        $this->refresh();
    }
}