<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\Dashboard;
use App\Livewire\Routine\RoutineEditor;
use App\Livewire\Social\CreatePost;
use App\Livewire\Social\SocialFeed;
use App\Livewire\Social\UserProfile;
use App\Livewire\Workout\WorkoutShow;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\LibraryController;
use App\Http\Controllers\Web\RoutinesController;
use App\Livewire\Workout\WorkoutSession;
use App\Http\Controllers\Web\SubscriptionController;

Route::get('/', function () {
    return redirect()->route('login');
})->middleware('guest');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class)
    ->name('dashboard');
    Route::get('/routines', [RoutinesController::class, 'index'])
        ->name('routines.index');
    Route::get('/library', [LibraryController::class, 'index'])
        ->name('library.index');

    Route::get('/routines/{routine}', RoutineEditor::class)
        ->name('routines.show');

    Route::get('/workouts/{workout}/session', WorkoutSession::class)
    ->name('workouts.session');

    Route::get('/workouts/{workout}', WorkoutShow::class
    )->name('workouts.show');
    Route::get('/statistics', \App\Livewire\Statistics::class)->name('statistics');

    // Social
    Route::get('/social', SocialFeed::class)->name('social.feed');
    Route::get('/social/profile/{username?}', UserProfile::class)->name('social.profile');
    Route::get('/social/post/create', CreatePost::class)->name('social.create-post');
    Route::get('/social/post/create/{workoutId}', CreatePost::class)->name('social.create-post-workout');

    // Subscrições
    Route::get('/plans', [SubscriptionController::class, 'plans'])->name('subscription.plans');
    Route::post('/subscription/checkout', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::get('/subscription/success', [SubscriptionController::class, 'success'])->name('subscription.success');
    Route::get('/subscription/portal', [SubscriptionController::class, 'portal'])->name('subscription.portal');
});

Route::post('/stripe/webhook', [SubscriptionController::class, 'webhook'])
    ->name('stripe.webhook')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
