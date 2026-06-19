<?php

namespace App\Http\Resources\PricePromo;

use Illuminate\Http\Resources\Json\JsonResource;

class PricePromoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image_url,
            'image_size' => $this->image_size ?: '1/2',
            'image_on_left' => (bool) $this->image_on_left,
            'placement' => $this->placement ?: 'top',
            'sort_order' => $this->sort_order,
        ];
    }
}
