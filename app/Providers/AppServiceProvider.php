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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Disable Vite if manifest not found (for development environments like Termux)
        // This provides fallback to prevent ViteManifestNotFoundException
        $manifestPath = public_path('build/manifest.json');
        
        if (!file_exists($manifestPath) && app()->environment('local', 'testing')) {
            // Log warning for development
            \Log::warning('Vite manifest not found at: ' . $manifestPath . 
                         '. Running in legacy mode. Run "npm run build" to generate assets.');
        }
    }
}
