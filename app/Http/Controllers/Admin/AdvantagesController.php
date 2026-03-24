<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Advantage\UpdateRequest;
use App\Models\Advantage;
use App\Service\AdvantageService;

class AdvantagesController extends Controller
{
    public function __construct(private readonly AdvantageService $service)
    {
    }

    public function index()
    {
        $advantages = Advantage::orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.advantages', compact('advantages'));
    }

    public function update(UpdateRequest $request)
    {
        $this->service->sync($request->validated('advantages'));

        return redirect()
            ->route('admin.advantages')
            ->with('success', 'Блок "Наши преимущества" обновлён.');
    }
}
