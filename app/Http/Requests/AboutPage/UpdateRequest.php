<?php

namespace App\Http\Requests\AboutPage;

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
        $allowedHref = \App\Support\HeroCtaLinkCatalog::allowedPaths();

        return [
            'hero_title' => 'required|string|max:255',
            'hero_subtitle' => 'nullable|string|max:500',
            'cta_title' => 'required|string|max:255',
            'cta_text' => 'required|string',
            'cta_button_text' => 'required|string|max:255',
            'cta_icon' => 'nullable|string|max:120',
            'cta_href' => ['required', 'string', 'max:500', Rule::in($allowedHref)],

            'blocks' => 'required|array|min:1',
            'blocks.*.id' => 'nullable|integer|exists:about_blocks,id',
            'blocks.*.pending_delete' => 'nullable|boolean',
            'blocks.*.title' => 'nullable|string|max:255',
            'blocks.*.description' => 'nullable|string',
            'blocks.*.existing_image' => 'nullable|string|max:2048',
            'blocks.*.image' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
            'blocks.*.image_on_left' => 'nullable|boolean',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            foreach ($this->input('blocks', []) as $index => $block) {
                if (!empty($block['pending_delete'])) {
                    continue;
                }

                $hasExistingImage = !empty($block['existing_image']);
                $hasUploadedImage = $this->hasFile("blocks.$index.image");

                if (empty($block['description'])) {
                    $validator->errors()->add("blocks.$index.description", 'Заполните текст блока.');
                }

                if (!$hasExistingImage && !$hasUploadedImage) {
                    $validator->errors()->add("blocks.$index.image", 'Для каждого блока нужно изображение.');
                }
            }
        });
    }

    public function messages()
    {
        return [
            'hero_title.required' => 'Укажите заголовок страницы.',
            'cta_title.required' => 'Укажите заголовок финального блока.',
            'cta_text.required' => 'Укажите текст призыва к действию.',
            'cta_button_text.required' => 'Укажите текст кнопки.',
            'blocks.required' => 'Добавьте хотя бы один блок.',
            'blocks.min' => 'Добавьте хотя бы один блок.',
            'blocks.*.id.exists' => 'Один из блоков не найден.',
            'blocks.*.image.image' => 'Файл изображения должен быть картинкой.',
            'blocks.*.image.mimes' => 'Допустимые форматы изображений: JPG, JPEG, PNG.',
            'blocks.*.image.max' => 'Максимальный размер изображения — 5 МБ.',
        ];
    }
}
