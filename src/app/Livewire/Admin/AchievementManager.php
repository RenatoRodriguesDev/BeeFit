<?php

namespace App\Livewire\Admin;

use App\Models\Achievement;
use Livewire\Component;

class AchievementManager extends Component
{
    public bool $showForm = false;
    public ?int $editingId = null;

    // Form fields
    public string $key = '';
    public string $icon = '';
    public int $xp_reward = 50;
    public array $translations = [
        'en' => ['name' => '', 'description' => ''],
        'pt' => ['name' => '', 'description' => ''],
        'es' => ['name' => '', 'description' => ''],
    ];
    public string $activeLocale = 'en';

    public function openCreate(): void
    {
        $this->reset(['key', 'icon', 'xp_reward', 'translations', 'editingId']);
        $this->xp_reward = 50;
        $this->translations = [
            'en' => ['name' => '', 'description' => ''],
            'pt' => ['name' => '', 'description' => ''],
            'es' => ['name' => '', 'description' => ''],
        ];
        $this->activeLocale = 'en';
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $achievement = Achievement::findOrFail($id);
        $this->editingId   = $id;
        $this->key         = $achievement->key;
        $this->icon        = $achievement->icon;
        $this->xp_reward   = $achievement->xp_reward;
        $this->activeLocale = 'en';

        // Load translations from lang files
        foreach (['en', 'pt', 'es'] as $locale) {
            $this->translations[$locale] = [
                'name'        => __('achievements.' . $achievement->key . '.name', [], $locale),
                'description' => __('achievements.' . $achievement->key . '.description', [], $locale),
            ];
        }

        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'key'      => 'required|string|alpha_dash',
            'icon'     => 'required|string|max:10',
            'xp_reward' => 'required|integer|min:0|max:9999',
            'translations.en.name'        => 'required|string|max:80',
            'translations.en.description' => 'required|string|max:200',
            'translations.pt.name'        => 'required|string|max:80',
            'translations.pt.description' => 'required|string|max:200',
            'translations.es.name'        => 'required|string|max:80',
            'translations.es.description' => 'required|string|max:200',
        ]);

        if ($this->editingId) {
            $achievement = Achievement::findOrFail($this->editingId);
            $achievement->update(['key' => $this->key, 'icon' => $this->icon, 'xp_reward' => $this->xp_reward]);
        } else {
            $achievement = Achievement::create(['key' => $this->key, 'icon' => $this->icon, 'xp_reward' => $this->xp_reward]);
        }

        // Write translations to lang files
        foreach (['en', 'pt', 'es'] as $locale) {
            $this->updateLangFile($locale, $achievement->key, $this->translations[$locale]);
        }

        $this->closeForm();
    }

    public function delete(int $id): void
    {
        Achievement::findOrFail($id)->delete();
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->reset(['key', 'icon', 'xp_reward', 'editingId']);
        $this->translations = [
            'en' => ['name' => '', 'description' => ''],
            'pt' => ['name' => '', 'description' => ''],
            'es' => ['name' => '', 'description' => ''],
        ];
    }

    private function updateLangFile(string $locale, string $key, array $data): void
    {
        $path = lang_path("{$locale}/achievements.php");

        if (file_exists($path)) {
            $current = include $path;
        } else {
            $current = [];
        }

        $current[$key] = [
            'name'        => $data['name'],
            'description' => $data['description'],
        ];

        ksort($current);

        $export = "<?php\n\nreturn [\n";
        foreach ($current as $k => $v) {
            $name        = addslashes($v['name']);
            $description = addslashes($v['description']);
            $export .= "    '{$k}' => ['name' => '{$name}', 'description' => '{$description}'],\n";
        }
        $export .= "];\n";

        file_put_contents($path, $export);
    }

    public function render()
    {
        return view('livewire.admin.achievement-manager', [
            'achievements' => Achievement::orderBy('xp_reward')->get(),
        ])
            ->title('Admin — ' . __('app.achievements'));
    }
}
