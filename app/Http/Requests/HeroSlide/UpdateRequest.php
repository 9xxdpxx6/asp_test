<?php

namespace App\Http\Requests\HeroSlide;

use App\Models\HeroSlide;
use App\Service\HeroSlideService;
use App\Support\HeroCtaLinkCatalog;
use App\Support\HeroSliderOptions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxSlides = max(1, (int) config('hero_slider.max_slides', 6));

        return [
            'main_slide_label' => ['required', 'string', 'max:60'],
            'main_slide_position' => ['required', Rule::in(['first', 'last'])],
            'slider_autoplay_seconds' => ['required', 'integer', Rule::in(HeroSliderOptions::autoplayChoices())],
            'slider_in_hero' => ['nullable', 'boolean'],
            'slider_show_cards' => ['nullable', 'boolean'],
            'cards_title' => ['nullable', 'string', 'max:120'],
            'cards_subtitle' => ['nullable', 'string', 'max:255'],

            'slides' => ['nullable', 'array', 'max:' . $maxSlides],
            'slides.*.id' => ['nullable', 'integer', 'exists:hero_slides,id'],
            'slides.*.token' => ['nullable', 'regex:/^[A-Za-z0-9]{24}$/'],
            'slides.*.pending_delete' => ['nullable', 'boolean'],
            'slides.*.is_active' => ['nullable', 'boolean'],
            'slides.*.tab_label' => ['nullable', 'string', 'max:60'],
            'slides.*.title' => ['nullable', 'string', 'max:255'],
            'slides.*.subtitle' => ['nullable', 'string', 'max:2000'],
            'slides.*.button_label' => ['nullable', 'string', 'max:120'],
            'slides.*.button_href' => ['nullable', 'string', 'max:500', Rule::in(array_merge([''], HeroCtaLinkCatalog::allowedPaths()))],
            'slides.*.frame_ratio' => ['nullable', Rule::in(array_keys(HeroSliderOptions::ratios()))],
            'slides.*.fit_mode' => ['nullable', Rule::in(array_keys(HeroSliderOptions::fits()))],
            'slides.*.media_side' => ['nullable', Rule::in(array_keys(HeroSliderOptions::sides()))],
            'slides.*.media_size' => ['nullable', Rule::in(array_keys(HeroSliderOptions::sizes()))],
            'slides.*.text_align' => ['nullable', Rule::in(array_keys(HeroSliderOptions::textAligns()))],
            'slides.*.text_valign' => ['nullable', Rule::in(array_keys(HeroSliderOptions::textValigns()))],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $service = app(HeroSlideService::class);
            $seenTokens = [];
            $seenIds = [];

            foreach ($this->input('slides', []) as $i => $row) {
                if (! is_array($row) || ! empty($row['pending_delete'])) {
                    continue;
                }

                $n = $i + 1;
                $id = ! empty($row['id']) ? (int) $row['id'] : null;
                $token = (string) ($row['token'] ?? '');

                if ($id !== null) {
                    if (isset($seenIds[$id])) {
                        $validator->errors()->add("slides.$i.id", "Слайд $n повторяется.");
                    }
                    $seenIds[$id] = true;
                }

                if ($id === null && $token === '') {
                    $validator->errors()->add("slides.$i.token", "Для слайда $n загрузите видео.");
                }

                if ($token !== '') {
                    if (isset($seenTokens[$token])) {
                        $validator->errors()->add("slides.$i.token", "Один и тот же файл выбран для двух слайдов (слайд $n).");
                    }
                    $seenTokens[$token] = true;

                    if (! $service->uploadExists($token)) {
                        $validator->errors()->add("slides.$i.token", "Файл видео для слайда $n устарел или не загружен. Загрузите его снова.");
                    }
                }

                if ($id !== null && $token === '' && ! HeroSlide::query()->whereKey($id)->exists()) {
                    $validator->errors()->add("slides.$i.id", "Слайд $n не найден.");
                }

                foreach (['tab_label' => 'название вкладки', 'title' => 'заголовок'] as $field => $name) {
                    if (trim((string) ($row[$field] ?? '')) === '') {
                        $validator->errors()->add("slides.$i.$field", "Слайд $n: заполните $name.");
                    }
                }

                foreach (['frame_ratio', 'fit_mode', 'media_side', 'media_size'] as $field) {
                    if (($row[$field] ?? '') === '') {
                        $validator->errors()->add("slides.$i.$field", "Слайд $n: выберите значение настройки.");
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'main_slide_label.required' => 'Введите название вкладки для основного слайда.',
            'slides.max' => 'Слишком много слайдов с видео.',
            'slider_autoplay_seconds.in' => 'Выберите автопереключение из списка.',
        ];
    }
}
