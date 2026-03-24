<?php

namespace App\Service;

use App\Models\FooterDocument;
use App\Models\FooterSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FooterSettingService
{
    /**
     * @param  array<string, mixed>  $validated
     * @param  array<int, UploadedFile|null>  $uploads  keyed by document index
     */
    public function update(FooterSetting $setting, array $validated, array $uploads, array $removeFileFlags): void
    {
        DB::transaction(function () use ($setting, $validated, $uploads, $removeFileFlags) {
            $setting->update([
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'address' => $validated['address'] ?? null,
                'logo_description' => $validated['logo_description'] ?? null,
                'social_links' => $validated['social_links'] ?? [],
            ]);

            foreach ($validated['documents'] as $index => $row) {
                $doc = FooterDocument::query()
                    ->where('footer_setting_id', $setting->id)
                    ->where('id', (int) $row['id'])
                    ->firstOrFail();

                $isActive = ! empty($row['is_active']);

                if (! empty($removeFileFlags[$index])) {
                    $this->deleteStoredFile($doc);
                }

                $upload = $uploads[$index] ?? null;
                if ($upload instanceof UploadedFile) {
                    $this->deleteStoredFile($doc);
                    $path = $upload->store('footer-pdf', 'public');
                    $doc->file_path = $path;
                    $doc->original_filename = $upload->getClientOriginalName();
                }

                $doc->title = (string) ($row['title'] ?? '');
                $doc->is_active = $isActive;
                $doc->save();
            }
        });
    }

    private function deleteStoredFile(FooterDocument $doc): void
    {
        if ($doc->file_path) {
            Storage::disk('public')->delete($doc->file_path);
            $doc->file_path = null;
            $doc->original_filename = null;
        }
    }
}
