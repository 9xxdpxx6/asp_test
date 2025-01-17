<?php

namespace App\Http\Controllers\General\CallbackRequest;

use App\Http\Controllers\Controller;
use App\Http\Requests\CallbackRequest\StoreRequest;
use App\Models\CallbackRequest;
use App\Mail\CallbackRequest\RequestMail;
use Illuminate\Support\Facades\Mail;

class StoreController extends Controller
{
    public function __invoke(StoreRequest $request)
    {
        $data = $request->validated();
        $data['status_id'] = 1;

        $callback = CallbackRequest::create($data);

        Mail::to('avtoshkola-politekh@mail.ru')->send(new RequestMail(
            $callback->id,
            $data['full_name'],
            $data['phone'],
            $data['email'],
            $data['comment'] ?? null
        ));

        return response()->json(['message' => 'Запрос успешно создан'], 201);
    }
}
