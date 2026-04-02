<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTranslations;

class Exercise extends Model
{
    use HasTranslations;

    protected $fillable = [
        'equipment_id',
        'primary_muscle_id',
        'thumbnail_path',
        'video_path',
    ];

    protected function translationModel(): string
    {
        return ExerciseTranslation::class;
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function primaryMuscle()
    {
        return $this->belongsTo(Muscle::class, 'primary_muscle_id');
    }

    public function getHasVideoAttribute(): bool
    {
        return $this->video_path && file_exists(public_path($this->video_path));
    }

    /**
     * Resolve thumbnail URL for both legacy (public/images/) and
     * admin-uploaded (storage/public/) exercise thumbnails.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if (! $this->thumbnail_path) {
            return null;
        }

        if (str_starts_with($this->thumbnail_path, 'images/') || str_starts_with($this->thumbnail_path, 'videos/')) {
            return asset($this->thumbnail_path);
        }

        return asset('storage/' . $this->thumbnail_path);
    }
}
