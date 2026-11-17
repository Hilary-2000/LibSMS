<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // isPresent(), getDateDifference(), elongateData(), getTimeAgo() for the views
        require_once app_path("Support/view_helpers.php");
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // the UI is Bootstrap 5 (Skote): render page links with Bootstrap 5 markup
        Paginator::useBootstrapFive();
    }
}
