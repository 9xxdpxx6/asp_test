<?php

namespace App\Http\Resources\Discount;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class DiscountHomeResource extends JsonResource
{
    public function toArray($request)
    {
        $preview = null;
        if ($this->preview_path) {
            $preview = Str::startsWith($this->preview_path, ['http://', 'https://'])
                ? $this->preview_path
                : asset('storage/' . $this->preview_path);
        }

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'title' => $this->name,
            'description' => $this->excerpt ?? '',
            'image' => $preview,
            'percentage' => $this->percentage,
        ];
    }
}
