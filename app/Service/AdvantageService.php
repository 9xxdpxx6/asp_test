<?php

namespace App\Service;

use App\Models\Advantage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdvantageService
{
    public function sync(array $advantages): int
    {
        return DB::transaction(function () use ($advantages) {
            $keepIds = [];
            $activeAdvantages = array_values(array_filter($advantages, function (array $advantage) {
                return empty($advantage['pending_delete']);
            }));

            foreach ($activeAdvantages as $index => $data) {
                $advantage = !empty($data['id']) ? Advantage::find($data['id']) : new Advantage();
                $currentImage = $advantage?->image;

                $imagePath = $currentImage;
                if (!empty($data['image']) && $data['image'] instanceof UploadedFile) {
                    $this->deleteManagedImage($currentImage);
                    $imagePath = $this->uploadImage($data['image']);
                } elseif (!empty($data['existing_image'])) {
                    $imagePath = $data['existing_image'];
                }

                $advantage->fill([
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'image' => $imagePath,
                    'sort_order' => $index + 1,
                    'image_on_left' => array_key_exists('image_on_left', $data)
                        ? filter_var($data['image_on_left'], FILTER_VALIDATE_BOOLEAN)
                        : true,
                ]);
                $advantage->save();

                $keepIds[] = $advantage->id;
            }

            $query = Advantage::query();
            if (!empty($keepIds)) {
                $query->whereNotIn('id', array_unique($keepIds));
            }

            $advantagesToDelete = $query->get();

            foreach ($advantagesToDelete as $advantage) {
                $this->deleteManagedImage($advantage->image);
                $advantage->delete();
            }

            return $advantagesToDelete->count();
        });
    }

    protected function uploadImage(UploadedFile $file): string
    {
        $fileName = 'advantage_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $directory = public_path('storage/images/advantages');

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $file->move($directory, $fileName);

        return 'images/advantages/' . $fileName;
    }

    protected function deleteManagedImage(?string $path): void
    {
        if (empty($path) || Str::startsWith($path, ['http://', 'https://', '/'])) {
            return;
        }

        $fullPath = public_path('storage/' . ltrim($path, '/'));

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
