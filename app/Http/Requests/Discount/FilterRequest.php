<?php

namespace App\Http\Requests\Discount;

use Illuminate\Foundation\Http\FormRequest;

class FilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'keyword' => 'nullable|string|max:255',
            'page' => 'nullable|integer|min:1',
            'sort' => 'nullable|string|in:percentage_asc,percentage_desc,date_asc,date_desc,default',
        ];
    }

    public function messages()
    {
        return [
            'keyword.string' => 'Ключевое слово должно быть строкой.',
            'keyword.max' => 'Ключевое слово не должно превышать 255 символов.',
            'page.integer' => 'Параметр страницы должен быть числом.',
            'page.min' => 'Номер страницы должен быть больше 0.',
            'sort.in' => 'Неверный параметр сортировки.',
        ];
    }
}
