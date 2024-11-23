<?php

namespace App\Http\Controllers\Admin\Discount;

use App\Http\Controllers\Controller;
use App\Http\Filters\CategoryFilter;
use App\Http\Requests\Category\FilterRequest;
use App\Models\Category;
use App\Models\Discount;
use Faker\Provider\Base;
use Illuminate\Http\Request;

class IndexController extends BaseController
{
    public function __invoke(FilterRequest $request)
    {
        $data = $request->validated();
        // Получаем параметры напрямую из запроса
        $minValue = $request->input('min_value');
        $maxValue = $request->input('max_value');

        // Начинаем запрос к модели Discount
        $query = Discount::query();

        if ($minValue) {
            $query->where('value', '>=', $minValue);
        }

        if ($maxValue) {
            $query->where('value', '<=', $maxValue);
        }

        // Получаем все скидки после применения фильтров
        $discounts = $query->get();

        return view('discount.index', compact('discounts'));
    }
}
