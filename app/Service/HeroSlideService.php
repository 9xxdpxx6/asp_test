<?php

namespace App\Service;

use App\Models\HeroSetting;
use App\Models\HeroSlide;
use App\Support\HeroSliderOptions;
use App\Support\ImageNormalizer;
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
            'kind' => 'video',
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

    /**
     * Сохраняет фото (рядом с текстом или фон слайда) во временную папку; слишком большие кадры уменьшаются на сервере.
     *
     * @param  string  $purpose  media | background
     * @return array{token: string, url: string, width: int, height: int, bytes: int}
     */
    public function storeImageUpload(UploadedFile $image, string $purpose): array
    {
        $this->purgeStaleUploads();

        $disk = Storage::disk(self::DISK);
        $token = Str::random(24);
        $dir = self::TMP_DIR . '/' . $token;
        $absDir = $disk->path($dir);
        if (! is_dir($absDir)) {
            mkdir($absDir, 0775, true);
        }

        $maxSide = max(640, (int) config('hero_slider.image_max_side', 2560));
        $tmp = $absDir . '/incoming.tmp';
        $result = ImageNormalizer::normalize($image->getRealPath(), $tmp, $maxSide);
        $file = 'image.' . $result['ext'];
        rename($tmp, $absDir . '/' . $file);

        $bytes = (int) filesize($absDir . '/' . $file);
        $disk->put($dir . '/meta.json', json_encode([
            'kind' => 'image',
            'purpose' => $purpose,
            'width' => $result['width'],
            'height' => $result['height'],
            'bytes' => $bytes,
        ]));

        return [
            'token' => $token,
            'url' => $disk->url($dir . '/' . $file),
            'width' => $result['width'],
            'height' => $result['height'],
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

    public function imageUploadExists(string $token): bool
    {
        return $this->imageFile($token) !== null;
    }

    /**
     * Путь к фото во временной папке (относительно диска public) или null.
     */
    public function imageFile(string $token): ?string
    {
        if (! preg_match('/^[A-Za-z0-9]{24}$/', $token)) {
            return null;
        }

        $disk = Storage::disk(self::DISK);
        $dir = self::TMP_DIR . '/' . $token;
        if (! $disk->exists($dir . '/meta.json')) {
            return null;
        }

        foreach ($disk->files($dir) as $path) {
            if (str_starts_with(basename($path), 'image.')) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Метаданные временной загрузки (width, height, bytes, ...).
     *
     * @return array<string, mixed>
     */
    public function uploadMeta(string $token): array
    {
        $disk = Storage::disk(self::DISK);
        $meta = json_decode((string) $disk->get(self::TMP_DIR . '/' . $token . '/meta.json'), true);

        return is_array($meta) ? $meta : [];
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
                if ($slide === null) {
                    $slide = HeroSlide::query()->create(['tab_label' => '', 'title' => '']);
                }

                $type = array_key_exists($row['media_type'] ?? '', HeroSliderOptions::mediaTypes()) ? $row['media_type'] : 'video';

                $fields = [
                    'sort_order' => $order++,
                    'is_active' => ! empty($row['is_active']),
                    'media_type' => $type,
                    'tab_label' => trim((string) $row['tab_label']),
                    'title' => trim((string) $row['title']),
                    'subtitle' => trim((string) ($row['subtitle'] ?? '')) ?: null,
                    'frame_ratio' => $row['frame_ratio'],
                    'fit_mode' => $row['fit_mode'],
                    'media_size' => $row['media_size'],
                    'text_align' => array_key_exists($row['text_align'] ?? '', HeroSliderOptions::textAligns()) ? $row['text_align'] : 'left',
                    'text_valign' => array_key_exists($row['text_valign'] ?? '', HeroSliderOptions::textValigns()) ? $row['text_valign'] : 'center',
                ];

                $label = trim((string) ($row['button_label'] ?? ''));
                $href = trim((string) ($row['button_href'] ?? ''));
                $fields['button_label'] = ($label !== '' && $href !== '') ? $label : null;
                $fields['button_href'] = ($label !== '' && $href !== '') ? $href : null;

                // Видео
                if ($type === 'video') {
                    if (! empty($row['token'])) {
                        $fields += $this->adoptUpload((string) $row['token'], $slide);
                    }
                } else {
                    $fields += $this->dropVideo($slide);
                }

                // Фото рядом с текстом
                if ($type === 'photo') {
                    if (! empty($row['image_token'])) {
                        $fields += $this->adoptImage((string) $row['image_token'], $slide, 'image');
                    }
                } else {
                    $fields += $this->dropImage($slide, 'image');
                }

                // Фон слайда (необязательный)
                if (! empty($row['bg_token'])) {
                    $fields += $this->adoptImage((string) $row['bg_token'], $slide, 'bg');
                } elseif (! empty($row['bg_remove'])) {
                    $fields += $this->dropImage($slide, 'bg');
                }

                [$width, $height] = $this->mediaDims($type, $fields, $slide);
                $aspect = HeroSliderOptions::naturalAspect($width, $height, $fields['frame_ratio']);
                $side = (string) ($row['media_side'] ?? 'right');
                $fields['media_side'] = $type === 'video'
                    ? HeroSliderOptions::normalizeSide($side, $aspect)
                    : HeroSliderOptions::normalizeSide($side === 'background' ? 'right' : $side, $aspect);

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
     * Размеры основного медиа слайда (видео или фото) с учётом ещё не записанных в модель новых значений.
     *
     * @param  array<string, mixed>  $fields
     * @return array{0: int, 1: int}
     */
    private function mediaDims(string $type, array $fields, HeroSlide $slide): array
    {
        if ($type === 'video') {
            return [(int) ($fields['video_width'] ?? $slide->video_width), (int) ($fields['video_height'] ?? $slide->video_height)];
        }
        if ($type === 'photo') {
            return [(int) ($fields['image_width'] ?? $slide->image_width), (int) ($fields['image_height'] ?? $slide->image_height)];
        }

        return [0, 0];
    }

    /**
     * Переносит файлы видео из временной папки в папку слайда и удаляет прежние.
     *
     * @return array<string, mixed>
     */
    private function adoptUpload(string $token, HeroSlide $slide): array
    {
        $disk = Storage::disk(self::DISK);
        $dir = self::TMP_DIR . '/' . $token;
        $meta = $this->uploadMeta($token);

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

    /**
     * Переносит фото из временной папки. slot: image (рядом с текстом) или bg (фон слайда).
     *
     * @return array<string, mixed>
     */
    private function adoptImage(string $token, HeroSlide $slide, string $slot): array
    {
        $disk = Storage::disk(self::DISK);
        $source = $this->imageFile($token);
        if ($source === null) {
            return [];
        }

        $meta = $this->uploadMeta($token);
        $ext = pathinfo($source, PATHINFO_EXTENSION) ?: 'jpg';
        $target = 'hero-slides/' . $slide->id . '/' . $slot . '-' . Str::lower(Str::random(8)) . '.' . $ext;
        $column = $slot === 'bg' ? 'bg_path' : 'image_path';
        $old = $slide->{$column};

        $disk->move($source, $target);
        $disk->deleteDirectory(self::TMP_DIR . '/' . $token);
        if ($old) {
            $disk->delete($old);
        }

        if ($slot === 'bg') {
            return ['bg_path' => $target];
        }

        return [
            'image_path' => $target,
            'image_width' => (int) ($meta['width'] ?? 0),
            'image_height' => (int) ($meta['height'] ?? 0),
        ];
    }

    /**
     * Убирает видео слайда (когда у слайда теперь другой тип медиа).
     *
     * @return array<string, mixed>
     */
    private function dropVideo(HeroSlide $slide): array
    {
        $old = array_filter([$slide->video_path, $slide->poster_path, $slide->preview_path]);
        if ($old !== []) {
            Storage::disk(self::DISK)->delete(array_values($old));
        }

        return [
            'video_path' => null,
            'poster_path' => null,
            'preview_path' => null,
            'video_width' => 0,
            'video_height' => 0,
            'duration_seconds' => 0,
            'video_bytes' => 0,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function dropImage(HeroSlide $slide, string $slot): array
    {
        $column = $slot === 'bg' ? 'bg_path' : 'image_path';
        if ($slide->{$column}) {
            Storage::disk(self::DISK)->delete($slide->{$column});
        }

        return $slot === 'bg'
            ? ['bg_path' => null]
            : ['image_path' => null, 'image_width' => 0, 'image_height' => 0];
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
                ->map(fn (HeroSlide $s) => $this->publicSlide($s, $disk))
                ->values()
                ->all();
        } catch (QueryException) {
            // таблицы hero_slides ещё нет
        }

        return $payload;
    }

    /**
     * @param  \Illuminate\Contracts\Filesystem\Filesystem  $disk
     * @return array<string, mixed>
     */
    private function publicSlide(HeroSlide $s, $disk): array
    {
        $type = (string) ($s->media_type ?: 'video');
        $hasVideo = $type === 'video' && $s->video_path && $disk->exists($s->video_path);
        $hasPhoto = $type === 'photo' && $s->image_path && $disk->exists($s->image_path);
        $media = $hasVideo ? 'video' : ($hasPhoto ? 'photo' : 'none');

        $width = $hasVideo ? $s->video_width : ($hasPhoto ? $s->image_width : 0);
        $height = $hasVideo ? $s->video_height : ($hasPhoto ? $s->image_height : 0);

        $aspect = HeroSliderOptions::aspect($s->frame_ratio, $width, $height);
        $css = ($s->frame_ratio !== 'auto' && str_contains($s->frame_ratio, ':'))
            ? str_replace(':', ' / ', $s->frame_ratio)
            : (($width > 0 && $height > 0) ? $width . ' / ' . $height : '9 / 16');

        $side = $media === 'video'
            ? HeroSliderOptions::normalizeSide($s->media_side, HeroSliderOptions::naturalAspect($width, $height, $s->frame_ratio))
            : ($s->media_side === 'left' ? 'left' : 'right');

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
            'media' => $media,
            'video_url' => $hasVideo ? $s->video_url : null,
            'poster_url' => $hasVideo ? $s->poster_url : null,
            'preview_url' => $hasVideo ? $s->preview_url : null,
            'image_url' => $hasPhoto ? $s->image_url : null,
            'background_url' => ($s->bg_path && $disk->exists($s->bg_path)) ? $s->background_url : null,
            'duration' => $s->duration_seconds,
            'aspect' => round($aspect, 4),
            'aspect_css' => $css,
            'fit' => $s->fit_mode === 'contain' ? 'contain' : 'cover',
            'side' => $side,
            'size' => in_array($s->media_size, ['s', 'm', 'l'], true) ? $s->media_size : 'm',
            'text_align' => array_key_exists((string) $s->text_align, HeroSliderOptions::textAligns()) ? $s->text_align : 'left',
            'text_valign' => array_key_exists((string) $s->text_valign, HeroSliderOptions::textValigns()) ? $s->text_valign : 'center',
        ];
    }
}
