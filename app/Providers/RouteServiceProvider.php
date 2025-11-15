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
     * Define the routes for the application.
     */
    public function boot(): void
    {
        // Define aquí los límites de tasa si los necesitas

        $this->routes(function () {
            
            // Carga y prefijo de las rutas API
            Route::middleware('api')
                ->prefix('api') // ESTA LÍNEA APLICA EL PREFIJO /API/
                ->group(base_path('routes/api.php'));

            // Carga de las rutas web (para Inertia y páginas normales)
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    // ... (el resto de la clase, que incluye la configuración de RateLimiting)

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
