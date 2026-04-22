<?php

namespace App\Livewire\Routine;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Routine;

class RoutineManager extends Component
{
    public string $name = '';
    public string $emoji = '💪';
    public bool $showModal = false;
    public bool $showEmojiPicker = false;

    public function selectEmoji(string $emoji): void
    {
        $this->emoji = $emoji;
        $this->showEmojiPicker = false;
    }

    public $showDeleteModal = false;
    public $routineToDelete = null;

    protected $listeners = ['refreshRoutines' => '$refresh'];

    public function createRoutine()
    {
        $this->validate([
            'name'  => 'required|min:3',
            'emoji' => 'required|string|max:10',
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
            'name'  => $this->name,
            'emoji' => $this->emoji,
        ]);

        $this->reset(['name', 'emoji', 'showModal', 'showEmojiPicker']);
        $this->emoji = '💪';


        return redirect()->route('routines.index', $routine);
    }

    public function render()
    {
        $user      = auth()->user();
        $canCreate = $user->canCreateRoutine();
        $isPaid    = $user->isPremium() || $user->isTrainer() || $user->isAdmin();

        return view('livewire.routine-manager', compact('canCreate', 'isPaid'))
            ->title(__('app.routines'));
    }
}