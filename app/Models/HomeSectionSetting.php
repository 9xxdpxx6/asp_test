<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSectionSetting extends Model
{
    protected $table = 'home_section_settings';

    protected $guarded = false;

    protected $casts = [
        'payload' => 'array',
    ];

    public static function forSection(string $section): self
    {
        return static::query()->where('section', $section)->firstOrFail();
    }
}
