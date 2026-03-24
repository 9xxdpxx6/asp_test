<?php

namespace App\Http\Requests\HomeFeature;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLearningProcessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'heading' => 'required|string|max:255',
            'subheading' => 'required|string|max:500',
            'blocks' => 'required|array|size:4',
            'blocks.*.title' => 'required|string|max:255',
            'blocks.*.description' => 'required|string|max:5000',
            'blocks.*.icon' => 'required|string|max:128',
        ];
    }

    public function attributes(): array
    {
        return [
            'heading' => 'заголовок секции',
            'subheading' => 'подзаголовок',
            'blocks' => 'блоки',
            'blocks.*.title' => 'заголовок шага',
            'blocks.*.description' => 'текст шага',
            'blocks.*.icon' => 'иконка',
        ];
    }
}
