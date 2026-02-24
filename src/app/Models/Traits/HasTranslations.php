<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasTranslations
{
    public function translations(): HasMany
    {
        return $this->hasMany($this->translationModel());
    }

    public function getNameAttribute()
    {
        $locale = app()->getLocale();

        $translation = $this->translations
            ->where('locale', $locale)
            ->first()
            ?? $this->translations->where('locale', config('app.fallback_locale'))->first();

        return $translation?->name;
    }

    abstract protected function translationModel(): string;
}