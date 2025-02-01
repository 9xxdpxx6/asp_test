<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VisitorTracker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next) {
        $ip = $request->ip();
        $userAgent = $request->header('User-Agent');

        // Запись в БД, если такого IP нет (уникальный визит)
        if (!Visit::where('ip_address', $ip)->exists()) {
            Visit::create(['ip_address' => $ip, 'user_agent' => $userAgent]);
        }

        return $next($request);
    }
}
