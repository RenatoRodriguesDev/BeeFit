<?php

namespace App\Providers;
use App\Livewire\Library\ExerciseHistory;
use App\Livewire\Routine\RoutineEditor;
use App\Livewire\Routine\RoutineList;
use App\Livewire\Routine\RoutineManager;
use App\Livewire\Statistics;
use App\Livewire\Workout\ActiveWorkoutBanner;
use App\Livewire\Workout\WorkoutSession;
use App\Livewire\Workout\WorkoutShow;
use Livewire\Livewire;
use App\Livewire\Library\LibraryPanel;
use App\Livewire\Library\ExerciseViewer;
use Illuminate\Support\Facades\URL;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        Livewire::component('library-panel', LibraryPanel::class);
        Livewire::component('exercise-viewer', ExerciseViewer::class);
        Livewire::component('routine-manager', RoutineManager::class);
        Livewire::component('routine-editor', RoutineEditor::class);
        Livewire::component('routine-list', RoutineList::class);
        Livewire::component('workout-session', WorkoutSession::class);
        Livewire::component('workout-show', WorkoutShow::class);
        Livewire::component('active-workout-banner', ActiveWorkoutBanner::class);
        Livewire::component('statistics', Statistics::class);
        Livewire::component('exercise-history', ExerciseHistory::class);

    }
}
