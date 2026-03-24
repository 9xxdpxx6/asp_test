<?php

namespace App\Http\Resources\Discount;

use Illuminate\Http\Resources\Json\JsonResource;

class DiscountBlockResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'content' => $this->content,
            'sort_order' => $this->sort_order,
        ];
    }
}
