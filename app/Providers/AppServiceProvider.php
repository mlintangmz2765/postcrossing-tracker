<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
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
        // Date format for display and date pickers (flatpickr uses the same d/m/Y letters).
        $dateFormat = (string) (config('app.date_format') ?: 'd/m/Y');
        View::share('dateFormat', $dateFormat);
        View::share('dateFormatHint', strtr($dateFormat, ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY']));
    }
}
