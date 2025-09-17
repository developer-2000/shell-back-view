<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole {

    /**
     * Проверка роли пользователя.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Проверка, имеет ли текущий пользователь одну из необходимых ролей
        if (!$request->user() || !$request->user()->roles->pluck('name')->intersect($roles)->isNotEmpty()) {
            return response()->json(['message' => 'No access'], 403);
        }

        return $next($request);
    }

}
