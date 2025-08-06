<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Contents;
use Helper;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class ContentController extends AdminController
{
	public function index($menu)
	{
		$title = trans('admin.content-' . $menu);
		$content = Contents::firstOrNew([Contents::MENU => $menu]);
		return view('admin.contents', [
			'title' => $title,
			'content' => $content
		]);
	}

	public function update(Request $request, $menu)
	{
		$content = Contents::firstOrNew([Contents::MENU => $menu]);

		$validate = [];
		foreach (LaravelLocalization::getSupportedLocales() as $locale => $properties) {
			$title = 'title_' . $locale;
			$detail = 'detail_' . $locale;

			$validate[$title] = 'required';
			$validate[$detail] = 'required';

			$content->translateOrNew($locale)->title = $request[$title];
			$content->translateOrNew($locale)->detail = $request[$detail];
		}

		$this->validate($request, $validate);
		$content->save();

		return Helper::redirect('admin/content/' . $menu);
	}
}
