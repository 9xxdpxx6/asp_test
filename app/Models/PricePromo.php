<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PricePromo extends Model
{
    use HasFactory;

    protected $table = 'price_promos';

    protected $fillable = [
        'title',
        'description',
        'image',
        'image_size',
        'image_on_left',
        'placement',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'image_on_left' => 'boolean',
        'is_active' => 'boolean',
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
