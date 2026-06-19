<?php

namespace App\Http\Requests\PricesPage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'promos' => 'nullable|array',
            'promos.*.id' => 'nullable|integer|exists:price_promos,id',
            'promos.*.pending_delete' => 'nullable|boolean',
            'promos.*.title' => 'nullable|string|max:255',
            'promos.*.description' => 'nullable|string|max:2000',
            'promos.*.existing_image' => 'nullable|string|max:2048',
            'promos.*.image' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
            'promos.*.image_size' => ['nullable', Rule::in(['3/4', '2/3', '1/2', '1/3', '1/4'])],
            'promos.*.image_on_left' => 'nullable|boolean',
            'promos.*.placement' => ['nullable', Rule::in(['top', 'bottom', 'left', 'right'])],
            'promos.*.is_active' => 'nullable|boolean',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            foreach ($this->input('promos', []) as $index => $promo) {
                if (!empty($promo['pending_delete'])) {
                    continue;
                }

                $hasExistingImage = !empty($promo['existing_image']);
                $hasUploadedImage = $this->hasFile("promos.$index.image");

                if (!$hasExistingImage && !$hasUploadedImage) {
                    $validator->errors()->add("promos.$index.image", 'Для акции нужно загрузить фото.');
                }
            }
        });
    }

    public function messages()
    {
        return [
            'promos.*.id.exists' => 'Одна из акций не найдена.',
            'promos.*.image.image' => 'Файл должен быть изображением.',
            'promos.*.image.mimes' => 'Допустимые форматы: JPG, JPEG, PNG.',
            'promos.*.image.max' => 'Максимальный размер изображения — 5 МБ.',
            'promos.*.description.max' => 'Описание не должно превышать 2000 символов.',
        ];
    }
}
