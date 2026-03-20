<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use App\Support\HtmlEntityDecoder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    use Filterable;

    protected $table = 'categories';
    protected $guarded = false;

    public function getDescriptionAttribute($value)
    {
        return HtmlEntityDecoder::decodeString($value);
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = HtmlEntityDecoder::decodeString($value);
    }

    public function categoryImages()
    {
        return $this->hasMany(CategoryImage::class);
    }

    public function blocks()
    {
        return $this->hasMany(CategoryBlock::class)->orderBy('sort_order');
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if (is_numeric($value)) {
            return $this->where('id', $value)->firstOrFail();
        }

        return $this->where('slug', $value)->firstOrFail();
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
