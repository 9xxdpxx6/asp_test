<?php

namespace App\Http\Resources\Discount;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class DiscountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
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
            'description' => $this->description,
            'preview' => $preview,
            'percentage' => $this->percentage,
        ];
    }
}
