<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PostResource extends JsonResource
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
            'title' => $this->title,
            'content' => $this->content,
            'preview' => $preview,
            'date' => $this->created_at,
        ];
    }
}
