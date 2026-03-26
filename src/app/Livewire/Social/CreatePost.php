<?php

namespace App\Livewire\Social;

use App\Models\Post;
use App\Models\Workout;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreatePost extends Component
{
    use WithFileUploads;

    public string $description = '';
    public string $emoji = '💪';
    public $photo = null;
    public ?int $workoutId = null;
    public bool $showModal = false;
    public bool $showEmojiPicker = false;

    public function mount(?int $workoutId = null): void
    {
        $this->workoutId = $workoutId;
        $this->showModal = $workoutId !== null;
    }

    public function openModal(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['description', 'photo', 'emoji', 'showEmojiPicker']);
        $this->emoji = '💪';
    }

    public function selectEmoji(string $emoji): void
    {
        $this->emoji = $emoji;
        $this->showEmojiPicker = false;
    }

    public function publish(): void
    {
        $this->validate([
            'description' => 'nullable|string|max:500',
            'photo'       => 'nullable|image|max:4096',
        ]);

        $photoPath = null;
        if ($this->photo) {
            $photoPath = $this->photo->store('posts', 'public');
        }

        Post::create([
            'user_id'     => auth()->id(),
            'workout_id'  => $this->workoutId,
            'emoji'       => $this->emoji,
            'description' => $this->description,
            'photo_path'  => $photoPath,
        ]);

        $this->closeModal();
        $this->dispatch('toast', message: __('app.post_published'), type: 'success');
        $this->redirect(route('social.feed'));
    }

    public function render()
    {
        $workout = $this->workoutId
            ? Workout::find($this->workoutId)
            : null;

        return view('livewire.social.create-post', compact('workout'));
    }
}
