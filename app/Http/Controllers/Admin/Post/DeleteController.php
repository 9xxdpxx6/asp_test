<?php

namespace App\Http\Controllers\Admin\Post;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeleteController extends BaseController
{
    public function __invoke(Post $post)
    {
        // Прежде чем удалять, эксплуатируйте имеющиеся изображения
        $currentImages = $post->images;

        // Удаляем все связанные изображения
        foreach ($currentImages as $currentImage){
            if(Storage::disk('public')->exists($currentImage->path)) {
                Storage::disk('public')->delete($currentImage->path); // Удаляем файл
            }
            $currentImage->delete(); // Удаляем запись в базе данных
        }

        // Теперь удаляем сам пост
        $post->delete();

        return response()->json(['message' => 'Post and related images deleted successfully']);
    }

}
