<?php

namespace App\Http\Requests\CallbackRequest;

use App\Models\Status;
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
        $statuses = Status::pluck('id')->toArray(); // Берем только идентификаторы (или другие уникальные поля)

        return [
            'keyword' => 'nullable|string|max:255',
            'sort' => 'nullable|string|in:date_asc,date_desc,default',
            'status' => 'nullable|integer|in:' . implode(',', $statuses), // Используем список идентификаторов статусов
        ];
    }

    public function messages()
    {
        return [
            'sort.in' => 'Неверный параметр сортировки.',
        ];
    }
}
