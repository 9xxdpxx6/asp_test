<?php

namespace App\Http\Requests\HeroSlide;

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
            'width' => ['required', 'integer', 'between:64,4096'],
            'height' => ['required', 'integer', 'between:64,4096'],
            'duration' => ['required', 'integer', 'between:1,3600'],
        ];
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
        });
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
