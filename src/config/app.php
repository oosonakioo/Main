<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    'timezone' => 'Asia/Bangkok',

    'log' => env('APP_LOG', 'single'),


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
