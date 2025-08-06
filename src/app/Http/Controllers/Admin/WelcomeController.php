<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Helper;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class WelcomeController extends AdminController
{
	public function index()
	{
		$lang = LaravelLocalization::getCurrentLocale();

		return view('admin.welcome', [
			'lang'	=> $lang,
		]);
	}
}
