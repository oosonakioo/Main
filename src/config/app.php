<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    'timezone' => 'Asia/Bangkok',

    'log' => env('APP_LOG', 'single'),

    'providers' => ServiceProvider::defaultProviders()->merge([
        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

        /*
         * 3rd Application
         */
        Mcamara\LaravelLocalization\LaravelLocalizationServiceProvider::class,
        Dimsav\Translatable\TranslatableServiceProvider::class,
        Jenssegers\Date\DateServiceProvider::class,
        Barryvdh\Elfinder\ElfinderServiceProvider::class,
        Mews\Purifier\PurifierServiceProvider::class,
        // Collective\Html\HtmlServiceProvider::class,
        Mews\Captcha\CaptchaServiceProvider::class,
        Maatwebsite\Excel\ExcelServiceProvider::class,
        niklasravnsborg\LaravelPdf\PdfServiceProvider::class,
    ])->toArray(),

    'aliases' => Facade::defaultAliases()->merge([
        'Captcha' => Mews\Captcha\Facades\Captcha::class,
        'Date' => Jenssegers\Date\Date::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        'LaravelLocalization' => Mcamara\LaravelLocalization\Facades\LaravelLocalization::class,
        'PDF' => niklasravnsborg\LaravelPdf\Facades\Pdf::class,
        'Purifier' => Mews\Purifier\Facades\Purifier::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
    ])->toArray(),

];
