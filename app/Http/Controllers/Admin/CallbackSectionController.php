<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomeFeature\UpdateCallbackSectionRequest;
use App\Models\HomeFeatureSection;
use App\Models\HomeSectionSetting;

class CallbackSectionController extends Controller
{
    public function index()
    {
        $defaults = $this->defaults();

        $setting = HomeSectionSetting::query()->firstOrCreate(
            ['section' => HomeFeatureSection::CALLBACK_FORM],
            $defaults
        );

        $payload = array_merge(
            $defaults['payload'],
            is_array($setting->payload) ? $setting->payload : []
        );

        return view('admin.callback-section', [
            'heading' => old('heading', $setting->heading ?: $defaults['heading']),
            'subheading' => old('subheading', $setting->subheading ?: $defaults['subheading']),
            'payload' => [
                'phone_label' => old('phone_label', $payload['phone_label']),
                'form_title' => old('form_title', $payload['form_title']),
                'name_placeholder' => old('name_placeholder', $payload['name_placeholder']),
                'phone_placeholder' => old('phone_placeholder', $payload['phone_placeholder']),
                'email_placeholder' => old('email_placeholder', $payload['email_placeholder']),
                'comment_placeholder' => old('comment_placeholder', $payload['comment_placeholder']),
                'button_text' => old('button_text', $payload['button_text']),
            ],
        ]);
    }

    public function update(UpdateCallbackSectionRequest $request)
    {
        $validated = $request->validated();

        HomeSectionSetting::query()->updateOrCreate(
            ['section' => HomeFeatureSection::CALLBACK_FORM],
            [
                'heading' => $validated['heading'],
                'subheading' => $validated['subheading'],
                'payload' => [
                    'phone_label' => $validated['phone_label'],
                    'form_title' => $validated['form_title'],
                    'name_placeholder' => $validated['name_placeholder'],
                    'phone_placeholder' => $validated['phone_placeholder'],
                    'email_placeholder' => $validated['email_placeholder'],
                    'comment_placeholder' => $validated['comment_placeholder'],
                    'button_text' => $validated['button_text'],
                ],
            ]
        );

        return redirect()
            ->route('admin.callback-section')
            ->with('success', 'Секция «Обратный звонок» на главной обновлена.');
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
