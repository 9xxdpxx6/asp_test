<?php

namespace App\Http\Requests\Category;

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
        $categoryId = optional($this->route('category'))->id;

        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . ($categoryId ?? 'NULL') . ',id',
            'description' => 'required|string',
            'price' => 'required|numeric|between:0,999999.99',
            'duration' => 'nullable|integer|min:1',
            'icon' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Поле "Название" обязательно для заполнения.',
            'name.string' => 'Поле "Название" должно быть строкой.',
            'name.max' => 'Поле "Название" не должно превышать 255 символов.',
            
            'slug.required' => 'Поле "Слаг" обязательно для заполнения.',
            'slug.string' => 'Поле "Слаг" должно быть строкой.',
            'slug.unique' => 'Категория с таким слагом уже существует.',
            'slug.max' => 'Поле "Слаг" не должно превышать 255 символов.',
            
            'description.required' => 'Поле "Описание" обязательно для заполнения.',
            'description.string' => 'Поле "Описание" должно быть строкой.',
            
            'price.required' => 'Поле "Цена" обязательно для заполнения.',
            'price.numeric' => 'Поле "Цена" должно быть числом.',
            'price.between' => 'Поле "Цена" должно быть в пределах от 0 до 999999.99.',
            
            'duration.integer' => 'Поле "Длительность" должно быть целым числом.',
            'duration.min' => 'Поле "Длительность" должно быть больше 0.',
            
            'icon.required' => 'Поле "Иконка" обязательно для заполнения.',
            'icon.string' => 'Поле "Иконка" должно быть строкой.',
            'icon.max' => 'Поле "Иконка" не должно превышать 255 символов.',
        ];
    }
}
