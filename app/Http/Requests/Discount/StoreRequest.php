<?php

namespace App\Http\Requests\Discount;

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
            'name' => 'required|string',
            'slug' => 'required|unique:discounts,slug|string',
            'description' => 'nullable|string',
            'percentage' => 'required|numeric|min:0|max:100',
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
            'name.required' => 'Поле "Название" обязательно для заполнения.',
            'name.string' => 'Поле "Название" должно быть строкой.',
            
            'slug.required' => 'Поле "Слаг" обязательно для заполнения.',
            'slug.string' => 'Поле "Слаг" должно быть строкой.',
            'slug.unique' => 'Скидка с таким слагом уже существует.',
            
            'description.string' => 'Поле "Описание" должно быть строкой.',
            
            'percentage.required' => 'Поле "Процент" обязательно для заполнения.',
            'percentage.numeric' => 'Поле "Процент" должно быть числом.',
            'percentage.min' => 'Поле "Процент" должно быть больше или равно 0.',
            'percentage.max' => 'Поле "Процент" не должно превышать 100.',
        ];
    }
}
