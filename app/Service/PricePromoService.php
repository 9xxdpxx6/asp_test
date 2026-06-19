<?php

namespace App\Service;

use App\Models\PricePromo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PricePromoService
{
    private const ALLOWED_SIZES = ['3/4', '2/3', '1/2', '1/3', '1/4'];
    private const ALLOWED_PLACEMENTS = ['top', 'bottom', 'left', 'right'];

    /**
     * @param  array<int, array<string, mixed>>  $promos
     */
    public function syncPromos(array $promos): void
    {
        DB::transaction(function () use ($promos) {
            $keepIds = [];
            $active = array_values(array_filter($promos, function (array $promo) {
                return empty($promo['pending_delete']);
            }));

            foreach ($active as $index => $data) {
                $promo = !empty($data['id']) ? PricePromo::find($data['id']) : new PricePromo();
                $currentImage = $promo?->image;

                $imagePath = $currentImage;
                if (!empty($data['image']) && $data['image'] instanceof UploadedFile) {
                    $this->deleteManagedImage($currentImage);
                    $imagePath = $this->uploadImage($data['image']);
                } elseif (!empty($data['existing_image'])) {
                    $imagePath = $data['existing_image'];
                }

                $title = isset($data['title']) ? trim((string) $data['title']) : '';
                $description = isset($data['description']) ? trim((string) $data['description']) : '';

                $size = $data['image_size'] ?? '1/2';
                if (!in_array($size, self::ALLOWED_SIZES, true)) {
                    $size = '1/2';
                }

                $placement = $data['placement'] ?? 'top';
                if (!in_array($placement, self::ALLOWED_PLACEMENTS, true)) {
                    $placement = 'top';
                }

                $promo->fill([
                    'title' => $title === '' ? null : $title,
                    'description' => $description === '' ? null : $description,
                    'image' => $imagePath,
                    'image_size' => $size,
                    'image_on_left' => array_key_exists('image_on_left', $data)
                        ? filter_var($data['image_on_left'], FILTER_VALIDATE_BOOLEAN)
                        : true,
                    'placement' => $placement,
                    'is_active' => array_key_exists('is_active', $data)
                        ? filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN)
                        : true,
                    'sort_order' => $index + 1,
                ]);
                $promo->save();

                $keepIds[] = $promo->id;
            }

            $query = PricePromo::query();
            if (!empty($keepIds)) {
                $query->whereNotIn('id', array_unique($keepIds));
            }

            foreach ($query->get() as $orphan) {
                $this->deleteManagedImage($orphan->image);
                $orphan->delete();
            }
        });
    }

    protected function uploadImage(UploadedFile $file): string
    {
        $fileName = 'promo_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();

        return $file->storeAs('images/promos', $fileName, 'public');
    }

    protected function deleteManagedImage(?string $path): void
    {
        if (empty($path) || Str::startsWith($path, ['http://', 'https://', '/'])) {
            return;
        }

        Storage::disk('public')->delete(ltrim($path, '/'));
    }
}
