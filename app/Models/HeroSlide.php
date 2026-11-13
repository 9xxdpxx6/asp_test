<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HeroSlide extends Model
{
    protected $table = 'hero_slides';

    protected $guarded = false;

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'video_width' => 'integer',
        'video_height' => 'integer',
        'duration_seconds' => 'integer',
        'video_bytes' => 'integer',
        'image_width' => 'integer',
        'image_height' => 'integer',
    ];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getVideoUrlAttribute(): ?string
    {
        return $this->video_path ? Storage::url($this->video_path) : null;
    }

    public function getPosterUrlAttribute(): ?string
    {
        return $this->poster_path ? Storage::url($this->poster_path) : null;
    }

    public function getPreviewUrlAttribute(): ?string
    {
        return $this->preview_path ? Storage::url($this->preview_path) : null;
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }

    public function getBackgroundUrlAttribute(): ?string
    {
        return $this->bg_path ? Storage::url($this->bg_path) : null;
    }
}
