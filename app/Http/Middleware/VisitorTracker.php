<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class VisitorTracker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next) {
        try {
            $ip = $request->ip();
            $userAgent = $request->header('User-Agent');

            // Проверяем, существует ли таблица visits
            if (\Schema::hasTable('visits')) {
                // Запись в БД, если такого IP нет (уникальный визит)
                if (!Visit::where('ip_address', $ip)->exists()) {
                    Visit::create(['ip_address' => $ip, 'user_agent' => $userAgent]);
                }
            }
        } catch (\Exception $e) {
            // Если что-то пошло не так, просто пропускаем middleware
            // Логируем ошибку для отладки
            \Log::warning('VisitorTracker middleware failed: ' . $e->getMessage());
        }

        return $next($request);
    }
}
