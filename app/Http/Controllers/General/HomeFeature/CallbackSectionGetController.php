<?php

namespace App\Http\Controllers\General\HomeFeature;

use App\Http\Controllers\Controller;
use App\Models\HomeFeatureSection;
use App\Models\HomeSectionSetting;

class CallbackSectionGetController extends Controller
{
    public function __invoke()
    {
        $defaults = $this->defaults();

        $setting = HomeSectionSetting::query()
            ->where('section', HomeFeatureSection::CALLBACK_FORM)
            ->first();

        $payload = array_merge(
            $defaults['payload'],
            is_array($setting?->payload) ? $setting->payload : []
        );

        return response()->json([
            'data' => [
                'heading' => $setting?->heading ?: $defaults['heading'],
                'subheading' => $setting?->subheading ?: $defaults['subheading'],
                'phone_label' => $payload['phone_label'],
                'form_title' => $payload['form_title'],
                'name_placeholder' => $payload['name_placeholder'],
                'phone_placeholder' => $payload['phone_placeholder'],
                'email_placeholder' => $payload['email_placeholder'],
                'comment_placeholder' => $payload['comment_placeholder'],
                'button_text' => $payload['button_text'],
            ],
        ]);
    }

    private function defaults(): array
    {
        return [
            'heading' => 'Готовы начать обучение?',
            'subheading' => 'Оставьте заявку, и мы свяжемся с вами, чтобы ответить на все вопросы и помочь с записью.',
            'payload' => [
                'phone_label' => '+7 (961) 526-23-59',
                'form_title' => 'Обратный звонок',
                'name_placeholder' => 'Ваше имя *',
                'phone_placeholder' => 'Телефон *',
                'email_placeholder' => 'Электронная почта',
                'comment_placeholder' => 'Комментарий',
                'button_text' => 'Отправить заявку',
            ],
        ];
    }
}
