<?php

namespace App\Http\Requests\HeroSlide;

use App\Support\ImageNormalizer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Validator;

class UploadImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxKb = max(1, (int) config('hero_slider.max_image_mb', 6)) * 1024;
        $mimes = implode(',', array_keys(ImageNormalizer::supportedTypes()));

        return [
            'image' => ['required', 'file', 'mimetypes:' . $mimes, 'max:' . $maxKb],
            'purpose' => ['required', 'in:media,background'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $file = $this->file('image');
            $info = $file instanceof UploadedFile ? ImageNormalizer::inspect($file->getRealPath()) : null;

            if ($info === null || ! isset(ImageNormalizer::supportedTypes()[$info['mime']])) {
                $validator->errors()->add('image', 'Не удалось прочитать фото: файл повреждён или это не JPEG, PNG и не WebP.');
            } elseif ($info['width'] * $info['height'] > ImageNormalizer::MAX_PIXELS) {
                $validator->errors()->add('image', 'Фото слишком большое по размерам. Уменьшите его перед загрузкой (в админке это делается автоматически).');
            }
        });
    }

    public function messages(): array
    {
        $maxMb = (int) config('hero_slider.max_image_mb', 6);

        return [
            'image.required' => 'Не получен файл фото.',
            'image.mimetypes' => 'Фото должно быть в формате JPEG, PNG или WebP.',
            'image.max' => 'Фото больше ' . $maxMb . ' МБ. В админке оно сжимается само: если видите это сообщение, уменьшите фото вручную.',
            'image.uploaded' => 'Фото не дошло до сервера: на сервере ограничен размер загрузки (upload_max_filesize / post_max_size).',
        ];
    }
}
