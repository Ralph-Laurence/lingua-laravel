<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
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
        Paginator::useBootstrapFive();

        //$this->registerBladeDirectives();
    }

    // protected function registerBladeDirectives(): void
    // {
    //     $library = [];

    //     Blade::directive('linkHrefOnce', function (string $urlToFile) use (&$library)
    //     {
    //         if (!isset($library[$urlToFile]))
    //         {
    //             $library[$urlToFile] = true;
    //             return "<link rel=\"stylesheet\" href=\"$urlToFile\" />";
    //         }
    //     });

    //     Blade::directive('scriptSrcOnce', function (string $urlToFile) use (&$library)
    //     {
    //         if (!isset($library[$urlToFile]))
    //         {
    //             $library[$urlToFile] = true;
    //             return "<script src=\"$urlToFile\"></script>";
    //         }
    //     });
    // }
}
