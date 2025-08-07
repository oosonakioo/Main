<?php

namespace App\Http\Controllers\Admin;

use Illuminate\View\View;
use App\Http\Controllers\AdminController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class WelcomeController extends AdminController
{
    public function index(): View
    {
        $lang = LaravelLocalization::getCurrentLocale();

        return view('admin.welcome', [
            'lang' => $lang,
        ]);
    }
}
