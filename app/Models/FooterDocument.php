<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FooterDocument extends Model
{
    protected $fillable = [
        'footer_setting_id',
        'sort_order',
        'title',
        'file_path',
        'original_filename',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function footerSetting(): BelongsTo
    {
        return $this->belongsTo(FooterSetting::class);
    }
}
