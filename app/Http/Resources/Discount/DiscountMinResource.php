<?php

namespace App\Http\Resources\Discount;

use App\Http\Resources\Post\PostImageResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountMinResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'preview' => $this->preview_path ? asset('storage/' . $this->preview_path) : null,
            'percentage' => $this->percentage,
        ];
    }
}
