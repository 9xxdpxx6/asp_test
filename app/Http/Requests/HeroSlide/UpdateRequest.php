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
            'slides.*.image_token' => ['nullable', 'regex:/^[A-Za-z0-9]{24}$/'],
            'slides.*.bg_token' => ['nullable', 'regex:/^[A-Za-z0-9]{24}$/'],
            'slides.*.bg_remove' => ['nullable', 'boolean'],
            'slides.*.media_type' => ['nullable', Rule::in(array_keys(HeroSliderOptions::mediaTypes()))],
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
                $type = (string) ($row['media_type'] ?? 'video');
                $existing = $id !== null ? HeroSlide::query()->find($id) : null;

                if ($id !== null) {
                    if (isset($seenIds[$id])) {
                        $validator->errors()->add("slides.$i.id", "Слайд $n повторяется.");
                    }
                    $seenIds[$id] = true;

                    if ($existing === null) {
                        $validator->errors()->add("slides.$i.id", "Слайд $n не найден.");
                    }
                }

                // Видео
                $token = (string) ($row['token'] ?? '');
                if ($type === 'video') {
                    if ($token === '' && ($existing === null || ! $existing->video_path)) {
                        $validator->errors()->add("slides.$i.token", "Слайд $n: загрузите видео или выберите другой вариант («Фото» или «Ничего»).");
                    }
                    if ($token !== '') {
                        $this->checkToken($validator, $service, $seenTokens, "slides.$i.token", $token, 'video', $n);
                    }
                }

                // Фото рядом с текстом
                $imageToken = (string) ($row['image_token'] ?? '');
                if ($type === 'photo') {
                    if ($imageToken === '' && ($existing === null || ! $existing->image_path)) {
                        $validator->errors()->add("slides.$i.image_token", "Слайд $n: загрузите фото или выберите другой вариант («Видео» или «Ничего»).");
                    }
                    if ($imageToken !== '') {
                        $this->checkToken($validator, $service, $seenTokens, "slides.$i.image_token", $imageToken, 'image', $n);
                    }
                }

                // Фон слайда (необязательный)
                $bgToken = (string) ($row['bg_token'] ?? '');
                if ($bgToken !== '') {
                    $this->checkToken($validator, $service, $seenTokens, "slides.$i.bg_token", $bgToken, 'image', $n);
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

    /**
     * @param  array<string, bool>  $seenTokens
     */
    private function checkToken(Validator $validator, HeroSlideService $service, array &$seenTokens, string $field, string $token, string $kind, int $n): void
    {
        if (isset($seenTokens[$token])) {
            $validator->errors()->add($field, "Один и тот же файл выбран дважды (слайд $n).");

            return;
        }
        $seenTokens[$token] = true;

        $exists = $kind === 'video' ? $service->uploadExists($token) : $service->imageUploadExists($token);
        if (! $exists) {
            $validator->errors()->add($field, "Загруженный файл для слайда $n устарел или не найден. Загрузите его снова.");
        }
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
