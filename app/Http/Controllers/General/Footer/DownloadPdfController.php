<?php

namespace App\Http\Controllers\General\Footer;

use App\Http\Controllers\Controller;
use App\Models\FooterDocument;
use Illuminate\Support\Facades\Storage;

class DownloadPdfController extends Controller
{
    public function __invoke(FooterDocument $footerDocument)
    {
        if (! $footerDocument->is_active || ! $footerDocument->file_path) {
            abort(404);
        }

        if (! Storage::disk('public')->exists($footerDocument->file_path)) {
            abort(404);
        }

        $downloadName = $footerDocument->original_filename
            ?: basename($footerDocument->file_path);

        if (! str_ends_with(strtolower($downloadName), '.pdf')) {
            $downloadName .= '.pdf';
        }

        return Storage::disk('public')->download($footerDocument->file_path, $downloadName, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
