<?php

namespace App\Http\Requests\Discount;

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
        $discountsId = optional($this->route('discount'))->id;

        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:discounts,slug,' . ($discountsId ?? 'NULL') . ',id',
            'percentage' => 'required|numeric|min:0|max:100',
            'description' => 'required|string',
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
            'slug.unique' => 'Скидка с таким слагом уже существует.',
            
            'percentage.required' => 'Поле "Процент" обязательно для заполнения.',
            'percentage.numeric' => 'Поле "Процент" должно быть числом.',
            'percentage.min' => 'Поле "Процент" должно быть больше или равно 0.',
            'percentage.max' => 'Поле "Процент" не должно превышать 100.',
            
            'description.required' => 'Поле "Описание" обязательно для заполнения.',
            'description.string' => 'Поле "Описание" должно быть строкой.',
        ];
    }
}
