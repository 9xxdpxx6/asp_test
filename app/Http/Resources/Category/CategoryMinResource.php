<?php

namespace App\Http\Resources\Category;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class CategoryMinResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $image = null;
        if ($this->image) {
            $image = Str::startsWith($this->image, ['http://', 'https://'])
                ? $this->image
                : url('storage/' . $this->image);
        }

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'duration' => $this->duration,
            'price' => $this->price,
            'icon' => $this->icon,
            'image' => $image,
            'images' => $this->images ? CategoryImageResource::collection($this->images) : [],
        ];
    }
}
