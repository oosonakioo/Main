<?php

namespace App\Http\Controllers\Admin;

use Illuminate\View\View;
use App\Http\Controllers\AdminController;
use App\Models\Categories;
use App\Models\Products;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class ProductController extends AdminController
{
    public function index(): View
    {
        $menu = $this->getCategoryURL();

        $lang = LaravelLocalization::getCurrentLocale();
        $products = Products::where('menu', $menu)
            ->orderBy('updated_at', 'desc')
            ->get();
        $categories = Categories::where('active', true)
            ->where('menu', $menu)
            ->get();

        return view('admin.products', [
            'lang' => $lang,
            'menu' => $menu,
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        $menu = $this->getCategoryURL();

        $product = new Products;
        $categories = Categories::where('active', true)
            ->where('menu', $menu)
            ->get();

        return view('admin.products-create', [
            'menu' => $menu,
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $menu = $this->getCategoryURL();
        $redirect = 'admin/'.$menu.'/product';

        $this->doValidate($request);
        $product = new Products;
        $this->doSave($request, $product);

        return Helper::redirect($redirect);
    }

    public function edit($id): View
    {
        $menu = $this->getCategoryURL();

        $product = Products::find($id);
        $categories = Categories::where('active', true)
            ->where('menu', $menu)
            ->get();

        return view('admin.products-create', [
            'menu' => $menu,
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, $id)
    {
        $menu = $this->getCategoryURL();
        $redirect = 'admin/'.$menu.'/product';

        $this->doValidate($request);
        $product = Products::find($id);
        $this->doSave($request, $product);

        return Helper::redirect($redirect);
    }

    public function destroy($id)
    {
        $count = Products::destroy($id);

        return $count == 1 ? $id : -1;
    }

    private function doValidate(Request $request)
    {
        $validate = [];
        foreach (LaravelLocalization::getSupportedLocales() as $locale => $properties) {
            $title = 'title_'.$locale;
            // $detail = 'detail_' . $locale;

            $validate[$title] = 'required';
            // $validate[$detail] = 'required';
        }

        // $validate['price'] = 'required';
        // $validate['image'] = 'required';
        $this->validate($request, $validate);
    }

    private function doSave(Request $request, $product)
    {
        foreach (LaravelLocalization::getSupportedLocales() as $locale => $properties) {
            $title = 'title_'.$locale;
            $detail = 'detail_'.$locale;

            $product->translateOrNew($locale)->title = $request[$title];
            $product->translateOrNew($locale)->detail = $request[$detail];
        }
        $product->categories_id = $request->category;
        $product->menu = $request->menu;
        $product->value = $request->value;
        $product->option = $request->option;
        $product->image = $request->image;
        $product->sort = $request->sort;
        $product->active = ($request->active === 'active');
        $product->save();
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
