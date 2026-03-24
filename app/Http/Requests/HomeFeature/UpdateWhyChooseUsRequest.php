<?php

namespace App\Http\Requests\HomeFeature;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWhyChooseUsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'heading' => 'required|string|max:255',
            'blocks' => 'required|array|size:3',
            'blocks.*.title' => 'required|string|max:255',
            'blocks.*.description' => 'required|string|max:5000',
            'blocks.*.icon' => 'required|string|max:128',
        ];
    }

    public function attributes(): array
    {
        return [
            'heading' => 'заголовок секции',
            'blocks' => 'блоки',
            'blocks.*.title' => 'заголовок карточки',
            'blocks.*.description' => 'текст карточки',
            'blocks.*.icon' => 'иконка',
        ];
    }
}
