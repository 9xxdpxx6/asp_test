<?php

namespace App\Http\Resources\Footer;

use App\Support\FooterSocialCatalog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FooterPublicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $social = FooterSocialCatalog::normalizeSocialForPublic($this->social_links ?? []);

        $documents = $this->documents
            ->filter(fn ($d) => $d->is_active && $d->file_path)
            ->sortBy('sort_order')
            ->values()
            ->map(fn ($d) => [
                'id' => $d->id,
                'title' => $d->title ?: 'Документ',
                'download_url' => url('/api/guest/footer/pdf/'.$d->id),
            ])
            ->all();

        return [
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address ?? '',
            'logo_description' => $this->logo_description ?? '',
            'social' => $social,
            'documents' => $documents,
        ];
    }
}
