<?php

namespace App\Http\Requests\CallbackRequest;

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
        return [
            'note' => 'nullable|string|max:255',
            'status_id' => 'nullable|exists:statuses,id',
        ];
    }

    public function messages()
    {
        return [
            'status_id.exists' => 'Выбранный статус не существует.',
        ];
    }
}
