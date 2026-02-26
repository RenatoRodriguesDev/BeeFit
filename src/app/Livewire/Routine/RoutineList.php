<?php

namespace App\Livewire\Routine;

use Livewire\Component;
use App\Models\Routine;
use Illuminate\Support\Facades\Auth;

class RoutineList extends Component
{
    public $showDeleteModal = false;
    public $routineToDelete;

    protected $listeners = ['refreshRoutines' => '$refresh'];

    public function confirmDelete($id)
    {
        $this->routineToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function deleteRoutine()
    {
        Routine::where('user_id', Auth::id())
            ->where('id', $this->routineToDelete)
            ->delete();

        $this->reset(['showDeleteModal', 'routineToDelete']);

        $this->dispatch(
            'toast',
            message: __('app.routine_deleted_success'),
            type: 'success'
        );

        $this->dispatch('refreshRoutines');
    }

    public function closeDeleteModal()
    {
        $this->reset(['showDeleteModal', 'routineToDelete']);
    }

    public function render()
    {
        return view('livewire.routine-list', [
            'routines' => Routine::where('user_id', Auth::id())
                ->withCount('exercises')
                ->get()
        ]);
    }
}