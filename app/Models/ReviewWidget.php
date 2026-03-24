<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewWidget extends Model
{
    use HasFactory;

    protected $table = 'review_widgets';

    protected $fillable = [
        'slug',
        'title',
        'description',
        'provider',
        'render_type',
        'config',
        'show_on_home',
        'home_sort_order',
        'is_active',
    ];

    protected $casts = [
        'config' => 'array',
        'show_on_home' => 'boolean',
        'is_active' => 'boolean',
    ];
}
