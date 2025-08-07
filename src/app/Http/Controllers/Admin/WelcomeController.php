<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\View\View;
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
