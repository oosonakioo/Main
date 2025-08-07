<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Home;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;

/***
 * CKEditor
 */
Route::get('elfinder/ckeditor', [\Barryvdh\Elfinder\ElfinderController::class, 'showCKeditor4']);
Route::get('elfinder/popup', [\Barryvdh\Elfinder\Barryvdh\Elfinder\ElfinderController::class, 'showPopup']);

/***
 * Authen page
 */
Route::prefix(LaravelLocalization::setLocale())->middleware('web')->group(function () {
    Route::auth();
});

/***
 * Admin page
 */
Route::prefix(LaravelLocalization::setLocale().'/admin')->middleware('web', 'auth', 'localeSessionRedirect', 'localizationRedirect')->group(
    function () {
        Route::get('profile', [Admin\ProfileController::class, 'index']);
        Route::post('profile', [Admin\ProfileController::class, 'update']);

        Route::get('/', [Admin\WelcomeController::class, 'index']);
        Route::get('settings', [Admin\SettingController::class, 'index']);
        Route::post('settings', [Admin\SettingController::class, 'update']);

        Route::get('media', [\Barryvdh\Elfinder\ElfinderController::class, 'showCKeditor4']);

        Route::get('banner', [Admin\BannerController::class, 'index']);
        Route::post('banner', [Admin\BannerController::class, 'update']);

        Route::get('issues', [Admin\IssueController::class, 'index']);
        Route::delete('issues/{id}', [Admin\IssueController::class, 'destroy']);

        // GET DYNAMIC ROUTE =======================================================
        $listsMenu = Config::get('setting.lists');
        $catcontentsMenu = Config::get('setting.catcontents');

        // ROUTE CATEGORY MENU
        foreach ($catcontentsMenu as $key => $content) {
            $routecat = $key.'/categories';
            Route::get($routecat, [Admin\CategoryController::class, 'index']);
            Route::resource($routecat, Admin\CategoryController::class);

            $routeproduct = $key.'/product';
            Route::get($routeproduct, [Admin\ProductController::class, 'index']);
            Route::resource($routeproduct, Admin\ProductController::class);
        }

        // ROUTE CONTENTS MENU
        Route::get('content/{menu}', [Admin\ContentController::class, 'index']);
        Route::post('content/{menu}', [Admin\ContentController::class, 'update']);

        // ROUTE LISTS MENU
        foreach ($listsMenu as $key => $content) {
            $routesub = 'lists/'.$key;

            Route::get($routesub, [Admin\ListController::class, 'index']);
            Route::resource($routesub, Admin\ListController::class);
        }
        // =========================================================================

        // ROUTE REGION
        // Route::get('region/regions', 'RegionController@index');
        // Route::resource('region/regions', 'RegionController');
        // Route::get('province/regions', 'RegionController@index');
        // Route::resource('province/regions', 'RegionController');

        // NEWS
        // Route::get('news', 'NewsController@index');
        // Route::resource('news', 'NewsController');

        // GALLERY
        // Route::get('gallerys', 'GalleryController@index');
        // Route::resource('gallerys', 'GalleryController');

        // SUBSCRIBE
        // Route::get('subscribe', 'SubscribeController@index');
        // Route::get('history', 'SubscribeController@history');
        // Route::delete('subscribe/{id}', 'SubscribeController@delete');

        // MEDIAS
        // Route::get('download/gallerys', 'GallerysController@index');
        // Route::get('loadgallery/{id}', 'GallerysController@getGallery');
        // Route::resource('download/gallerys', 'GallerysController');

        // MANAGE STUDENT
        Route::get('student', [Admin\StudentController::class, 'index']);
        Route::post('student/save', [Admin\StudentController::class, 'save']);
        Route::resource('student', Admin\StudentController::class);

        // PAYMENT
        Route::get('payment', [Admin\PaymentMastersController::class, 'index']);
        Route::resource('payment', Admin\PaymentMastersController::class);
        Route::post('payment/save', [Admin\PaymentMastersController::class, 'save']);
        Route::post('payment/batchdelete', [Admin\PaymentMastersController::class, 'batchdelete']);
        Route::get('paymentdetail', [Admin\PaymentDetailsController::class, 'index']);
        Route::resource('paymentdetail', Admin\PaymentDetailsController::class);
        Route::post('paymentdetail/save', [Admin\PaymentDetailsController::class, 'save']);

        // IMPORT
        // Route::get('import/custInfo', 'ImportController@custinfo');
        // Route::get('import/invoice', 'ImportController@invoice');
        Route::post('import/uploadcustinfo', [Admin\ImportController::class, 'uploadCustInfo']);
        Route::post('import/uploadinvoice', [Admin\ImportController::class, 'uploadInvoice']);

        // Mail
        Route::get('template', [Admin\TemplatesController::class, 'index']);
        Route::resource('template', Admin\TemplatesController::class);
        Route::get('maillist', [Admin\MaillistsController::class, 'index']);
        Route::resource('maillist', Admin\MaillistsController::class);

        // Report
        Route::get('report', [Admin\ReportsController::class, 'index']);
        // Route::resource('report', 'ReportsController');
        Route::get('report/export', [Admin\ReportsController::class, 'export']);

        // gen mail
        Route::post('maillist/genmail', [Admin\MaillistsController::class, 'genmail']);
        Route::post('maillist/sendmail', [Admin\MaillistsController::class, 'sendmail']);
        Route::post('maillist/getfile', [Admin\MaillistsController::class, 'getfile']);

        // users
        Route::get('users', [Admin\UserController::class, 'index']);
        Route::resource('users', Admin\UserController::class);
    });

/***
 * Home page
 */
Route::prefix(LaravelLocalization::setLocale())->middleware('web', 'localeSessionRedirect', 'localizationRedirect')->group(
    function () {
        Route::get('/', [Home\HomeController::class, 'index']);
        Route::get('home', [Home\HomeController::class, 'index']);

        // CONTACT
        // Route::get('contact', 'ContactController@index');
        // Route::get('contact/{tab}', 'ContactController@index');
        // Route::post('contact/send', 'ContactController@contactSend');
        // Route::get('get_captcha/{config?}', function (\Mews\Captcha\Captcha $captcha, $config = 'default') {
        //  return $captcha->src($config);
        // });

        Route::get('invoice/{studentid}/{masterid}', [Home\HomeController::class, 'invoice']);
        Route::get('payment/{encryptionstr}', [Home\HomeController::class, 'payment']);

        Route::get('payment/result/success', [Home\HomeController::class, 'paymentsuccess']);
        Route::get('payment/result/fail', [Home\HomeController::class, 'paymentfail']);
        Route::get('payment/result/cancel', [Home\HomeController::class, 'paymentcancel']);
        // Route::post('confirm', 'HomeController@paymentconfirm');
        // Route::get('paymentinfo', 'HomeController@paymentinfo');
    });

Route::middleware('web')->group(function () {
    Route::get('/set/{value}', [Home\SessionController::class, 'set']);
    Route::get('/get', [Home\SessionController::class, 'get']);
    Route::get('/destroy', [Home\SessionController::class, 'destroy']);

    Route::get('/en/set/{value}', [Home\SessionController::class, 'set']);
    Route::get('/en/get', [Home\SessionController::class, 'get']);
    Route::get('/en/destroy', [Home\SessionController::class, 'destroy']);
});
