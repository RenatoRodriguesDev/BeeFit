<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterRole = '';
    public string $filterPlan = '';

    public ?int $editingUserId = null;
    public string $editRole = '';
    public string $editPlan = '';
    public string $editSubscriptionStatus = '';

    protected $queryString = ['search', 'filterRole', 'filterPlan'];

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedFilterRole(): void { $this->resetPage(); }
    public function updatedFilterPlan(): void { $this->resetPage(); }

    public function editUser(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editingUserId = $id;
        $this->editRole = $user->role;
        $this->editPlan = $user->plan ?? 'free';
        $this->editSubscriptionStatus = $user->subscription_status ?? 'none';
    }

    public function saveUser(): void
    {
        $user = User::findOrFail($this->editingUserId);
        $user->update([
            'role'                => $this->editRole,
            'plan'                => $this->editPlan,
            'subscription_status' => $this->editSubscriptionStatus,
        ]);
        $this->editingUserId = null;
        $this->dispatch('toast', message: 'Utilizador atualizado.', type: 'success');
    }

    public function cancelEdit(): void
    {
        $this->editingUserId = null;
    }

        public function delete(int $id): void
    {
            User::findOrFail($id)->delete();
            $this->dispatch('toast', message: 'Utilizador eliminado.', type: 'success');
        
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('username', 'like', '%' . $this->search . '%');
            }))
            ->when($this->filterRole, fn($q) => $q->where('role', $this->filterRole))
            ->when($this->filterPlan, fn($q) => $q->where('plan', $this->filterPlan))
            ->latest()
            ->paginate(20);

        return view('livewire.admin.user-manager', compact('users'))
            ->layout('layouts.admin')
            ->title('Admin — ' . __('app.users'));
    }
}
