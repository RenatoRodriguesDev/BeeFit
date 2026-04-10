<?php

namespace App\Livewire\Admin;

use App\Models\Equipment;
use App\Models\Exercise;
use App\Models\ExerciseTranslation;
use App\Models\Muscle;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ExerciseManager extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public string $filterEquipment = '';
    public string $filterMuscle = '';

    public bool $showForm = false;
    public ?int $editingId = null;

    // Form fields
    public int|string $equipmentId = '';
    public int|string $muscleId = '';
    public bool $isCustom = false;
    public string $exerciseType = 'strength';
    public $thumbnail = null;
    public $video = null;
    public string $keepThumbnail = '';
    public string $keepVideo = '';

    // Translations
    public array $translations = [
        'en' => ['name' => '', 'description' => ''],
        'pt' => ['name' => '', 'description' => ''],
        'es' => ['name' => '', 'description' => ''],
    ];
    public string $activeLocale = 'en';

    protected $queryString = ['search', 'filterEquipment', 'filterMuscle'];

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedFilterEquipment(): void { $this->resetPage(); }
    public function updatedFilterMuscle(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editingId = null;
    }

    public function openEdit(int $id): void
    {
        $exercise = Exercise::with('translations')->findOrFail($id);
        $this->editingId = $id;
        $this->equipmentId   = $exercise->equipment_id ?? '';
        $this->muscleId      = $exercise->primary_muscle_id ?? '';
        $this->isCustom      = (bool) $exercise->is_custom;
        $this->exerciseType  = $exercise->exercise_type ?? 'strength';
        $this->keepThumbnail = $exercise->thumbnail_path ?? '';
        $this->keepVideo = $exercise->video_path ?? '';
        $this->thumbnail = null;
        $this->video = null;

        $this->translations = [
            'en' => ['name' => '', 'description' => ''],
            'pt' => ['name' => '', 'description' => ''],
            'es' => ['name' => '', 'description' => ''],
        ];
        foreach ($exercise->translations as $t) {
            if (isset($this->translations[$t->locale])) {
                $this->translations[$t->locale] = ['name' => $t->name, 'description' => $t->description ?? ''];
            }
        }

        $this->activeLocale = 'en';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'equipmentId'              => 'required|exists:equipment,id',
            'muscleId'                 => 'required|exists:muscles,id',
            'translations.en.name'    => 'required|string|max:200',
            'translations.pt.name'    => 'nullable|string|max:200',
            'translations.es.name'    => 'nullable|string|max:200',
            'thumbnail'               => 'nullable|image|max:4096',
            'video'                   => 'nullable|mimes:mp4,mov,webm|max:51200',
        ]);

        $data = [
            'equipment_id'      => $this->equipmentId,
            'primary_muscle_id' => $this->muscleId,
            'is_custom'         => $this->isCustom,
            'exercise_type'     => $this->exerciseType,
        ];

        if ($this->thumbnail) {
            $data['thumbnail_path'] = $this->thumbnail->store('exercises/thumbnails', 'public');
        } elseif ($this->editingId) {
            $data['thumbnail_path'] = $this->keepThumbnail ?: null;
        }

        if ($this->video) {
            $data['video_path'] = 'storage/' . $this->video->store('exercises/videos', 'public');
        } elseif ($this->editingId) {
            $data['video_path'] = $this->keepVideo ?: null;
        }

        if ($this->editingId) {
            $exercise = Exercise::findOrFail($this->editingId);
            $exercise->update($data);
        } else {
            $exercise = Exercise::create($data);
        }

        foreach ($this->translations as $locale => $trans) {
            if (empty($trans['name'])) continue;
            ExerciseTranslation::updateOrCreate(
                ['exercise_id' => $exercise->id, 'locale' => $locale],
                ['name' => $trans['name'], 'description' => $trans['description'] ?? '']
            );
        }

        $this->showForm = false;
        $this->resetForm();
        $this->dispatch('toast', message: $this->editingId ? 'Exercício atualizado.' : 'Exercício criado.', type: 'success');
        $this->editingId = null;
    }

    public function delete(int $id): void
    {
        Exercise::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Exercício eliminado.', type: 'success');
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->equipmentId  = '';
        $this->muscleId     = '';
        $this->isCustom     = false;
        $this->exerciseType = 'strength';
        $this->thumbnail    = null;
        $this->video = null;
        $this->keepThumbnail = '';
        $this->keepVideo = '';
        $this->translations = [
            'en' => ['name' => '', 'description' => ''],
            'pt' => ['name' => '', 'description' => ''],
            'es' => ['name' => '', 'description' => ''],
        ];
        $this->activeLocale = 'en';
    }

    public function render()
    {
        $exercises = Exercise::with(['translations', 'equipment.translations', 'primaryMuscle.translations'])
            ->when($this->search, function ($q) {
                $q->whereHas('translations', fn($t) => $t->where('name', 'like', '%' . $this->search . '%'));
            })
            ->when($this->filterEquipment, fn($q) => $q->where('equipment_id', $this->filterEquipment))
            ->when($this->filterMuscle, fn($q) => $q->where('primary_muscle_id', $this->filterMuscle))
            ->latest()
            ->paginate(20);

        $equipmentList = Equipment::with('translations')->get();
        $muscleList    = Muscle::with('translations')->get();

        return view('livewire.admin.exercise-manager', compact('exercises', 'equipmentList', 'muscleList'))
            ->layout('layouts.admin')
            ->title('Admin — ' . __('app.exercises'));
    }
}
