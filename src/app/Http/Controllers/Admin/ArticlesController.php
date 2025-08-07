<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Articles;
use App\Models\Categories;
use Helper;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class ArticlesController extends AdminController
{
    public function index(): View
    {
        $articles = Articles::orderBy('articles_date', 'desc')->get();

        return view('admin.articles', [
            'articles' => $articles,
        ]);
    }

    public function create(): View
    {
        $articles = new Articles;

        return view('admin.articles-create', [
            'articles' => $articles,
            'categories' => $this->getCategories(),
        ]);
    }

    public function store(Request $request)
    {
        $this->doValidate($request);
        $articles = new Articles;
        $this->doSave($request, $articles);

        return Helper::redirect('admin/articles');
    }

    public function edit($id): View
    {
        $articles = Articles::find($id);

        return view('admin.articles-create', [
            'articles' => $articles,
            'categories' => $this->getCategories(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->doValidate($request);
        $articles = Articles::find($id);
        $this->doSave($request, $articles);

        return Helper::redirect('admin/articles');
    }

    public function destroy($id)
    {
        $count = Articles::destroy($id);

        return $count == 1 ? $id : -1;
    }

    private function doValidate(Request $request)
    {
        $validate = [];
        foreach (LaravelLocalization::getSupportedLocales() as $locale => $properties) {
            $title = 'title_'.$locale;
            $detail = 'detail_'.$locale;

            $validate[$title] = 'required';
            $validate[$detail] = 'required';
        }
        // $validate['category'] = 'required';
        // $validate['articles_date'] = 'required';
        // $validate['image'] = 'required';

        // if($this->isPinToHome($request)) {
        //    $validate['sort'] = 'required';
        // }

        $this->validate($request, $validate);
    }

    private function isPinToHome(Request $request)
    {
        return $request->pin_home_page === 'pin_home_page';
    }

    private function doSave(Request $request, $articles)
    {
        foreach (LaravelLocalization::getSupportedLocales() as $locale => $properties) {
            $title = 'title_'.$locale;
            $description = 'description_'.$locale;
            $detail = 'detail_'.$locale;
            $articleby = 'articleby_'.$locale;

            $articles->translateOrNew($locale)->title = $request[$title];
            $articles->translateOrNew($locale)->description = $request[$description];
            $articles->translateOrNew($locale)->detail = $request[$detail];
            $articles->translateOrNew($locale)->articleby = $request[$articleby];
        }

        $articles->articles_category_id = $request->category;
        $articles->articles_date = $request->articles_date;
        $articles->image_thumbnail = $request->image_thumbnail;
        $articles->image_main = $request->image_main;
        $articles->view = $request->view;
        $articles->active = ($request->active === 'active');
        $articles->pin_home_page = $this->isPinToHome($request);
        $articles->sort = $request->sort;
        $articles->save();
    }

    private function getCategories()
    {
        return Categories::where('active', true)
            ->where('menu', 'articles-cat')
            ->get();
    }
}
