<?php

namespace App\Http\Requests\Advantage;

use Illuminate\Foundation\Http\FormRequest;
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
            'advantages' => 'required|array|min:1',
            'advantages.*.id' => 'nullable|integer|exists:advantages,id',
            'advantages.*.pending_delete' => 'nullable|boolean',
            'advantages.*.title' => 'nullable|string|max:255',
            'advantages.*.description' => 'nullable|string',
            'advantages.*.existing_image' => 'nullable|string|max:2048',
            'advantages.*.image' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
            'advantages.*.image_on_left' => 'nullable|boolean',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            foreach ($this->input('advantages', []) as $index => $advantage) {
                if (!empty($advantage['pending_delete'])) {
                    continue;
                }

                $hasExistingImage = !empty($advantage['existing_image']);
                $hasUploadedImage = $this->hasFile("advantages.$index.image");

                if (empty($advantage['title'])) {
                    $validator->errors()->add("advantages.$index.title", 'Заполните заголовок преимущества.');
                }

                if (empty($advantage['description'])) {
                    $validator->errors()->add("advantages.$index.description", 'Заполните описание преимущества.');
                }

                if (!$hasExistingImage && !$hasUploadedImage) {
                    $validator->errors()->add("advantages.$index.image", 'Для каждого преимущества нужно загрузить изображение.');
                }
            }
        });
    }

    public function messages()
    {
        return [
            'advantages.required' => 'Добавьте хотя бы одно преимущество.',
            'advantages.array' => 'Некорректный формат списка преимуществ.',
            'advantages.min' => 'Добавьте хотя бы одно преимущество.',
            'advantages.*.id.exists' => 'Одно из преимуществ не найдено.',
            'advantages.*.title.required' => 'Заполните заголовок преимущества.',
            'advantages.*.title.max' => 'Заголовок преимущества не должен превышать 255 символов.',
            'advantages.*.description.required' => 'Заполните описание преимущества.',
            'advantages.*.image.image' => 'Файл изображения должен быть картинкой.',
            'advantages.*.image.mimes' => 'Допустимые форматы изображений: JPG, JPEG, PNG.',
            'advantages.*.image.max' => 'Максимальный размер изображения — 5 МБ.',
        ];
    }
}
