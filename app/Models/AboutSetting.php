<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutSetting extends Model
{
    use HasFactory;

    protected $table = 'about_settings';

    protected $fillable = [
        'hero_title',
        'hero_subtitle',
        'cta_title',
        'cta_text',
        'cta_button_text',
        'cta_icon',
        'cta_href',
    ];
}
