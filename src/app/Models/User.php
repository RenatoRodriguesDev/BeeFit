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

    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
        'role',
        'plan',
        'stripe_customer_id',
        'stripe_subscription_id',
        'subscription_status',
        'subscription_ends_at',
        'avatar_path',
        'birthdate',
        'height_cm',
        'weight_kg',
        'gender',
        'theme_color',
        'theme_mode',
        'is_private',
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
}