<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

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
