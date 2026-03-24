<?php

namespace App\Models;

use App\Support\HtmlEntityDecoder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountBlock extends Model
{
    use HasFactory;

    protected $table = 'discount_blocks';

    protected $guarded = false;

    public function getContentAttribute($value)
    {
        $decoded = json_decode($value, true);

        if (!is_array($decoded)) {
            return [];
        }

        return HtmlEntityDecoder::decodeRecursive($decoded);
    }

    public function setContentAttribute($value)
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (!is_array($value)) {
            $value = [];
        }

        $this->attributes['content'] = json_encode(
            HtmlEntityDecoder::decodeRecursive($value),
            JSON_UNESCAPED_UNICODE
        );
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
}
