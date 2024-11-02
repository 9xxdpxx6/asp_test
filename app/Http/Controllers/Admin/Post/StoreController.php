<?php

namespace App\Http\Controllers\Admin\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StoreRequest;
use App\Models\Post;
use App\Models\PostImage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class StoreController extends BaseController
{
    public function __invoke(StoreRequest $request)
    {
        $data = $request->validated();

        // Retrieve and save the post content (HTML) directly
        $post = Post::create([
            'title' => $data['title'], // передаем заголовок
            'preview_path' => 'dsad', // передаем путь к превью (если есть)
            'slug' => $this->generateSlug($data['title']),
            'content' => $data['content'], // передаем содержимое
        ]);

        // Extract images from the content and save them
        $this->saveImagesFromContent($data['content'], $post->id);

        return view('post.show', compact('post'));
    }

    private function saveImagesFromContent($content, $postId)
    {
        // Load the DOM Document to parse the HTML
        $doc = new \DOMDocument();
        @$doc->loadHTML($content); // Suppress warnings for invalid HTML
        $images = $doc->getElementsByTagName('img');

        foreach ($images as $image) {
            $src = $image->getAttribute('src');

            // If the image src is a base64 string, process it
            if (strpos($src, 'data:image/') === 0) {
                $imageData = explode(',', $src)[1];
                $imageData = base64_decode($imageData);

                // Generate a unique name for the image
                $imageName = md5(Carbon::now() . '_' . uniqid()) . '.png'; // Change extension as needed
                $filePath = Storage::disk('public')->put('/images/posts', $imageData);

                // Save image path in PostImage model
                PostImage::create([
                    'post_id' => $postId,
                    'image_path' => $filePath
                ]);

                // Update the content with the new image path
                $newSrc = asset('storage/' . $filePath); // Get the full URL
                $image->setAttribute('src', $newSrc);
            }
        }

        // Save updated content with image URLs in the post
        $post = Post::find($postId);
        $post->content = $doc->saveHTML();
        $post->save();
    }

    private function generateSlug($title)
    {
        // Удаляем лишние символы и пробелы
        $title = preg_replace('/[^A-Za-z0-9а-яА-ЯёЁ\s-]/u', '', $title);
        $title = trim($title);
        $title = preg_replace('/[\s-]+/', '-', $title);

        return $this->transliterate($title);
    }

    function transliterate($text) {
        $transliterationTable = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'ts',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            // Заглавные буквы
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'Yo', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'Kh', 'Ц' => 'Ts',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Shch', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        ];

        return strtr($text, $transliterationTable);
    }
}
