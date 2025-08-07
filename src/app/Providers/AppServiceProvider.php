<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Bootstrap any application services.
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

        $this->bootRoute();
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function bootRoute(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
