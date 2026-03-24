<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterDocument;
use App\Models\FooterSetting;
use App\Service\FooterSettingService;
use App\Support\FooterSocialCatalog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FooterSettingsController extends Controller
{
    public function __construct(private readonly FooterSettingService $service)
    {
    }

    public function index()
    {
        $setting = FooterSetting::query()->with('documents')->firstOrFail();

        $socialRows = old('social_links', $setting->social_links ?? []);
        if (! is_array($socialRows)) {
            $socialRows = [];
        }
        $socialRows = $this->normalizeSocialFormRows($socialRows);

        return view('admin.footer-settings', [
            'footer' => $setting,
            'documents' => $setting->documents->sortBy('sort_order')->values(),
            'socialRows' => $socialRows,
        ]);
    }

    /**
     * @param  array<int, mixed>  $rows
     * @return array<int, array{code: string, url: string}>
     */
    private function normalizeSocialFormRows(array $rows): array
    {
        $rows = array_values(array_filter($rows, fn ($r) => is_array($r)));
        $byCode = collect($rows)->keyBy(fn (array $r) => (string) ($r['code'] ?? ''));
        $defs = FooterSocialCatalog::definitions();

        $out = [];
        foreach (FooterSocialCatalog::formRowOrder() as $code) {
            $url = '';
            if ($byCode->has($code)) {
                $url = (string) ($byCode->get($code)['url'] ?? '');
            }
            $out[] = [
                'code' => $code,
                'url' => $url,
                'label' => $defs[$code]['label'] ?? $code,
            ];
        }

        return $out;
    }

    public function update(Request $request)
    {
        $allowedCodes = FooterSocialCatalog::allowedCodes();

        $validated = $request->validate([
            'phone' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'address' => 'nullable|string|max:2000',
            'logo_description' => 'nullable|string|max:5000',
            'social_links' => 'required|array|size:8',
            'social_links.*.code' => ['required', 'string', Rule::in($allowedCodes)],
            'social_links.*.url' => 'nullable|string|max:2048',
            'documents.0.id' => 'required|integer|exists:footer_documents,id',
            'documents.1.id' => 'required|integer|exists:footer_documents,id',
            'documents.2.id' => 'required|integer|exists:footer_documents,id',
            'documents.0.title' => 'nullable|string|max:500',
            'documents.1.title' => 'nullable|string|max:500',
            'documents.2.title' => 'nullable|string|max:500',
            'documents.0.file' => 'nullable|file|mimes:pdf|max:10240',
            'documents.1.file' => 'nullable|file|mimes:pdf|max:10240',
            'documents.2.file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $setting = FooterSetting::query()->with('documents')->firstOrFail();

        $documentsPayload = [];
        foreach ([0, 1, 2] as $i) {
            $id = (int) $request->input("documents.$i.id");
            $documentsPayload[] = [
                'id' => $id,
                'title' => (string) $request->input("documents.$i.title", ''),
                'is_active' => $request->boolean("documents.$i.is_active"),
                'remove_file' => $request->boolean("documents.$i.remove_file"),
            ];
        }

        $validated['documents'] = $documentsPayload;

        $socialLinks = collect($validated['social_links'] ?? [])
            ->map(fn (array $row): array => [
                'code' => $row['code'],
                'url' => trim((string) ($row['url'] ?? '')),
            ])
            ->filter(fn (array $row) => $row['url'] !== '')
            ->values()
            ->all();

        $validated['social_links'] = $socialLinks;

        foreach ($validated['documents'] as $i => $row) {
            $doc = FooterDocument::query()->find($row['id']);
            if (! $doc || $doc->footer_setting_id !== $setting->id) {
                abort(403);
            }

            if ($row['is_active']) {
                $willHaveFile = $doc->file_path && ! $row['remove_file'];
                $willHaveFile = $willHaveFile || $request->hasFile("documents.$i.file");
                if (! $willHaveFile) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->withErrors(["documents.$i.file" => 'Для активной ссылки загрузите PDF или отключите «Показывать».']);
                }
            }
        }

        $activeCount = collect($validated['documents'])->filter(fn ($r) => $r['is_active'])->count();
        if ($activeCount > 3) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['documents' => 'Не более трёх активных документов в подвале.']);
        }

        $uploads = [];
        $removeFlags = [];
        foreach ([0, 1, 2] as $i) {
            $uploads[$i] = $request->file("documents.$i.file");
            $removeFlags[$i] = $validated['documents'][$i]['remove_file'];
        }

        $this->service->update($setting, $validated, $uploads, $removeFlags);

        return redirect()
            ->route('admin.footer-settings')
            ->with('success', 'Настройки подвала сайта обновлены.');
    }
}
