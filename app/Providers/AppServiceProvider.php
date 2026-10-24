<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;
use App\Services\CaptchaService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CaptchaService::class, function () {
            return new CaptchaService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        Validator::extend('captcha', function ($attribute, $value, $parameters, $validator) {
            return app(CaptchaService::class)->validate($value);
        }, 'The captcha code is incorrect.');
    }
}
