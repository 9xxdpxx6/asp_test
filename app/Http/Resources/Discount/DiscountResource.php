<?php

namespace App\Http\Resources\Discount;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class DiscountResource extends JsonResource
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
            'excerpt' => $this->excerpt,
            'description' => $this->description,
            'preview' => $preview,
            'percentage' => $this->percentage,
            'blocks' => DiscountBlockResource::collection($this->blocks),
        ];
    }
}
