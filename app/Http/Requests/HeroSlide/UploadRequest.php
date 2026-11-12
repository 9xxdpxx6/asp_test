<?php

namespace App\Http\Requests\HeroSlide;

use App\Support\Mp4Info;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Validator;

class UploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxVideoKb = max(1, (int) config('hero_slider.max_video_mb', 40)) * 1024;
        $maxPreviewKb = max(1, (int) config('hero_slider.max_preview_mb', 6)) * 1024;
        $maxPosterKb = max(1, (int) config('hero_slider.max_poster_kb', 2048));

        return [
            'video' => ['required', 'file', 'mimetypes:video/mp4', 'max:' . $maxVideoKb],
            'poster' => ['required', 'file', 'mimetypes:image/jpeg', 'max:' . $maxPosterKb],
            'preview' => ['nullable', 'file', 'mimetypes:video/mp4', 'max:' . $maxPreviewKb],
            // Размеры и длительность присылает браузер, но сервер им не верит: берёт из самого файла (см. videoMeta()).
            'width' => ['nullable', 'integer'],
            'height' => ['nullable', 'integer'],
            'duration' => ['nullable', 'integer'],
        ];
    }

    /** @var array{duration: float, width: int, height: int, has_audio: bool, faststart: bool}|null */
    private ?array $videoMeta = null;

    /**
     * Размер кадра, длительность и прочее, прочитанные из загруженного MP4 (после валидации).
     *
     * @return array{duration: float, width: int, height: int, has_audio: bool, faststart: bool}
     */
    public function videoMeta(): array
    {
        return $this->videoMeta ?? ['duration' => 0.0, 'width' => 0, 'height' => 0, 'has_audio' => false, 'faststart' => false];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            foreach (['video', 'preview'] as $field) {
                $file = $this->file($field);
                if ($file instanceof UploadedFile && ! $this->looksLikeMp4($file)) {
                    $validator->errors()->add($field, 'Файл не похож на MP4-видео.');
                }
            }

            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $this->checkVideo($validator);
            $this->checkPreview($validator);
            $this->checkPoster($validator);
        });
    }

    private function checkVideo(Validator $validator): void
    {
        $file = $this->file('video');
        $info = $file instanceof UploadedFile ? Mp4Info::read($file->getRealPath()) : null;
        if ($info === null) {
            $validator->errors()->add('video', 'Не удалось прочитать видео: файл повреждён или это не MP4 с видеодорожкой.');

            return;
        }

        $maxDim = (int) config('hero_slider.max_dimension', 1920);
        $maxDuration = (int) config('hero_slider.max_duration_seconds', 900);
        $maxKbps = (int) config('hero_slider.max_bitrate_kbps', 4500);
        $duration = (float) $info['duration'];

        if ($duration < 0.5) {
            $validator->errors()->add('video', 'В видео не определилась длительность.');

            return;
        }
        if ($duration > $maxDuration) {
            $validator->errors()->add('video', 'Ролик длиннее ' . intdiv($maxDuration, 60) . ' минут: сократите его.');
        }
        if (max($info['width'], $info['height']) > $maxDim) {
            $validator->errors()->add('video', "Кадр слишком большой ({$info['width']}×{$info['height']}), допустимо не больше {$maxDim} px по длинной стороне. Видео должно сжиматься в браузере перед загрузкой.");
        }

        $kbps = (int) round(((int) $file->getSize()) * 8 / $duration / 1000);
        if ($kbps > $maxKbps) {
            $validator->errors()->add('video', "Видео слишком тяжёлое для своей длины ({$kbps} кбит/с, допустимо до {$maxKbps}). Оно должно сжиматься в браузере перед загрузкой: откройте админку в Chrome или Edge.");
        }

        $this->videoMeta = [
            'duration' => $duration,
            'width' => $info['width'],
            'height' => $info['height'],
            'has_audio' => $info['has_audio'],
            'faststart' => $info['faststart'],
        ];
    }

    private function checkPreview(Validator $validator): void
    {
        $file = $this->file('preview');
        if (! $file instanceof UploadedFile) {
            return;
        }

        $info = Mp4Info::read($file->getRealPath());
        $maxDim = (int) config('hero_slider.max_preview_dimension', 960);
        $maxSeconds = (int) config('hero_slider.max_preview_seconds', 20);

        if ($info === null || max($info['width'], $info['height']) > $maxDim || $info['duration'] > $maxSeconds) {
            $validator->errors()->add('preview', 'Превью должно быть коротким (до ' . $maxSeconds . ' с) и небольшим (до ' . $maxDim . ' px).');
        }
    }

    private function checkPoster(Validator $validator): void
    {
        $file = $this->file('poster');
        $size = $file instanceof UploadedFile ? @getimagesize($file->getRealPath()) : false;
        $maxDim = (int) config('hero_slider.max_poster_dimension', 2000);

        if ($size === false || ($size['mime'] ?? '') !== 'image/jpeg') {
            $validator->errors()->add('poster', 'Обложка должна быть корректным JPEG.');
        } elseif (max($size[0], $size[1]) > $maxDim) {
            $validator->errors()->add('poster', 'Обложка слишком большая (допустимо до ' . $maxDim . ' px).');
        }
    }

    public function messages(): array
    {
        $maxMb = (int) config('hero_slider.max_video_mb', 40);

        return [
            'video.required' => 'Не получен файл видео.',
            'video.mimetypes' => 'Видео должно быть в формате MP4.',
            'video.max' => 'Ролик больше ' . $maxMb . ' МБ даже после сжатия. Сократите его по времени.',
            'video.uploaded' => 'Видео не дошло до сервера: на сервере ограничен размер загрузки (upload_max_filesize / post_max_size).',
            'poster.required' => 'Не получена обложка видео.',
            'poster.mimetypes' => 'Обложка должна быть в формате JPEG.',
            'preview.mimetypes' => 'Превью должно быть в формате MP4.',
        ];
    }

    private function looksLikeMp4(UploadedFile $file): bool
    {
        $handle = @fopen($file->getRealPath(), 'rb');
        if ($handle === false) {
            return false;
        }
        $head = (string) fread($handle, 12);
        fclose($handle);

        // Контейнер MP4 начинается с блока «ftyp» (смещение 4)
        return strlen($head) >= 8 && substr($head, 4, 4) === 'ftyp';
    }
}
