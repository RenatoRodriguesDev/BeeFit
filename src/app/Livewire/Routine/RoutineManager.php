<?php

namespace App\Livewire\Routine;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Routine;

class RoutineManager extends Component
{
    public $name;
    public $showModal = false;

    public $showDeleteModal = false;
    public $routineToDelete = null;

    public function createRoutine()
    {
        $this->validate([
            'name' => 'required|min:3'
        ]);

        if (!auth()->user()->canCreateRoutine()) {
            $this->dispatch(
                'toast',
                message: __('app.routine_limit_reached'),
                type: 'error'
            );
            return redirect()->route('subscription.plans');
        }

        $routine = auth()->user()->routines()->create([
            'name' => $this->name,
            'is_active' => true,
        ]);

        // Desativar outras
        auth()->user()->routines()
            ->where('id', '!=', $routine->id)
            ->update(['is_active' => false]);

        $this->reset(['name', 'showModal']);


        return redirect()->route('routines.index', $routine);
    }

    public function render()
    {
        return view('livewire.routine-manager');
    }
}