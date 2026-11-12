<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        //
        // 2. Tambahkan kode ini agar Laravel selalu men-generate URL HTTPS
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }

        // --- FITUR BARU: PENYADAP AKTIVITAS LOGIN ---
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Login::class, function ($event) {
            \App\Models\LoginActivity::create([
                'user_id'    => $event->user->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });
    }
}
