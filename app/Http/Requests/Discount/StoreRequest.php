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
            'slug' => 'required|string|unique:discounts,slug,' . ($discountsId ?? 'NULL') . ',id',
            'description' => 'nullable|string',
            'percentage' => 'required|numeric|min:0|max:100',
        ];

    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
}
