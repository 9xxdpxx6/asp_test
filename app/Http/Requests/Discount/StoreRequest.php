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

    protected function prepareForValidation(): void
    {
        if ($this->has('percentage') && $this->input('percentage') === '') {
            $this->merge(['percentage' => null]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|unique:discounts,slug|string|max:255',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'excerpt' => 'nullable|string|max:2000',
            'preview' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'blocks' => 'nullable|string',
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
            
            'percentage.numeric' => 'Поле "Процент" должно быть числом.',
            'percentage.min' => 'Поле "Процент" должно быть больше или равно 0.',
            'percentage.max' => 'Поле "Процент" не должно превышать 100.',
            'preview.image' => 'Файл превью должен быть изображением.',
            'preview.mimes' => 'Допустимые форматы превью: JPEG, PNG, WebP.',
            'preview.max' => 'Максимальный размер превью — 5 МБ.',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        if (isset($data['blocks']) && is_string($data['blocks'])) {
            $data['blocks'] = json_decode($data['blocks'], true) ?? [];
        }

        return $data;
    }
}
