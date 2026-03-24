<?php

namespace App\Http\Resources\ReviewWidget;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewWidgetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => data_get($this->resource, 'id'),
            'slug' => data_get($this->resource, 'slug'),
            'title' => data_get($this->resource, 'title'),
            'description' => data_get($this->resource, 'description'),
            'provider' => data_get($this->resource, 'provider'),
            'render_type' => data_get($this->resource, 'render_type'),
            'config' => data_get($this->resource, 'config', []),
        ];
    }
}
