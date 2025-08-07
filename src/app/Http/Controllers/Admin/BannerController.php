<?php

namespace App\Http\Controllers\Admin;

use Illuminate\View\View;
use App\Http\Controllers\AdminController;
use App\Models\Banners;
use Helper;
use Illuminate\Http\Request;

class BannerController extends AdminController
{
    public function index(): View
    {
        $banners = [
            Banners::firstOrCreate(['menu' => 'home-1']),
            Banners::firstOrCreate(['menu' => 'home-2']),
            Banners::firstOrCreate(['menu' => 'home-3']),
            Banners::firstOrCreate(['menu' => 'home-4']),
            Banners::firstOrCreate(['menu' => 'home-5']),
            Banners::firstOrCreate(['menu' => 'home-6']),
            Banners::firstOrCreate(['menu' => 'home-7']),
            Banners::firstOrCreate(['menu' => 'home-8']),
            Banners::firstOrCreate(['menu' => 'home-9']),
            Banners::firstOrCreate(['menu' => 'home-10']),
        ];

        return view('admin.banner', [
            'banners' => $banners,
        ]);
    }

    public function update(Request $request)
    {
        $banner1 = Banners::firstOrNew(['menu' => 'home-1']);
        $this->saveBanner($request, $banner1, 1);

        $banner2 = Banners::firstOrNew(['menu' => 'home-2']);
        $this->saveBanner($request, $banner2, 2);

        $banner3 = Banners::firstOrNew(['menu' => 'home-3']);
        $this->saveBanner($request, $banner3, 3);

        $banner4 = Banners::firstOrNew(['menu' => 'home-4']);
        $this->saveBanner($request, $banner4, 4);

        $banner5 = Banners::firstOrNew(['menu' => 'home-5']);
        $this->saveBanner($request, $banner5, 5);

        $banner6 = Banners::firstOrNew(['menu' => 'home-6']);
        $this->saveBanner($request, $banner6, 6);

        $banner7 = Banners::firstOrNew(['menu' => 'home-7']);
        $this->saveBanner($request, $banner7, 7);

        $banner8 = Banners::firstOrNew(['menu' => 'home-8']);
        $this->saveBanner($request, $banner8, 8);

        $banner9 = Banners::firstOrNew(['menu' => 'home-9']);
        $this->saveBanner($request, $banner9, 9);

        $banner10 = Banners::firstOrNew(['menu' => 'home-10']);
        $this->saveBanner($request, $banner10, 10);

        return Helper::redirect('admin/banner');
    }

    private function saveBanner(Request $request, $banner, $index)
    {
        $index = $index - 1;
        $banner->images = $request['image'.$index];
        $banner->link = $request['link'.$index];
        $banner->active = ($request['active'.$index] === 'active');
        $banner->save();
    }
}
