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
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug,' . ($postId ?? 'NULL') . ',id',
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
            'title.required' => 'Поле "Заголовок" обязательно для заполнения.',
            'title.string' => 'Поле "Заголовок" должно быть строкой.',
            'title.max' => 'Поле "Заголовок" не должно превышать 255 символов.',
            
            'slug.required' => 'Поле "Слаг" обязательно для заполнения.',
            'slug.string' => 'Поле "Слаг" должно быть строкой.',
            'slug.unique' => 'Пост с таким слагом уже существует.',
            'slug.max' => 'Поле "Слаг" не должно превышать 255 символов.',
            
            'images.array' => 'Поле "Изображения" должно быть массивом.',
            
            'content.string' => 'Поле "Содержимое" должно быть строкой.',
            
            'image_ids_for_delete.array' => 'Поле "ID изображений для удаления" должно быть массивом.',
            'image_urls_for_delete.array' => 'Поле "URL изображений для удаления" должно быть массивом.',
        ];
    }
}
