<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\Dashboard;
use App\Livewire\Routine\RoutineEditor;
use App\Livewire\Workout\WorkoutShow;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\LibraryController;
use App\Http\Controllers\Web\RoutinesController;
use App\Livewire\Workout\WorkoutSession;

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
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
