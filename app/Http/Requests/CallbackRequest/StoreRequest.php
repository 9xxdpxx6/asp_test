<?php

namespace App\Http\Requests\CallbackRequest;

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
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|string|max:50',
            'comment' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'full_name.required' => 'Поле "ФИО" обязательно для заполнения.',
            'full_name.string' => 'Поле "ФИО" должно быть строкой.',
            'full_name.max' => 'Поле "ФИО" не должно превышать 255 символов.',

            'phone.required' => 'Поле "Телефон" обязательно для заполнения.',
            'phone.string' => 'Поле "Телефон" должно быть строкой.',
            'phone.max' => 'Поле "Телефон" не должно превышать 20 символов.',

            'email.string' => 'Поле "Почта" должно быть строкой.',
            'email.max' => 'Поле "Почта" не должно превышать 50 символов.',

            'comment.string' => 'Поле "Комментарий" должно быть строкой.',
            'comment.max' => 'Поле "Комментарий" не должно превышать 255 символов.',
        ];
    }
}
