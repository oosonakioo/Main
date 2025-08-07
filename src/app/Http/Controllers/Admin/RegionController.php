<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Lists;
use App\Models\Regions;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\View\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class RegionController extends AdminController
{
    public function index(): View
    {
        $menu = $this->getCategoryURL();

        $regions = Regions::where('menu', $menu)
            ->orderBy('sort', 'asc')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.regions', [
            'menu' => $menu,
            'regions' => $regions,
        ]);
    }

    public function create()
    {
        $menu = $this->getCategoryURL();
        $regions = new Regions;

        return $this->returnView($menu, $regions);
    }

    public function store(Request $request)
    {
        $menu = $this->getCategoryURL();
        $redirect = 'admin/'.$menu.'/regions';

        $this->doValidate($request);
        $regions = new Regions;
        $this->doSave($request, $regions, 'create');

        return Helper::redirect($redirect);
    }

    public function edit($id)
    {
        $menu = $this->getCategoryURL();
        $regions = Regions::find($id);

        return $this->returnView($menu, $regions);
    }

    public function update(Request $request, $id)
    {
        $menu = $this->getCategoryURL();
        $redirect = 'admin/'.$menu.'/regions';

        $this->doValidate($request);
        $regions = Regions::find($id);
        $this->doSave($request, $regions, 'update');

        return Helper::redirect($redirect);
    }

    public function destroy($id)
    {
        $count = Regions::destroy($id);

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
        $request->validate($validate);
    }

    private function doSave(Request $request, $regions, $mode)
    {
        if ($mode == 'create') {
            $regions_id = Regions::orderBy('main_id', 'desc')
                ->first();
            if ($regions_id === null) {
                $regions->main_id = 0;
            } else {
                $max_id = $regions_id->main_id + 1;
                $regions->main_id = $max_id;
            }
        }

        foreach (LaravelLocalization::getSupportedLocales() as $locale => $properties) {
            $title = 'title_'.$locale;
            $detail = 'detail_'.$locale;

            $regions->translateOrNew($locale)->title = $request[$title];
            $regions->translateOrNew($locale)->detail = $request[$detail];
        }

        if (isset($request->parents)) {
            $regions->parent_regions_id = $request->parents;
        }

        $regions->group_id = $request->group_id;
        $regions->menu = $request->menu;
        $regions->image = $request->image;
        $regions->sort = $request->sort;
        $regions->active = ($request->active === 'active');
        $regions->save();
    }

    private function returnView($menu, $regions)
    {
        $args = [];
        $args['menu'] = $menu;
        $args['regions'] = $regions;
        $args['groups'] = $this->getGroup('group');

        switch ($menu) {
            case 'province':
                $args['parentText'] = trans('admin.categories-region');
                $args['parents'] = $this->getCategory('region');
                break;

                /*case 'district':
                    $args['parentText'] = trans('admin.categories-province');
                    $args['parents'] = $this->getCategory('province');
                    break;

                case 'subdistrict':
                    $args['parentText'] = trans('admin.categories-district');
                     $args['parents'] = $this->getCategory('district');
                    break;*/
        }

        return view('admin.regions-create', $args);
    }

    private function getCategory($menu)
    {
        return Regions::where('menu', $menu)
            ->where('active', true)
            ->orderBy('sort', 'asc')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    private function getGroup($menu)
    {
        return Lists::where('menu', $menu)
            ->where('active', true)
            ->orderBy('sort', 'asc')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    private static function getCategoryURL()
    {
        $arr_menu = Config::get('setting.catcontents');
        $currenturl = $_SERVER['REQUEST_URI'];
        $return = '';
        foreach ($arr_menu as $value) {
            if (strpos($currenturl, $value) !== false) {
                $return = $value;
            }
        }

        return $return;
    }
}
