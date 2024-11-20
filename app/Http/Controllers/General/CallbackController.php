<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Http\Requests\CallbackRequest\StoreRequest;
use App\Http\Resources\Post\PostResource;
use App\Models\CallbackRequest;
use App\Models\Post;
use Illuminate\Http\Request;

class CallbackController extends Controller
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
