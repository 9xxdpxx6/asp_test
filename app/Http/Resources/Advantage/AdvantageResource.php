<?php

namespace App\Http\Resources\Advantage;

use Illuminate\Http\Resources\Json\JsonResource;

class AdvantageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image_url,
            'sort_order' => $this->sort_order,
            'image_on_left' => (bool) $this->image_on_left,
        ];
    }
}
