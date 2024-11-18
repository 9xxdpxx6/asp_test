<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    use Filterable;

    protected $table = 'posts';
    protected $guarded = false;

    public function postImages()
    {
        return $this->hasMany(PostImage::class);
    }

}
