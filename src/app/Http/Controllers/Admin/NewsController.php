<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Categories;
use App\Models\News;
use Helper;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class NewsController extends AdminController
{
    public function index()
    {
        $news = News::orderBy('sort', 'asc')->get();

        return view('admin.news', [
            'news' => $news,
        ]);
    }

    public function create()
    {
        $news = new News;

        return view('admin.news-create', [
            'news' => $news,
            'categories' => $this->getCategories(),
        ]);
    }

    public function store(Request $request)
    {
        $this->doValidate($request);
        $news = new News;
        $this->doSave($request, $news);

        return Helper::redirect('admin/news');
    }

    public function edit($id)
    {
        $news = News::find($id);

        return view('admin.news-create', [
            'news' => $news,
            'categories' => $this->getCategories(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->doValidate($request);
        $news = News::find($id);
        $this->doSave($request, $news);

        return Helper::redirect('admin/news');
    }

    public function destroy($id)
    {
        $count = News::destroy($id);

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
        // $validate['news_date'] = 'required';
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

    private function doSave(Request $request, $news)
    {
        foreach (LaravelLocalization::getSupportedLocales() as $locale => $properties) {
            $title = 'title_'.$locale;
            $detail = 'detail_'.$locale;

            $news->translateOrNew($locale)->title = $request[$title];
            $news->translateOrNew($locale)->detail = $request[$detail];
        }

        $news->news_category_id = $request->category;
        $news->news_date = $request->news_date;
        $news->images = $request->image;
        $news->active = ($request->active === 'active');
        $news->pin_home_page = $this->isPinToHome($request);
        $news->sort = $request->sort;
        $news->save();
    }

    private function getCategories()
    {
        return Categories::where('active', true)
            ->where('menu', 'news-cat')
            ->get();
    }
}
