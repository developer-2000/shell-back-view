<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(function () {
                    require base_path('routes/api.php');
                    // Все маршруты из channels.php
//                    require base_path('routes/channels.php');
                });

            // Роутер для Company
            Route::middleware('api')
                ->prefix('company_api')
                ->group(base_path('routes/company_api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // Загрузка маршрутов для каналов
            $this->mapChannels();
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    /**
     * Load the routes for broadcasting channels.
     */
    protected function mapChannels(): void
    {
        Route::middleware('api')
            ->group(base_path('routes/channels.php'));
    }
}
