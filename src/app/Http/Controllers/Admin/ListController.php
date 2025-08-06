<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Lists;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class ListController extends AdminController
{
	public function index()
	{
		$menu = $this->getCategoryURL();
		$returnview = 'admin.lists';

		$lists = Lists::where('menu', $menu)
			->orderBy('sort', 'asc')
			->orderBy('updated_at', 'desc')
			->get();
		return view($returnview, [
			'lists' => $lists,
			'menu' => $menu,
		]);
	}

	public function create()
	{
		$menu = $this->getCategoryURL();
		$returnview = 'admin.lists-create';

		$lists = new lists();
		return view($returnview, [
			'lists' => $lists,
			'menu' => $menu,
		]);
	}

	public function store(Request $request)
	{
		$menu = $this->getCategoryURL();
		$redirect = 'admin/lists/'. $menu;

		$this->doValidate($request);
		$lists = new Lists();
		$this->doSave($request, $lists);
		return Helper::redirect($redirect);
	}

	public function edit($id)
	{
		$menu = $this->getCategoryURL();
		$returnview = 'admin.lists-create';

		$lists = lists::find($id);
		return view($returnview, [
			'lists' => $lists,
			'menu' => $menu,
		]);
	}

	public function update(Request $request, $id)
	{
		$menu = $this->getCategoryURL();
		$redirect = 'admin/lists/'. $menu;

		$this->doValidate($request);
		$lists = Lists::find($id);
		$this->doSave($request, $lists);
		return Helper::redirect($redirect);
	}

	public function destroy($id)
	{
		$count = Lists::destroy($id);
		return $count == 1 ? $id : -1;
	}

	private function doValidate(Request $request)
	{
		$validate = [];
		foreach (LaravelLocalization::getSupportedLocales() as $locale => $properties) {
			$title = 'title_' . $locale;
			//$detail = 'detail_' . $locale;

			$validate[$title] = 'required';
			//$validate[$detail] = 'required';
		}
		$this->validate($request, $validate);
	}

	private function doSave(Request $request, $lists)
	{
		foreach (LaravelLocalization::getSupportedLocales() as $locale => $properties) {
			$title = 'title_' . $locale;
			$detail = 'detail_' . $locale;

			$lists->translateOrNew($locale)->title = $request[$title];
			$lists->translateOrNew($locale)->detail = $request[$detail];
		}
		$lists->menu = $request->menu;
		$lists->value = $request->value;
		$lists->option = $request->option;
		$lists->image = $request->image;
		$lists->sort = $request->sort;
		$lists->active = ($request->active === 'active');
		$lists->save();
	}

	private static function getCategoryURL()
	{
		$arr_menu = Config::get('setting.lists');
		$currenturl = $_SERVER['REQUEST_URI'];

		foreach ($arr_menu as $value) {
			if (strpos($currenturl, $value) !== false) {
				$return = $value;
			}
		}
		return $return;
	}
}
