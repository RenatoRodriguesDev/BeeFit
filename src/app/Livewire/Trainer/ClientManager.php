<?php

namespace App\Livewire\Trainer;

use App\Models\TrainerClient;
use App\Models\User;
use Livewire\Component;

class ClientManager extends Component
{
    public string $search = '';
    public bool $showInviteModal = false;
    public string $inviteUsername = '';
    public ?int $removeClientId = null;
    public bool $showRemoveModal = false;

    public function inviteClient(): void
    {
        $this->validate(['inviteUsername' => 'required|string|min:2']);

        $trainer = auth()->user();

        $client = User::where('username', $this->inviteUsername)
            ->orWhere('email', $this->inviteUsername)
            ->first();

        if (! $client) {
            $this->addError('inviteUsername', __('app.trainer_client_not_found'));
            return;
        }

        if ($client->id === $trainer->id) {
            $this->addError('inviteUsername', __('app.trainer_client_self_invite'));
            return;
        }

        $existing = TrainerClient::where('trainer_id', $trainer->id)
            ->where('client_id', $client->id)
            ->first();

        if ($existing) {
            $this->addError('inviteUsername', __('app.trainer_client_already_exists'));
            return;
        }

        TrainerClient::create([
            'trainer_id' => $trainer->id,
            'client_id'  => $client->id,
            'status'     => 'invited',
        ]);

        $this->reset(['inviteUsername', 'showInviteModal']);

        $this->dispatch('toast', message: __('app.trainer_client_invited'), type: 'success');
    }

    public function confirmRemove(int $clientId): void
    {
        $this->removeClientId = $clientId;
        $this->showRemoveModal = true;
    }

    public function removeClient(): void
    {
        TrainerClient::where('trainer_id', auth()->id())
            ->where('client_id', $this->removeClientId)
            ->delete();

        $this->reset(['removeClientId', 'showRemoveModal']);
        $this->dispatch('toast', message: __('app.trainer_client_removed'), type: 'success');
    }

    public function suspendClient(int $clientId): void
    {
        TrainerClient::where('trainer_id', auth()->id())
            ->where('client_id', $clientId)
            ->update(['status' => 'suspended']);

        $this->dispatch('toast', message: __('app.trainer_client_suspended'), type: 'info');
    }

    public function reactivateClient(int $clientId): void
    {
        TrainerClient::where('trainer_id', auth()->id())
            ->where('client_id', $clientId)
            ->update(['status' => 'active']);

        $this->dispatch('toast', message: __('app.trainer_client_reactivated'), type: 'success');
    }

    public function render()
    {
        $trainer = auth()->user();

        $clients = TrainerClient::where('trainer_id', $trainer->id)
            ->with('client')
            ->when($this->search, function ($q) {
                $q->whereHas('client', fn($u) =>
                    $u->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('username', 'like', '%' . $this->search . '%')
                );
            })
            ->latest()
            ->get();

        return view('livewire.trainer.client-manager', compact('clients'))
            ->title(__('app.trainer_clients'));
    }
}
