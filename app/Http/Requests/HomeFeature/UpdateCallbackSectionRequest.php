<?php

namespace App\Http\Requests\HomeFeature;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCallbackSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'heading' => 'required|string|max:255',
            'subheading' => 'required|string|max:1000',
            'phone_label' => 'required|string|max:120',
            'form_title' => 'required|string|max:255',
            'name_placeholder' => 'required|string|max:120',
            'phone_placeholder' => 'required|string|max:120',
            'email_placeholder' => 'required|string|max:120',
            'comment_placeholder' => 'required|string|max:120',
            'button_text' => 'required|string|max:120',
        ];
    }

    public function attributes(): array
    {
        return [
            'heading' => 'заголовок секции',
            'subheading' => 'описание секции',
            'phone_label' => 'телефон',
            'form_title' => 'заголовок формы',
            'name_placeholder' => 'подсказка поля имени',
            'phone_placeholder' => 'подсказка поля телефона',
            'email_placeholder' => 'подсказка поля почты',
            'comment_placeholder' => 'подсказка поля комментария',
            'button_text' => 'текст кнопки',
        ];
    }
}
