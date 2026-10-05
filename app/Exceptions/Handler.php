<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'blocks',
        'image',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Запрос больше post_max_size: PHP уже выбросил все поля и файлы, поэтому возвращаем на форму с понятной ошибкой.
        // Сессия здесь ещё не запущена (ValidatePostSize — глобальный middleware), поэтому флаг передаём через query.
        $this->renderable(function (PostTooLargeException $e, $request) {
            if ($request->expectsJson()) {
                return null;
            }

            $previous = url()->previous();
            $separator = str_contains($previous, '?') ? '&' : '?';

            return redirect()->to($previous . $separator . 'upload_error=1');
        });
    }
}
