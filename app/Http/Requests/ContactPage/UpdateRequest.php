<?php

namespace App\Http\Requests\ContactPage;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page_title' => 'required|string|max:255',
            'page_subtitle' => 'nullable|string|max:500',
            'contacts_heading' => 'nullable|string|max:255',
            'contacts_intro' => 'nullable|string|max:5000',
            'phones' => 'nullable|array',
            'phones.*' => 'nullable|string|max:120',
            'emails' => 'nullable|array',
            'emails.*' => 'nullable|string|max:255',
            'branches' => 'required|array|min:1',
            'branches.*.id' => 'nullable|integer|exists:contact_branches,id',
            'branches.*.title' => 'required|string|max:255',
            'branches.*.map_embed_html' => 'nullable|string|max:100000',
            'branches.*.details_text' => 'nullable|string|max:50000',
            'branches.*.pending_delete' => 'sometimes|boolean',
            'branches.*.existing_photos' => 'nullable|array',
            'branches.*.existing_photos.*' => 'nullable|string|max:500',
            'branches.*.photos' => 'nullable|array',
            'branches.*.photos.*' => 'nullable|image|max:8192',
        ];
    }
}
