<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class Controller extends BaseController
{

    public function __construct()
    {
        $lang = LaravelLocalization::getCurrentLocale();
        $title = Settings::firstOrNew([Settings::KEY => Settings::WEB_TITLE]);
        $desc = Settings::firstOrNew([Settings::KEY => Settings::WEB_DESC]);
        $keyword = Settings::firstOrNew([Settings::KEY => Settings::WEB_KEYWORD]);

        View::share([
            'title' => $title->value,
            'desc' => $desc->value,
            'keyword' => $keyword->value,
            'lang' => $lang,
        ]);
    }
}
