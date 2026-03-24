<?php

namespace App\Http\Resources\About;

use Illuminate\Http\Resources\Json\JsonResource;

class AboutBlockResource extends JsonResource
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
