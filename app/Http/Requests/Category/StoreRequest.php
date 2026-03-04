<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|unique:categories,slug|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|between:0,999999.99',
            'duration' => 'nullable|integer|min:1',
            'icon' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,heic,heif|max:5120',
            'blocks' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Поле "Название" обязательно для заполнения.',
            'name.string' => 'Поле "Название" должно быть строкой.',
            'name.max' => 'Поле "Название" не должно превышать 255 символов.',
            'slug.required' => 'Поле "URL" обязательно для заполнения.',
            'slug.string' => 'Поле "URL" должно быть строкой.',
            'slug.unique' => 'Категория с таким URL уже существует.',
            'slug.max' => 'Поле "URL" не должно превышать 255 символов.',
            'description.string' => 'Поле "Описание" должно быть строкой.',
            'price.required' => 'Поле "Цена" обязательно для заполнения.',
            'price.numeric' => 'Поле "Цена" должно быть числом.',
            'price.between' => 'Поле "Цена" должно быть в пределах от 0 до 999999.99.',
            'duration.integer' => 'Поле "Длительность" должно быть целым числом.',
            'duration.min' => 'Поле "Длительность" должно быть больше 0.',
            'image.image' => 'Фото должно быть изображением.',
            'image.mimes' => 'Допустимые форматы: JPEG, PNG, HEIC.',
            'image.max' => 'Максимальный размер фото — 5 МБ.',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        if (isset($data['blocks']) && is_string($data['blocks'])) {
            $data['blocks'] = json_decode($data['blocks'], true) ?? [];
        }

        return $data;
    }
}
