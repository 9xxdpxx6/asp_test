<?php

namespace App\Http\Requests\Category;

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
            'name' => 'string',
            'description' => 'string',
            'price' => ['required', 'numeric', 'between:0,999999.99'],
            'duration' => 'integer',
        ];
    }

    public function messages()
    {
        return [
            'name.string' => 'Поле "Название" должно быть строкой.',
            'description.string' => 'Поле "Описание" должно быть строкой.',
            'price.required' => 'Поле "Цена" обязательно для заполнения.',
            'price.numeric' => 'Поле "Цена" должно быть числом.',
            'price.between' => 'Поле "Цена" должно быть в пределах от 0 до 999999.99.',
            'duration.integer' => 'Поле "Длительность" должно быть целым числом.',
        ];
    }
}
