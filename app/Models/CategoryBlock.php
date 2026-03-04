<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryBlock extends Model
{
    use HasFactory;

    protected $table = 'category_blocks';
    protected $guarded = false;

    protected $casts = [
        'content' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
