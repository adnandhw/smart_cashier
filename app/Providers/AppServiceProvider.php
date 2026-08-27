<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Dynamically resolve public path based on the active index.php location
        if (isset($_SERVER['SCRIPT_FILENAME']) && basename($_SERVER['SCRIPT_FILENAME']) === 'index.php') {
            $publicPath = dirname($_SERVER['SCRIPT_FILENAME']);
            $this->app->usePublicPath($publicPath);
        } else {
            // Fallback for CLI/Artisan console commands
            if (is_dir(base_path('public_html'))) {
                $this->app->usePublicPath(base_path('public_html'));
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production' || request()->server('HTTP_X_FORWARDED_PROTO') === 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
