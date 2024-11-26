<?php

namespace App\Http\Controllers\General\CallbackRequest;

use App\Http\Controllers\Controller;
use App\Http\Requests\CallbackRequest\StoreRequest;
use App\Models\CallbackRequest;

class StoreController extends Controller
{
    public function __invoke(StoreRequest $request)
    {
        $data = $request->validated();
        $data['status_id'] = 1;

        // Сохраняем данные в базу
        CallbackRequest::create($data);

        return response()->json(['message' => 'Запрос успешно создан'], 201);
    }
}
