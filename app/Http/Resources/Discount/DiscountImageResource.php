<?php

namespace App\Http\Resources\Discount;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DiscountImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'path' => asset('storage/' . $this->image_path),
            'size' => Storage::disk('public')->size($this->image_path),
            'name' => str_replace('iamges/discounts','', $this->image_path),
        ];
    }
}
