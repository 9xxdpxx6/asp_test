<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactPage\UpdateRequest;
use App\Models\ContactBranch;
use App\Models\ContactPageSetting;
use App\Service\ContactPageService;

class ContactPageController extends Controller
{
    public function __construct(private readonly ContactPageService $service)
    {
    }

    public function index()
    {
        $settings = ContactPageSetting::query()->first() ?? new ContactPageSetting([
            'page_title' => 'Наши адреса',
            'page_subtitle' => '',
            'contacts_heading' => 'Свяжитесь с нами',
            'contacts_intro' => '',
            'phones' => [],
            'emails' => [],
        ]);

        $branches = ContactBranch::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $phoneList = old('phones');
        if (! is_array($phoneList)) {
            $phoneList = array_values($settings->phones ?? []);
        }
        if (count($phoneList) === 0) {
            $phoneList = [''];
        }

        $emailList = old('emails');
        if (! is_array($emailList)) {
            $emailList = array_values($settings->emails ?? []);
        }
        if (count($emailList) === 0) {
            $emailList = [''];
        }

        $branchRows = $branches->map(function (ContactBranch $b) {
            return [
                'id' => $b->id,
                'title' => $b->title,
                'map_embed_html' => $b->map_embed_html ?? '',
                'details_text' => $b->details_text ?? '',
                'photo_slots' => array_values(array_pad($b->photos ?? [], 3, '')),
            ];
        })->values()->all();

        return view('admin.contacts-page', compact('settings', 'phoneList', 'emailList', 'branchRows'));
    }

    public function update(UpdateRequest $request)
    {
        $validated = $request->validated();

        $phones = $this->normalizeStringList($validated['phones'] ?? []);
        $emails = $this->normalizeStringList($validated['emails'] ?? []);

        $this->service->updateSettings([
            'page_title' => $validated['page_title'],
            'page_subtitle' => $validated['page_subtitle'] ?? null,
            'contacts_heading' => $validated['contacts_heading'] ?? 'Свяжитесь с нами',
            'contacts_intro' => $validated['contacts_intro'] ?? null,
            'phones' => $phones,
            'emails' => $emails,
        ]);

        $this->service->syncBranches($validated['branches']);

        return redirect()
            ->route('admin.contacts-page')
            ->with('success', 'Страница «Контакты» обновлена.');
    }

    /**
     * @param  array<int, mixed>  $items
     * @return array<int, string>
     */
    protected function normalizeStringList(array $items): array
    {
        return array_values(array_filter(array_map(function ($v) {
            return trim((string) $v);
        }, $items), fn (string $s) => $s !== ''));
    }
}
