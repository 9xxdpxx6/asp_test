<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FooterSetting extends Model
{
    protected $fillable = [
        'phone',
        'email',
        'address',
        'logo_description',
        'social_links',
    ];

    protected $casts = [
        'social_links' => 'array',
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(FooterDocument::class)->orderBy('sort_order');
    }
}
