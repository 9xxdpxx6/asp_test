<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|unique:posts,slug|string|max:255',
            'images' => 'nullable|array',
            'content' => 'nullable|string',
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
        ];
    }
}
