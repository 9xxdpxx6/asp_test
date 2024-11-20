<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Faker\Factory as Faker;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Генерация 5 постов
        foreach (range(40, 45) as $index) {
            // Генерация случайного контента с картинкой
            $content = $this->generateFormattedContent();

            // Вставляем пост в таблицу (без preview_path)
            $post = Post::create([
                'title' => $faker->sentence,
                'slug' => $faker->slug,
                'content' => $content, // Форматированный контент
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Генерация случайного изображения
            $imagePath = $this->generateRandomImage();

            // Обновляем только поле preview_path
            $post->update([
                'preview_path' => $imagePath, // Путь к изображению
            ]);
        }
    }

    /**
     * Генерация случайного изображения
     *
     * @return string
     */
    private function generateRandomImage()
    {
        $width = 600;
        $height = 300;
        $image = imagecreatetruecolor($width, $height);
        $backgroundColor = imagecolorallocate($image, 255, 255, 255); // Белый фон
        imagefill($image, 0, 0, $backgroundColor);

        // Цвет текста
        $textColor = imagecolorallocate($image, 0, 0, 0); // Черный

        // Добавляем текст на картинку
        $text = 'Random Image';
        $font = public_path('fonts/Cygre.ttf'); // Укажите путь к шрифту
        $fontSize = 24;

        // Позиционирование текста
        $bbox = imagettfbbox($fontSize, 0, $font, $text);
        $textWidth = $bbox[2] - $bbox[0];
        $textHeight = $bbox[1] - $bbox[7];
        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2 + $textHeight;

        // Рисуем текст на изображении
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $font, $text);

        // Генерация пути для сохранения изображения
        $imagePath = 'post/images/' . uniqid() . '.png';

        // Сохраняем изображение в хранилище
        Storage::disk('public')->put($imagePath, imagepng($image));

        // Очищаем ресурсы
        imagedestroy($image);

        return $imagePath;
    }

    /**
     * Генерация форматированного контента с изображениями
     *
     * @return string
     */
    private function generateFormattedContent()
    {
        return "<p><strong>Что мы обучаем:</strong></p>
                <p>Этот курс включает создание веб-приложений с использованием современных технологий.</p>
                <img src='" . asset('storage/post/images/' . uniqid() . '.png') . "' alt='Random Image' />
                <p><strong>Кому подходит:</strong> Подходит для всех, кто хочет изучить веб-разработку.</p>";
    }
}
