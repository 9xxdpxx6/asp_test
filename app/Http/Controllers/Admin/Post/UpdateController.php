<?php

namespace App\Http\Controllers\Admin\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\UpdateRequest;
use App\Models\Post;
use App\Models\PostImage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class UpdateController extends BaseController
{
    public function __invoke(UpdateRequest $request, Post $post)
    {
        $data = $request->validated();
        $images = isset($data['images']) ? $data['images'] : null;
        $imageIdsForDelete = isset($data['image_ids_for_delete']) ? $data['image_ids_for_delete'] : null;
//        $imageUrlsForDelete = isset($data['image_urls_for_delete']) ? $data['image_urls_for_delete'] : null;

        unset($data['images'], $data['image_ids_for_delete'], $data['image_urls_for_delete']);
        $post->update([
            'title' => $data['title'], // обновляем заголовок
            'preview_path' => 'dsad', // обновляем путь к превью (если требуется)
            'slug' => $this->generateSlug($data['title']), // генерируем новый slug
            'content' => $data['content'], // обновляем содержимое
        ]);


        $currentImages = $post->images;
        if($imageIdsForDelete){
            foreach ($currentImages as $currentImage){
                if(in_array($currentImage->id,$imageIdsForDelete)){
                    Storage::disk('public')->delete($currentImage->path);
                    $currentImage->delete();
                }
            }
        }

//        if($imageUrlsForDelete){
//            foreach ($imageUrlsForDelete as $imageUrlForDelete){
//                $removeStr = $request->root(). '/storage/';
//                $path = str_replace($removeStr,'',$imageUrlForDelete);
//                Storage::disk('public')->delete($path);
//
//            }
//        }

        if ($images) {
            foreach ($images as $image) {
                $name = md5(Carbon::now() . '_' . $image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();
                $filePath = Storage::disk('public')->putFileAs('/images', $image, $name);

                PostImage::create([
                    'post_id' => $post->id,
                    'image_path' => $filePath
                ]);

            }
        }

        return view('post.show', compact('post'));
//        return response()->json(['message' => 'success']);
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
