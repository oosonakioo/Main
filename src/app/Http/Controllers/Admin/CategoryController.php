<?php

namespace App\Http\Controllers\Admin;

use Illuminate\View\View;
use App\Http\Controllers\AdminController;
use App\Models\Categories;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class CategoryController extends AdminController
{
    public function index(): View
    {
        $menu = $this->getCategoryURL();

        $categories = Categories::where('menu', $menu)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.categories', [
            'menu' => $menu,
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        $menu = $this->getCategoryURL();

        $category = new Categories;

        return view('admin.categories-create', [
            'menu' => $menu,
            'category' => $category,
        ]);
    }

    public function store(Request $request)
    {
        $menu = $this->getCategoryURL();
        $redirect = 'admin/'.$menu.'/categories';

        $this->doValidate($request);
        $category = new Categories;
        $this->doSave($request, $category);

        return Helper::redirect($redirect);
    }

    public function edit($id): View
    {
        $menu = $this->getCategoryURL();

        $category = Categories::find($id);

        return view('admin.categories-create', [
            'menu' => $menu,
            'category' => $category,
        ]);
    }

    public function update(Request $request, $id)
    {
        $menu = $this->getCategoryURL();
        $redirect = 'admin/'.$menu.'/categories';

        $this->doValidate($request);
        $category = Categories::find($id);
        $this->doSave($request, $category);

        return Helper::redirect($redirect);
    }

    public function destroy($id)
    {
        $count = Categories::destroy($id);

        return $count == 1 ? $id : -1;
    }

    private function doValidate(Request $request)
    {
        $validate = [];
        foreach (LaravelLocalization::getSupportedLocales() as $locale => $properties) {
            $title = 'title_'.$locale;
            $detail = 'detail_'.$locale;

            $validate[$title] = 'required';
            // $validate[$detail] = 'required';
        }
        $this->validate($request, $validate);
    }

    private function doSave(Request $request, $category)
    {
        foreach (LaravelLocalization::getSupportedLocales() as $locale => $properties) {
            $title = 'title_'.$locale;
            $detail = 'detail_'.$locale;

            $category->translateOrNew($locale)->title = $request[$title];
            $category->translateOrNew($locale)->detail = $request[$detail];
        }
        $category->menu = $request->menu;
        $category->value = $request->value;
        $category->sort = $request->sort;
        $category->active = ($request->active === 'active');
        $category->save();
    }

    private static function getCategoryURL()
    {
        $arr_menu = Config::get('setting.catcontents');
        $currenturl = $_SERVER['REQUEST_URI'];

        foreach ($arr_menu as $value) {
            if (strpos($currenturl, $value) !== false) {
                $return = $value;
            }
        }

        return $return;
    }
}
