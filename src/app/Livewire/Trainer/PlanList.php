<?php

namespace App\Livewire\Trainer;

use App\Models\TrainerPlan;
use Livewire\Component;

class PlanList extends Component
{
    public bool $showCreateModal = false;
    public string $planName = '';
    public string $planDescription = '';
    public ?int $deletePlanId = null;
    public bool $showDeleteModal = false;

    protected array $rules = [
        'planName'        => 'required|string|min:2|max:100',
        'planDescription' => 'nullable|string|max:500',
    ];

    public function createPlan(): void
    {
        $this->validate();

        TrainerPlan::create([
            'trainer_id'  => auth()->id(),
            'name'        => $this->planName,
            'description' => $this->planDescription ?: null,
            'is_active'   => true,
        ]);

        $this->reset(['planName', 'planDescription', 'showCreateModal']);
        $this->dispatch('toast', message: __('app.trainer_plan_created'), type: 'success');
    }

    public function confirmDelete(int $planId): void
    {
        $this->deletePlanId = $planId;
        $this->showDeleteModal = true;
    }

    public function deletePlan(): void
    {
        TrainerPlan::where('trainer_id', auth()->id())
            ->where('id', $this->deletePlanId)
            ->delete();

        $this->reset(['deletePlanId', 'showDeleteModal']);
        $this->dispatch('toast', message: __('app.trainer_plan_deleted'), type: 'success');
    }

    public function render()
    {
        $plans = TrainerPlan::where('trainer_id', auth()->id())
            ->withCount(['planRoutines', 'activeAssignments'])
            ->latest()
            ->get();

        return view('livewire.trainer.plan-list', compact('plans'))
            ->title(__('app.trainer_plans'));
    }
}
