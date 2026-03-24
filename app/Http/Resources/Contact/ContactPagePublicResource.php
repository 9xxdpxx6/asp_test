<?php

namespace App\Http\Resources\Contact;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContactPagePublicResource extends JsonResource
{
    /**
     * @param  \App\Models\ContactPageSetting  $resource
     */
    public function toArray(Request $request): array
    {
        $branches = \App\Models\ContactBranch::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return [
            'page_title' => $this->page_title,
            'page_subtitle' => $this->page_subtitle ?? '',
            'contacts_heading' => $this->contacts_heading ?? 'Свяжитесь с нами',
            'contacts_intro' => $this->contacts_intro ?? '',
            'phones' => $this->phones ?? [],
            'emails' => $this->emails ?? [],
            'branches' => $branches->map(function ($b) {
                $photos = collect($b->photos ?? [])
                    ->filter()
                    ->map(fn ($p) => $this->photoPublicUrl((string) $p))
                    ->values()
                    ->all();

                return [
                    'id' => $b->id,
                    'title' => $b->title,
                    'map_embed_html' => $b->map_embed_html ?? '',
                    'details_text' => $b->details_text ?? '',
                    'photos' => $photos,
                ];
            })->all(),
        ];
    }

    protected function photoPublicUrl(string $path): string
    {
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, '/')) {
            return url($path);
        }

        return Storage::disk('public')->url($path);
    }
}
