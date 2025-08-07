<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Medias;
use App\Models\MediasGallery;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class GallerysController extends AdminController
{
    public function index(): View
    {
        $menu = $this->getDownloadURL();
        $medias = Medias::where('menu', 'gallerys')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.medias', [
            'menu' => $menu,
            'medias' => $medias,
        ]);
    }

    public function create(): View
    {
        $menu = $this->getDownloadURL();
        $medias = new Medias;

        return view('admin.medias-create', [
            'menu' => $menu,
            'medias' => $medias,
        ]);
    }

    public function store(Request $request)
    {
        $menu = $this->getDownloadURL();
        $returnview = 'admin/download/'.$menu;

        $this->doValidate($request);
        $medias = new Medias;
        $this->doSave($request, $medias);

        return Helper::redirect($returnview);
    }

    public function edit($id): View
    {
        $menu = $this->getDownloadURL();
        $medias = Medias::find($id);
        $gallerycount = MediasGallery::where('medias_id', $id)
            ->get()
            ->count();

        return view('admin.medias-create', [
            'menu' => $menu,
            'medias' => $medias,
            'gallerycount' => $gallerycount,
        ]);
    }

    public function update(Request $request, $id)
    {
        $menu = $this->getDownloadURL();
        $returnview = 'admin/download/'.$menu;

        $this->doValidate($request);
        $medias = Medias::find($id);
        $this->doSave($request, $medias);

        return Helper::redirect($returnview);
    }

    public function destroy($id)
    {
        $count = Medias::destroy($id);

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
        $this->validate($request, $validate);
    }

    private function isPinToHome(Request $request)
    {
        return $request->pin_home_page == 'pin_home_page';
    }

    private function doSave(Request $request, $medias)
    {
        foreach (LaravelLocalization::getSupportedLocales() as $locale => $properties) {
            $title = 'title_'.$locale;
            // $detail = 'detail_' . $locale;

            $medias->translateOrNew($locale)->title = $request[$title];
            // $medias->translateOrNew($locale)->detail = $request[$detail];
        }

        $medias->menu = $request->menu;
        $medias->images = $request->images;
        $medias->downloads = $request->downloads;
        $medias->pin_home_page = $this->isPinToHome($request);
        $medias->active = ($request->active === 'active');
        $medias->sort = $request->sort;
        $medias->save();

        // lastest id
        $lastest_id = $medias->id;
        $max_gallery = $request->gallerycount;
        $list_id = '';

        for ($i = 0; $i < $max_gallery; $i++) {

            $imagerequest = $request->idrequest[$i];

            $galleryimages = $request->$imagerequest;
            $imagessort = $request->imagessort[$i];
            $galleryid = $request->galleryid[$i];

            if ($galleryid == -1) {
                // insert
                $savegallery = new MediasGallery;
            } else {
                // update
                $savegallery = MediasGallery::find($galleryid);
            }
            $savegallery->medias_id = $lastest_id;
            $savegallery->images = $galleryimages;
            $savegallery->sort = $imagessort;
            $savegallery->save();

            $list_id .= $savegallery->id.',';
        }
        if ($list_id != '') {
            $list_id = substr($list_id, 0, -1);
            $sql = 'DELETE FROM medias_gallerys WHERE medias_id = '.$lastest_id.' AND id NOT IN ('.$list_id.')';
            $deleted = DB::delete($sql);
        } else {
            $sql = 'DELETE FROM medias_gallerys WHERE medias_id = '.$lastest_id;
            $deleted = DB::delete($sql);
        }
    }

    public function getGallery($id)
    {
        $gallerynumber = 0;
        $result = '';

        if ($id == -99) {
            // no gallery to Load

        } else {
            $mediasgallery = MediasGallery::where('medias_id', $id)
                ->orderBy('sort', 'asc')
                ->get();
            foreach ($mediasgallery as $items) {

                $gallerynumber++;

                $result .= '<tr>';

                $result .= '<td style="border: 1px solid #afafaf;" nowrap>';
                $result .= '<div class="col-md-8 col-xs-6">';
                $result .= '<input class="form-control" type="text" id="imagesgallery'.$gallerynumber.'" name="imagesgallery'.$gallerynumber.'" value="'.$items->images.'" readonly/>';
                $result .= '<span class="input-group-btn">';
                $result .= '</div>';
                $result .= '<div class="col-md-4 col-xs-6">';
                $result .= '<button class="btn popup_selector" data-inputid="imagesgallery'.$gallerynumber.'">'.trans('admin.contents-browse').'</button>';
                $result .= '</span>';
                $result .= '</div>';

                $result .= '<br>';
                $result .= '<div class="col-md-12 col-xs-12">';
                $result .= '<img src="'.asset($items->images).'" width="150px" class="img-responsive">';
                $result .= '</div>';
                $result .= '</td>';
                $result .= '<td style="border: 1px solid #afafaf;">';
                $result .= '<input class="form-control" type="text" id="imagessort" name="imagessort[]" value="'.$items->sort.'" onkeypress="return isNumber(event)" required>';
                $result .= '</td>';
                $result .= '<td style="border: 1px solid #afafaf;" align="center">';
                $result .= '<a href="javascript:void(0);" class="removegallery" onclick="reductThis();"><span class="text-danger">'.trans('admin.contents-delete').'</span></a>';
                $result .= '<input type="hidden" id="galleryid" name="galleryid[]" value="'.$items->id.'">';
                $result .= '<input type="hidden" id="idrequest" name="idrequest[]" value="imagesgallery'.$gallerynumber.'">';
                $result .= '</td>';
                $result .= '</tr>';
            }
        }

        return $result;
    }

    private static function getDownloadURL()
    {
        $arr_menu = ['gallerys'];
        $currenturl = $_SERVER['REQUEST_URI'];

        foreach ($arr_menu as $value) {
            if (strpos($currenturl, $value) !== false) {
                $return = $value;
            }
        }

        return $return;
    }
}
