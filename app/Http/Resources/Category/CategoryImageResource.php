<?php

namespace App\Http\Resources\Category;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CategoryImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $exists = Storage::disk('public')->exists($this->image_path);

        return [
            'id' => $this->id,
            'path' => $exists ? asset('storage/' . $this->image_path) : null, // Если файла нет, возвращаем null
            'size' => $exists ? Storage::disk('public')->size($this->image_path) : 0, // Если файла нет, размер 0
            'name' => $exists ? str_replace('images/categories/', '', $this->image_path) : 'File not found', // Указываем текст вместо имени
        ];
    }
}
