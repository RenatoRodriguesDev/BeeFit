<?php

namespace App\Providers;
use App\Livewire\Routine\RoutineEditor;
use App\Livewire\Routine\RoutineManager;
use Livewire\Livewire;
use App\Livewire\Library\LibraryPanel;
use App\Livewire\Library\ExerciseViewer;

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
        Livewire::component('library-panel', LibraryPanel::class);
        Livewire::component('exercise-viewer', ExerciseViewer::class);
        Livewire::component('routine-manager', RoutineManager::class);
        Livewire::component('routine-editor', RoutineEditor::class);

    }
}
