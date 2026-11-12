<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\HeroSlide\UpdateRequest;
use App\Http\Requests\HeroSlide\UploadRequest;
use App\Models\HeroSetting;
use App\Models\HeroSlide;
use App\Service\HeroSlideService;
use App\Support\HeroCtaLinkCatalog;
use App\Support\HeroSliderOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class HeroSlidesController extends Controller
{
    public function __construct(private readonly HeroSlideService $service)
    {
    }

    public function index()
    {
        $hero = HeroSetting::query()->firstOrFail();

        return view('admin.hero-slides', [
            'settings' => [
                'main_slide_label' => old('main_slide_label', $hero->main_slide_label ?: 'Об автошколе'),
                'main_slide_position' => old('main_slide_position', $hero->main_slide_position ?: 'last'),
                'slider_autoplay_seconds' => (int) old('slider_autoplay_seconds', $hero->slider_autoplay_seconds ?? 0),
                'slider_in_hero' => (bool) (old() ? old('slider_in_hero') : ($hero->slider_in_hero ?? true)),
                'slider_show_cards' => (bool) (old() ? old('slider_show_cards') : ($hero->slider_show_cards ?? false)),
                'cards_title' => old('cards_title', $hero->cards_title ?: 'Обучение по категориям'),
                'cards_subtitle' => old('cards_subtitle', $hero->cards_subtitle ?? ''),
            ],
            'slides' => $this->slidesForForm(),
            'linkOptions' => HeroCtaLinkCatalog::options(),
            'ratioOptions' => HeroSliderOptions::ratios(),
            'fitOptions' => HeroSliderOptions::fits(),
            'sideOptions' => HeroSliderOptions::sides(),
            'sizeOptions' => HeroSliderOptions::sizes(),
            'autoplayChoices' => HeroSliderOptions::autoplayChoices(),
            'maxSlides' => (int) config('hero_slider.max_slides', 6),
            'maxVideoBytes' => (int) config('hero_slider.max_video_mb', 40) * 1024 * 1024,
            'backgroundMinRatio' => HeroSliderOptions::BACKGROUND_MIN_RATIO,
        ]);
    }

    public function update(UpdateRequest $request)
    {
        $this->service->sync($request->validated());

        return redirect()
            ->route('admin.hero-slides')
            ->with('success', 'Слайдер на главной обновлён.');
    }

    /**
     * Принимает уже сжатый в браузере ролик, обложку и короткое превью.
     */
    public function upload(UploadRequest $request): JsonResponse
    {
        $result = $this->service->storeUpload(
            $request->file('video'),
            $request->file('poster'),
            $request->file('preview'),
            (int) $request->input('width'),
            (int) $request->input('height'),
            (int) $request->input('duration'),
        );

        return response()->json($result);
    }

    /**
     * Состояние слайдов для формы: из базы, а после ошибки валидации из введённых значений.
     *
     * @return list<array<string, mixed>>
     */
    private function slidesForForm(): array
    {
        $saved = HeroSlide::query()->ordered()->get()->keyBy('id');
        $old = old('slides');
        $disk = Storage::disk('public');

        if (is_array($old)) {
            $rows = [];
            foreach ($old as $row) {
                if (! is_array($row) || ! empty($row['pending_delete'])) {
                    continue;
                }

                $id = ! empty($row['id']) ? (int) $row['id'] : null;
                $db = $id !== null ? $saved->get($id) : null;
                $token = (string) ($row['token'] ?? '');
                $media = $db !== null ? $this->mediaFromSlide($db) : $this->emptyMedia();

                if ($token !== '' && $this->service->uploadExists($token)) {
                    $dir = HeroSlideService::TMP_DIR . '/' . $token;
                    $meta = json_decode((string) $disk->get($dir . '/meta.json'), true) ?: [];
                    $media = [
                        'video_url' => $disk->url($dir . '/video.mp4'),
                        'poster_url' => $disk->url($dir . '/poster.jpg'),
                        'preview_url' => $disk->exists($dir . '/preview.mp4') ? $disk->url($dir . '/preview.mp4') : null,
                        'video_width' => (int) ($meta['width'] ?? 0),
                        'video_height' => (int) ($meta['height'] ?? 0),
                        'duration_seconds' => (int) ($meta['duration'] ?? 0),
                        'video_bytes' => (int) ($meta['bytes'] ?? 0),
                    ];
                } else {
                    $token = '';
                }

                $rows[] = $this->formRow($row, $id, $token, $media);
            }

            return $rows;
        }

        return $saved->map(fn (HeroSlide $s) => $this->formRow([
            'tab_label' => $s->tab_label,
            'title' => $s->title,
            'subtitle' => $s->subtitle,
            'button_label' => $s->button_label,
            'button_href' => $s->button_href,
            'is_active' => $s->is_active,
            'frame_ratio' => $s->frame_ratio,
            'fit_mode' => $s->fit_mode,
            'media_side' => $s->media_side,
            'media_size' => $s->media_size,
        ], $s->id, '', $this->mediaFromSlide($s)))->values()->all();
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array<string, mixed>  $media
     * @return array<string, mixed>
     */
    private function formRow(array $row, ?int $id, string $token, array $media): array
    {
        return [
            'id' => $id,
            'token' => $token,
            'is_active' => ! empty($row['is_active']),
            'tab_label' => (string) ($row['tab_label'] ?? ''),
            'title' => (string) ($row['title'] ?? ''),
            'subtitle' => (string) ($row['subtitle'] ?? ''),
            'button_label' => (string) ($row['button_label'] ?? ''),
            'button_href' => (string) ($row['button_href'] ?? ''),
            'frame_ratio' => (string) ($row['frame_ratio'] ?? 'auto'),
            'fit_mode' => (string) ($row['fit_mode'] ?? 'cover'),
            'media_side' => (string) ($row['media_side'] ?? 'right'),
            'media_size' => (string) ($row['media_size'] ?? 'm'),
        ] + $media;
    }

    /**
     * @return array<string, mixed>
     */
    private function mediaFromSlide(HeroSlide $s): array
    {
        return [
            'video_url' => $s->video_url,
            'poster_url' => $s->poster_url,
            'preview_url' => $s->preview_url,
            'video_width' => $s->video_width,
            'video_height' => $s->video_height,
            'duration_seconds' => $s->duration_seconds,
            'video_bytes' => $s->video_bytes,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyMedia(): array
    {
        return [
            'video_url' => null,
            'poster_url' => null,
            'preview_url' => null,
            'video_width' => 0,
            'video_height' => 0,
            'duration_seconds' => 0,
            'video_bytes' => 0,
        ];
    }
}
