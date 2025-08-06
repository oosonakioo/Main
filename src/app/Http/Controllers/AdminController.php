<?php

namespace App\Http\Controllers;

use App\Models\Contents;
use App\Models\Subcontents;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class AdminController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');

      $lang = LaravelLocalization::getCurrentLocale();
      $contentsMenu = Config::get('setting.contents');
      $catcontentsMenu = Config::get('setting.catcontents');
      $listsMenu = Config::get('setting.lists');

  		View::share([
        'lang'              => $lang,
        'contentsMenu'      => $contentsMenu,
        'catcontentsMenu'   => $catcontentsMenu,
        'listsMenu'         => $listsMenu,
      ]);
    }
}
