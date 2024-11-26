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
}