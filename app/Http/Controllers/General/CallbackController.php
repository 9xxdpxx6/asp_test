<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Http\Resources\Post\PostResource;
use App\Models\CallbackRequest;
use App\Models\Post;
use Illuminate\Http\Request;

class CallbackController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|string|max:50',
            'comment' => 'nullable|string|max:255',
            'status_id' => 1,
        ]);

        // Сохраняем данные в базу
        CallbackRequest::create($validated);

        return response()->json(['message' => 'Запрос успешно создан'], 201);
    }
}
