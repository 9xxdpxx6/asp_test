<?php

namespace App\Service;

use App\Models\ContactBranch;
use App\Models\ContactPageSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContactPageService
{
    public function updateSettings(array $data): void
    {
        ContactPageSetting::query()->updateOrCreate(
            ['id' => 1],
            [
                'page_title' => $data['page_title'],
                'page_subtitle' => $data['page_subtitle'] ?? null,
                'contacts_heading' => $data['contacts_heading'] ?? 'Свяжитесь с нами',
                'contacts_intro' => $data['contacts_intro'] ?? null,
                'phones' => $data['phones'] ?? [],
                'emails' => $data['emails'] ?? [],
            ]
        );
    }

    /**
     * @param  array<int, array<string, mixed>>  $branches
     */
    public function syncBranches(array $branches): void
    {
        DB::transaction(function () use ($branches) {
            $keepIds = [];
            $active = array_values(array_filter($branches, function (array $b) {
                $pd = $b['pending_delete'] ?? false;

                return ! ($pd === true || $pd === 1 || $pd === '1');
            }));

            foreach ($active as $index => $data) {
                $branch = ! empty($data['id']) ? ContactBranch::find($data['id']) : new ContactBranch();

                $photos = $this->mergeBranchPhotos($branch, $data);

                $branch->fill([
                    'title' => $data['title'],
                    'map_embed_html' => isset($data['map_embed_html']) ? trim((string) $data['map_embed_html']) : null,
                    'details_text' => isset($data['details_text']) ? (string) $data['details_text'] : null,
                    'photos' => $photos,
                    'sort_order' => $index + 1,
                ]);
                $branch->save();

                $keepIds[] = $branch->id;
            }

            $query = ContactBranch::query();
            if (! empty($keepIds)) {
                $query->whereNotIn('id', array_unique($keepIds));
            }

            foreach ($query->get() as $orphan) {
                $this->deleteBranchPhotos($orphan->photos ?? []);
                $orphan->delete();
            }
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int, string>
     */
    protected function mergeBranchPhotos(ContactBranch $branch, array $data): array
    {
        $oldPhotos = is_array($branch->photos) ? array_values($branch->photos) : [];
        $existing = $data['existing_photos'] ?? [];
        $uploads = $data['photos'] ?? [];

        $newList = [];
        for ($i = 0; $i < 3; $i++) {
            $upload = $uploads[$i] ?? null;
            $kept = isset($existing[$i]) ? trim((string) $existing[$i]) : '';

            if ($upload instanceof UploadedFile) {
                if (isset($oldPhotos[$i]) && $this->isManagedStoragePath($oldPhotos[$i])) {
                    $this->deleteManagedImage($oldPhotos[$i]);
                }
                $newList[] = $this->uploadImage($upload);

                continue;
            }

            if ($kept !== '') {
                $newList[] = $kept;
            } elseif (isset($oldPhotos[$i]) && $this->isManagedStoragePath($oldPhotos[$i])) {
                $this->deleteManagedImage($oldPhotos[$i]);
            }
        }

        foreach ($oldPhotos as $p) {
            if ($p !== '' && ! in_array($p, $newList, true) && $this->isManagedStoragePath($p)) {
                $this->deleteManagedImage($p);
            }
        }

        return array_values(array_filter($newList, fn ($p) => $p !== ''));
    }

    protected function isManagedStoragePath(string $path): bool
    {
        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return false;
        }

        return str_starts_with($path, 'images/contacts/');
    }

    /**
     * @param  array<int, string|null>  $photos
     */
    protected function deleteBranchPhotos(array $photos): void
    {
        foreach ($photos as $p) {
            $this->deleteManagedImage($p);
        }
    }

    protected function uploadImage(UploadedFile $file): string
    {
        $fileName = 'contact_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $directory = public_path('storage/images/contacts');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $file->move($directory, $fileName);

        return 'images/contacts/' . $fileName;
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
