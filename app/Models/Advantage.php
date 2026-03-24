<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Advantage extends Model
{
    use HasFactory;

    protected $table = 'advantages';

    protected $fillable = [
        'title',
        'description',
        'image',
        'sort_order',
        'image_on_left',
    ];

    protected $casts = [
        'image_on_left' => 'boolean',
    ];

    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        if (Str::startsWith($this->image, ['/'])) {
            return url(ltrim($this->image, '/'));
        }

        return url('storage/' . ltrim($this->image, '/'));
    }
}
