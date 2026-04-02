<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Exercise;
use App\Models\Workout;
use App\Models\Post;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            'total_users'      => User::count(),
            'users_today'      => User::whereDate('created_at', today())->count(),
            'users_this_month' => User::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'premium_users'    => User::where('subscription_status', 'active')->count(),
            'admin_users'      => User::where('role', 'admin')->count(),
            'trainer_users'    => User::where('role', 'trainer')->count(),
            'total_exercises'  => Exercise::count(),
            'custom_exercises' => Exercise::where('is_custom', true)->count(),
            'total_workouts'   => Workout::where('status', 'completed')->count(),
            'workouts_today'   => Workout::where('status', 'completed')->whereDate('updated_at', today())->count(),
            'total_posts'      => Post::count(),
            'posts_today'      => Post::whereDate('created_at', today())->count(),
        ];

        $recentUsers = User::latest()->take(8)->get();

        $subscriptionBreakdown = [
            'active'    => User::where('subscription_status', 'active')->count(),
            'trialing'  => User::where('subscription_status', 'trialing')->count(),
            'past_due'  => User::where('subscription_status', 'past_due')->count(),
            'canceled'  => User::where('subscription_status', 'canceled')->count(),
            'none'      => User::whereIn('subscription_status', ['none', 'incomplete'])->orWhereNull('subscription_status')->count(),
        ];

        return view('livewire.admin.dashboard', compact('stats', 'recentUsers', 'subscriptionBreakdown'))
            ->layout('layouts.admin');
    }
}
