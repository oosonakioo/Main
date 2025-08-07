<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class AdminController extends Controller implements HasMiddleware
{
    public function __construct()
    {

        $lang = LaravelLocalization::getCurrentLocale();
        $contentsMenu = Config::get('setting.contents');
        $catcontentsMenu = Config::get('setting.catcontents');
        $listsMenu = Config::get('setting.lists');

        View::share([
            'lang' => $lang,
            'contentsMenu' => $contentsMenu,
            'catcontentsMenu' => $catcontentsMenu,
            'listsMenu' => $listsMenu,
        ]);
    }

    public static function middleware(): array
    {
        return [
            'auth',
        ];
    }
}
