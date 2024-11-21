<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $postId = optional($this->route('post'))->id; // Получаем ID поста или NULL, если его нет

        return [
            'title' => 'required|string',
            'slug' => 'required|string|unique:posts,slug,' . ($postId ?? 'NULL') . ',id',
            'images' => 'nullable|array',
            'content' => 'nullable|string',
            'image_ids_for_delete' => 'nullable|array',
            'image_urls_for_delete' => 'nullable|array',
        ];
    }


    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required' => 'Заголовок обязателен для заполнения.',
            'title.string' => 'Заголовок должен быть строкой.',
            'slug.required' => ' ',
            'slug.unique' => 'Новость с таким названием уже существует.',
            'images.array' => 'Изображения должны быть в виде массива.',
            'content.string' => 'Содержимое должно быть строкой.',
            'image_ids_for_delete.array' => 'Идентификаторы изображений для удаления должны быть в виде массива.',
            'image_urls_for_delete.array' => 'URL изображений для удаления должны быть в виде массива.',
        ];
    }
}
