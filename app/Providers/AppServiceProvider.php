<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\WhatsAppService;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(WhatsAppService::class, function ($app) {
            return new WhatsAppService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        config(['app.locale' => 'id']);
	    Carbon::setLocale('id');
    }
}
