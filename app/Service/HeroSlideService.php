<?php

namespace App\Service;

use App\Models\HeroSetting;
use App\Models\HeroSlide;
use App\Support\HeroSliderOptions;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HeroSlideService
{
    public const TMP_DIR = 'hero-slides/tmp';

    private const DISK = 'public';

    /**
     * Сохраняет сжатый в браузере ролик, обложку и короткое превью во временную папку.
     * В слайд файлы переезжают только когда админ нажмёт «Сохранить».
     *
     * @return array{token: string, video_url: string, poster_url: string, preview_url: ?string, bytes: int}
     */
    public function storeUpload(
        UploadedFile $video,
        UploadedFile $poster,
        ?UploadedFile $preview,
        int $width,
        int $height,
        int $duration
    ): array {
        $this->purgeStaleUploads();

        $disk = Storage::disk(self::DISK);
        $token = Str::random(24);
        $dir = self::TMP_DIR . '/' . $token;

        $disk->putFileAs($dir, $video, 'video.mp4');
        $disk->putFileAs($dir, $poster, 'poster.jpg');
        if ($preview !== null) {
            $disk->putFileAs($dir, $preview, 'preview.mp4');
        }

        $bytes = (int) $video->getSize();
        $disk->put($dir . '/meta.json', json_encode([
            'width' => $width,
            'height' => $height,
            'duration' => $duration,
            'bytes' => $bytes,
        ]));

        return [
            'token' => $token,
            'video_url' => $disk->url($dir . '/video.mp4'),
            'poster_url' => $disk->url($dir . '/poster.jpg'),
            'preview_url' => $preview !== null ? $disk->url($dir . '/preview.mp4') : null,
            'bytes' => $bytes,
        ];
    }

    public function uploadExists(string $token): bool
    {
        if (! preg_match('/^[A-Za-z0-9]{24}$/', $token)) {
            return false;
        }

        $disk = Storage::disk(self::DISK);
        $dir = self::TMP_DIR . '/' . $token;

        return $disk->exists($dir . '/video.mp4')
            && $disk->exists($dir . '/poster.jpg')
            && $disk->exists($dir . '/meta.json');
    }

    /**
     * Удаляет загруженные, но так и не сохранённые в слайд файлы старше TTL.
     */
    public function purgeStaleUploads(): void
    {
        $disk = Storage::disk(self::DISK);
        $limit = now()->subHours(max(1, (int) config('hero_slider.tmp_ttl_hours', 24)))->getTimestamp();

        $alive = [];
        foreach ($disk->directories(self::TMP_DIR) as $dir) {
            $meta = $dir . '/meta.json';
            $stamp = $disk->exists($meta) ? $disk->lastModified($meta) : 0;
            if ($stamp < $limit) {
                $disk->deleteDirectory($dir);
            } else {
                $alive[$dir] = $stamp;
            }
        }

        // Ограничиваем и количество: при превышении удаляем самые старые (место на диске не безгранично)
        $keep = max(1, (int) config('hero_slider.max_tmp_uploads', 12)) - 1;
        asort($alive);
        foreach (array_slice(array_keys($alive), 0, max(0, count($alive) - $keep)) as $dir) {
            $disk->deleteDirectory($dir);
        }
    }

    /**
     * @param  array<string, mixed>  $data  валидированные данные формы
     */
    public function sync(array $data): void
    {
        DB::transaction(function () use ($data) {
            $hero = HeroSetting::query()->firstOrFail();
            $hero->update([
                'main_slide_label' => trim((string) $data['main_slide_label']),
                'main_slide_position' => $data['main_slide_position'],
                'slider_autoplay_seconds' => (int) $data['slider_autoplay_seconds'],
                'slider_in_hero' => ! empty($data['slider_in_hero']),
                'slider_show_cards' => ! empty($data['slider_show_cards']),
                'cards_title' => trim((string) ($data['cards_title'] ?? '')) ?: 'Обучение по категориям',
                'cards_subtitle' => trim((string) ($data['cards_subtitle'] ?? '')) ?: null,
            ]);

            $keepIds = [];
            $order = 0;

            foreach (($data['slides'] ?? []) as $row) {
                if (! empty($row['pending_delete'])) {
                    continue;
                }

                $slide = ! empty($row['id']) ? HeroSlide::query()->find((int) $row['id']) : null;
                $isNew = $slide === null;
                if ($isNew) {
                    $slide = HeroSlide::query()->create(['tab_label' => '', 'title' => '', 'video_path' => '']);
                }

                $fields = [
                    'sort_order' => $order++,
                    'is_active' => ! empty($row['is_active']),
                    'tab_label' => trim((string) $row['tab_label']),
                    'title' => trim((string) $row['title']),
                    'subtitle' => trim((string) ($row['subtitle'] ?? '')) ?: null,
                    'frame_ratio' => $row['frame_ratio'],
                    'fit_mode' => $row['fit_mode'],
                    'media_size' => $row['media_size'],
                ];

                $label = trim((string) ($row['button_label'] ?? ''));
                $href = trim((string) ($row['button_href'] ?? ''));
                $fields['button_label'] = ($label !== '' && $href !== '') ? $label : null;
                $fields['button_href'] = ($label !== '' && $href !== '') ? $href : null;

                if (! empty($row['token'])) {
                    $fields += $this->adoptUpload((string) $row['token'], $slide);
                }

                $width = (int) ($fields['video_width'] ?? $slide->video_width);
                $height = (int) ($fields['video_height'] ?? $slide->video_height);
                $aspect = HeroSliderOptions::naturalAspect($width, $height, $fields['frame_ratio']);
                $fields['media_side'] = HeroSliderOptions::normalizeSide((string) ($row['media_side'] ?? 'right'), $aspect);

                $slide->update($fields);
                $keepIds[] = $slide->id;
            }

            HeroSlide::query()
                ->when($keepIds !== [], fn ($q) => $q->whereNotIn('id', $keepIds))
                ->get()
                ->each(function (HeroSlide $slide) {
                    $this->deleteFiles($slide);
                    $slide->delete();
                });
        });
    }

    /**
     * Переносит файлы из временной папки в папку слайда и удаляет прежние.
     *
     * @return array<string, mixed>
     */
    private function adoptUpload(string $token, HeroSlide $slide): array
    {
        $disk = Storage::disk(self::DISK);
        $dir = self::TMP_DIR . '/' . $token;
        $meta = json_decode((string) $disk->get($dir . '/meta.json'), true) ?: [];

        $suffix = Str::lower(Str::random(8));
        $base = 'hero-slides/' . $slide->id;
        $old = array_filter([$slide->video_path, $slide->poster_path, $slide->preview_path]);

        $paths = [
            'video_path' => $base . '/video-' . $suffix . '.mp4',
            'poster_path' => $base . '/poster-' . $suffix . '.jpg',
            'preview_path' => null,
        ];

        $disk->move($dir . '/video.mp4', $paths['video_path']);
        $disk->move($dir . '/poster.jpg', $paths['poster_path']);
        if ($disk->exists($dir . '/preview.mp4')) {
            $paths['preview_path'] = $base . '/preview-' . $suffix . '.mp4';
            $disk->move($dir . '/preview.mp4', $paths['preview_path']);
        }
        $disk->deleteDirectory($dir);

        if ($old !== []) {
            $disk->delete(array_values($old));
        }

        return $paths + [
            'video_width' => (int) ($meta['width'] ?? 0),
            'video_height' => (int) ($meta['height'] ?? 0),
            'duration_seconds' => (int) ($meta['duration'] ?? 0),
            'video_bytes' => (int) ($meta['bytes'] ?? 0),
        ];
    }

    private function deleteFiles(HeroSlide $slide): void
    {
        Storage::disk(self::DISK)->deleteDirectory('hero-slides/' . $slide->id);
    }

    /**
     * Данные слайдера для публичной главной. Если таблицы ещё нет (миграция не накатана), слайдер просто выключен.
     *
     * @return array<string, mixed>
     */
    public function publicPayload(HeroSetting $hero): array
    {
        $payload = [
            'autoplay_seconds' => (int) ($hero->slider_autoplay_seconds ?? 0),
            'main_position' => ($hero->main_slide_position ?? 'last') === 'first' ? 'first' : 'last',
            'main_label' => (string) ($hero->main_slide_label ?: 'Об автошколе'),
            'in_hero' => (bool) ($hero->slider_in_hero ?? true),
            'show_cards' => (bool) ($hero->slider_show_cards ?? false),
            'cards_title' => (string) ($hero->cards_title ?: 'Обучение по категориям'),
            'cards_subtitle' => (string) ($hero->cards_subtitle ?? ''),
            'slides' => [],
        ];

        try {
            $disk = Storage::disk(self::DISK);
            $payload['slides'] = HeroSlide::query()->active()->ordered()->get()
                ->filter(fn (HeroSlide $s) => $s->video_path && $disk->exists($s->video_path))
                ->map(fn (HeroSlide $s) => $this->publicSlide($s))
                ->values()
                ->all();
        } catch (QueryException) {
            // таблицы hero_slides ещё нет
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    private function publicSlide(HeroSlide $s): array
    {
        $aspect = HeroSliderOptions::aspect($s->frame_ratio, $s->video_width, $s->video_height);
        $css = ($s->frame_ratio !== 'auto' && str_contains($s->frame_ratio, ':'))
            ? str_replace(':', ' / ', $s->frame_ratio)
            : (($s->video_width > 0 && $s->video_height > 0) ? $s->video_width . ' / ' . $s->video_height : '9 / 16');

        $hasButton = filled($s->button_label) && filled($s->button_href);

        return [
            'id' => $s->id,
            'label' => $s->tab_label,
            'title' => $s->title,
            'subtitle' => $s->subtitle ?? '',
            'button' => $hasButton ? [
                'label' => $s->button_label,
                'href' => $s->button_href,
                'icon' => 'fas fa-arrow-right',
            ] : null,
            'video_url' => $s->video_url,
            'poster_url' => $s->poster_url,
            'preview_url' => $s->preview_url,
            'duration' => $s->duration_seconds,
            'aspect' => round($aspect, 4),
            'aspect_css' => $css,
            'fit' => $s->fit_mode === 'contain' ? 'contain' : 'cover',
            'side' => HeroSliderOptions::normalizeSide($s->media_side, HeroSliderOptions::naturalAspect($s->video_width, $s->video_height, $s->frame_ratio)),
            'size' => in_array($s->media_size, ['s', 'm', 'l'], true) ? $s->media_size : 'm',
        ];
    }
}
