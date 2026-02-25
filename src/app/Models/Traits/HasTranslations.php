<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasTranslations
{
    abstract protected function translationModel(): string;

    public function translations(): HasMany
    {
        return $this->hasMany($this->translationModel());
    }

    public function translate(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations
            ->where('locale', $locale)
            ->first()
            ?? $this->translations
                ->where('locale', config('app.fallback_locale'))
                ->first();
    }
}