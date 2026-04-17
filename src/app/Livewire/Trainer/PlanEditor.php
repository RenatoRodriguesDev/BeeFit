<?php

namespace App\Livewire\Trainer;

use App\Models\Routine;
use App\Models\TrainerClient;
use App\Models\TrainerPlan;
use App\Models\TrainerPlanAssignment;
use App\Models\TrainerPlanRoutine;
use Livewire\Component;

class PlanEditor extends Component
{
    public TrainerPlan $trainerPlan;

    // Plan meta editing
    public string $name = '';
    public string $description = '';
    public bool $editingMeta = false;

    // Add routine to plan
    public bool $showAddRoutineModal = false;
    public int $selectedRoutineId = 0;
    public int $weekNumber = 1;
    public string $dayLabel = '';
    public string $notes = '';

    // Edit existing routine entry
    public bool $showEditEntryModal = false;
    public ?int $editingEntryId = null;
    public int $editWeekNumber = 1;
    public string $editDayLabel = '';
    public string $editNotes = '';

    // Assign to client
    public bool $showAssignModal = false;
    public int $selectedClientId = 0;

    public function mount(TrainerPlan $trainerPlan): void
    {
        abort_unless($trainerPlan->trainer_id === auth()->id(), 403);

        $this->trainerPlan   = $trainerPlan;
        $this->name          = $trainerPlan->name;
        $this->description   = $trainerPlan->description ?? '';
    }

    public function saveMeta(): void
    {
        $this->validate([
            'name'        => 'required|string|min:2|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $this->trainerPlan->update([
            'name'        => $this->name,
            'description' => $this->description ?: null,
        ]);

        $this->editingMeta = false;
        $this->dispatch('toast', message: __('app.saved'), type: 'success');
    }

    public function addRoutine(): void
    {
        $this->validate([
            'selectedRoutineId' => 'required|integer|min:1',
            'weekNumber'        => 'required|integer|min:1|max:52',
            'dayLabel'          => 'nullable|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'notes'             => 'nullable|string|max:300',
        ]);

        // Verify routine belongs to this trainer
        $routine = Routine::where('id', $this->selectedRoutineId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $order = TrainerPlanRoutine::where('trainer_plan_id', $this->trainerPlan->id)
            ->where('week_number', $this->weekNumber)
            ->max('order') ?? 0;

        TrainerPlanRoutine::create([
            'trainer_plan_id' => $this->trainerPlan->id,
            'routine_id'      => $routine->id,
            'week_number'     => $this->weekNumber,
            'day_label'       => $this->dayLabel ?: null,
            'order'           => $order + 1,
            'notes'           => $this->notes ?: null,
        ]);

        $this->reset(['showAddRoutineModal', 'selectedRoutineId', 'weekNumber', 'dayLabel', 'notes']);
        $this->weekNumber = 1;
        $this->trainerPlan->refresh();
        $this->dispatch('toast', message: __('app.trainer_routine_added'), type: 'success');
    }

    public function editEntry(int $entryId): void
    {
        $entry = TrainerPlanRoutine::whereHas('plan', fn($q) => $q->where('trainer_id', auth()->id()))
            ->findOrFail($entryId);

        $this->editingEntryId = $entryId;
        $this->editWeekNumber = $entry->week_number;
        $this->editDayLabel   = $entry->day_label ?? '';
        $this->editNotes      = $entry->notes ?? '';
        $this->showEditEntryModal = true;
    }

    public function updateEntry(): void
    {
        $this->validate([
            'editWeekNumber' => 'required|integer|min:1|max:52',
            'editDayLabel'   => 'nullable|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'editNotes'      => 'nullable|string|max:300',
        ]);

        TrainerPlanRoutine::whereHas('plan', fn($q) => $q->where('trainer_id', auth()->id()))
            ->where('id', $this->editingEntryId)
            ->update([
                'week_number' => $this->editWeekNumber,
                'day_label'   => $this->editDayLabel ?: null,
                'notes'       => $this->editNotes ?: null,
            ]);

        $this->reset(['showEditEntryModal', 'editingEntryId', 'editDayLabel', 'editNotes']);
        $this->editWeekNumber = 1;
        $this->trainerPlan->refresh();
        $this->dispatch('toast', message: __('app.saved'), type: 'success');
    }

    public function removeRoutineEntry(int $entryId): void
    {
        TrainerPlanRoutine::where('id', $entryId)
            ->whereHas('plan', fn($q) => $q->where('trainer_id', auth()->id()))
            ->delete();

        $this->trainerPlan->refresh();
        $this->dispatch('toast', message: __('app.trainer_routine_removed'), type: 'success');
    }

    public function assignToClient(): void
    {
        $this->validate(['selectedClientId' => 'required|integer|min:1']);

        // Verify client belongs to this trainer and is active
        $tc = TrainerClient::where('trainer_id', auth()->id())
            ->where('client_id', $this->selectedClientId)
            ->where('status', 'active')
            ->first();

        if (! $tc) {
            $this->addError('selectedClientId', __('app.trainer_client_not_active'));
            return;
        }

        TrainerPlanAssignment::firstOrCreate(
            [
                'trainer_plan_id' => $this->trainerPlan->id,
                'client_id'       => $this->selectedClientId,
            ],
            [
                'trainer_id' => auth()->id(),
                'is_active'  => true,
            ]
        );

        $this->reset(['showAssignModal', 'selectedClientId']);
        $this->trainerPlan->refresh();
        $this->dispatch('toast', message: __('app.trainer_plan_assigned'), type: 'success');
    }

    public function unassignClient(int $clientId): void
    {
        TrainerPlanAssignment::where('trainer_plan_id', $this->trainerPlan->id)
            ->where('client_id', $clientId)
            ->where('trainer_id', auth()->id())
            ->delete();

        $this->trainerPlan->refresh();
        $this->dispatch('toast', message: __('app.trainer_plan_unassigned'), type: 'success');
    }

    public function render()
    {
        $planRoutines = TrainerPlanRoutine::where('trainer_plan_id', $this->trainerPlan->id)
            ->with(['routine' => fn($q) => $q->withCount('exercises')])
            ->orderBy('week_number')
            ->orderBy('order')
            ->get()
            ->groupBy('week_number');

        $myRoutines = Routine::where('user_id', auth()->id())
            ->withCount('exercises')
            ->orderBy('name')
            ->get();

        $activeClients = TrainerClient::where('trainer_id', auth()->id())
            ->where('status', 'active')
            ->with('client')
            ->get();

        $assignments = TrainerPlanAssignment::where('trainer_plan_id', $this->trainerPlan->id)
            ->where('is_active', true)
            ->with('client')
            ->get();

        return view('livewire.trainer.plan-editor', compact(
            'planRoutines', 'myRoutines', 'activeClients', 'assignments'
        ))->title($this->trainerPlan->name . ' • ' . __('app.trainer_plans'));
    }
}
