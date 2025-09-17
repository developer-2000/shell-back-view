<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/*',
//        'api/login',
    ];


    /**
     * Determine if the request has a valid CSRF token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        // Получаем разрешенные домены из конфигурации
        $allowedDomains = config('csrf.allowed_domains');
        $referer = $request->headers->get('referer');
        $refererHost = parse_url($referer, PHP_URL_HOST);

        // Пропускаем CSRF проверку для разрешенных доменов
        if (in_array($refererHost, $allowedDomains)) {
            return true;
        }

        return parent::tokensMatch($request);
    }
}
