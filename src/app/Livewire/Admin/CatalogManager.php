<?php

namespace App\Livewire\Admin;

use App\Models\Equipment;
use App\Models\EquipmentTranslation;
use App\Models\Muscle;
use App\Models\MuscleTranslation;
use Livewire\Component;

class CatalogManager extends Component
{
    public string $tab = 'equipment'; // equipment | muscles

    // Form state
    public bool $showForm = false;
    public string $formType = 'equipment'; // equipment | muscle
    public ?int $editingId = null;
    public string $activeLocale = 'en';
    public array $translations = [
        'en' => ['name' => ''],
        'pt' => ['name' => ''],
        'es' => ['name' => ''],
    ];

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->closeForm();
    }

    public function openCreate(string $type): void
    {
        $this->formType = $type;
        $this->editingId = null;
        $this->activeLocale = 'en';
        $this->translations = ['en' => ['name' => ''], 'pt' => ['name' => ''], 'es' => ['name' => '']];
        $this->showForm = true;
    }

    public function openEdit(string $type, int $id): void
    {
        $this->formType = $type;
        $this->editingId = $id;
        $this->activeLocale = 'en';
        $this->translations = ['en' => ['name' => ''], 'pt' => ['name' => ''], 'es' => ['name' => '']];

        if ($type === 'equipment') {
            $model = Equipment::with('translations')->findOrFail($id);
        } else {
            $model = Muscle::with('translations')->findOrFail($id);
        }

        foreach ($model->translations as $t) {
            if (isset($this->translations[$t->locale])) {
                $this->translations[$t->locale] = ['name' => $t->name];
            }
        }

        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'translations.en.name' => 'required|string|max:100',
            'translations.pt.name' => 'nullable|string|max:100',
            'translations.es.name' => 'nullable|string|max:100',
        ]);

        if ($this->formType === 'equipment') {
            $model = $this->editingId ? Equipment::findOrFail($this->editingId) : Equipment::create([]);
            foreach ($this->translations as $locale => $data) {
                if (empty($data['name'])) continue;
                EquipmentTranslation::updateOrCreate(
                    ['equipment_id' => $model->id, 'locale' => $locale],
                    ['name' => $data['name']]
                );
            }
        } else {
            $model = $this->editingId ? Muscle::findOrFail($this->editingId) : Muscle::create([]);
            foreach ($this->translations as $locale => $data) {
                if (empty($data['name'])) continue;
                MuscleTranslation::updateOrCreate(
                    ['muscle_id' => $model->id, 'locale' => $locale],
                    ['name' => $data['name']]
                );
            }
        }

        $this->closeForm();
        $label = $this->formType === 'equipment' ? 'Equipamento' : 'Músculo';
        $this->dispatch('toast', message: $label . ($this->editingId ? ' atualizado.' : ' criado.'), type: 'success');
    }

    public function delete(string $type, int $id): void
    {
        if ($type === 'equipment') {
            Equipment::findOrFail($id)->delete();
            $this->dispatch('toast', message: 'Equipamento eliminado.', type: 'success');
        } else {
            Muscle::findOrFail($id)->delete();
            $this->dispatch('toast', message: 'Músculo eliminado.', type: 'success');
        }
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
    }

    public function render()
    {
        $equipment = Equipment::with('translations')->get()->sortBy(fn($e) => $e->translate('en')?->name);
        $muscles   = Muscle::with('translations')->get()->sortBy(fn($m) => $m->translate('en')?->name);

        return view('livewire.admin.catalog-manager', compact('equipment', 'muscles'))
            ->layout('layouts.admin')
            ->title('Admin — ' . __('app.catalog'));
    }
}
