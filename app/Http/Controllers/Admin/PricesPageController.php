<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PricesPage\UpdateRequest;
use App\Models\PricePromo;
use App\Service\PricePromoService;

class PricesPageController extends Controller
{
    public function __construct(private readonly PricePromoService $service)
    {
    }

    public function index()
    {
        $promos = PricePromo::orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.prices-page', compact('promos'));
    }

    public function update(UpdateRequest $request)
    {
        $validated = $request->validated();

        $this->service->syncPromos($validated['promos'] ?? []);

        return redirect()
            ->route('admin.prices-page')
            ->with('success', 'Акции на странице «Цены» обновлены.');
    }
}
