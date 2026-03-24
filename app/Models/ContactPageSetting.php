<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactPageSetting extends Model
{
    protected $table = 'contact_page_settings';

    protected $guarded = [];

    protected $casts = [
        'phones' => 'array',
        'emails' => 'array',
    ];

    public static function singleton(): self
    {
        return static::query()->firstOrFail();
    }
}
