<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSetting extends Model
{
    protected $table = 'hero_settings';

    protected $guarded = false;

    protected $casts = [
        'trust_items' => 'array',
        'cta_buttons' => 'array',
    ];

    public static function singleton(): self
    {
        return static::query()->firstOrFail();
    }
}
