<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\LibraryController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\RoutinesController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
        Route::get('/routines', [RoutinesController::class, 'index'])
        ->name('routines.index');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/{locale}/library', [LibraryController::class, 'index'])
        ->where('locale', 'en|es|pt')
        ->name('library.index');
});

require __DIR__.'/auth.php';
