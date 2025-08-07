<?php

namespace App\Providers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $langurl = Request::server('REQUEST_URI');
        if (strpos($langurl, '/en/') !== false) {
            $lang = 'en';
            $lang_map = '&language=en&region=EN';
        } else {
            $lang = 'th';
            $lang_map = '&language=th&region=TH';
        }
        view()->share([
            'lang' => $lang,
            'lang_map' => $lang_map,
        ]);

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }
}
