<?php
use Illuminate\Support\Facades\Config;

/***
 * CKEditor
 */
Route::get('elfinder/ckeditor', '\Barryvdh\Elfinder\ElfinderController@showCKeditor4');
Route::get('elfinder/popup', '\Barryvdh\Elfinder\Barryvdh\Elfinder\ElfinderController@showPopup');

/***
 * Authen page
 */
Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['web']
], function () {
    Route::auth();
});

/***
 * Admin page
 */
Route::group(
    [
        'prefix' => LaravelLocalization::setLocale() . '/admin',
        'middleware' => ['web', 'auth', 'localeSessionRedirect', 'localizationRedirect'],
        'namespace' => 'Admin'
    ]
    , function () {
    Route::get('profile', 'ProfileController@index');
    Route::post('profile', 'ProfileController@update');

    Route::get('/', 'WelcomeController@index');
    Route::get('settings', 'SettingController@index');
    Route::post('settings', 'SettingController@update');

    Route::get('media', '\Barryvdh\Elfinder\ElfinderController@showCKeditor4');

    Route::get('banner', 'BannerController@index');
    Route::post('banner', 'BannerController@update');

    Route::get('issues', 'IssueController@index');
    Route::delete('issues/{id}', 'IssueController@destroy');

    // GET DYNAMIC ROUTE =======================================================
    $listsMenu = Config::get('setting.lists');
    $catcontentsMenu = Config::get('setting.catcontents');

    // ROUTE CATEGORY MENU
    foreach($catcontentsMenu as $key => $content) {
      $routecat = $key. '/categories';
      Route::get($routecat, 'CategoryController@index');
      Route::resource($routecat, 'CategoryController');

      $routeproduct = $key. '/product';
      Route::get($routeproduct, 'ProductController@index');
      Route::resource($routeproduct, 'ProductController');
    }

    // ROUTE CONTENTS MENU
    Route::get('content/{menu}', 'ContentController@index');
    Route::post('content/{menu}', 'ContentController@update');

    // ROUTE LISTS MENU
    foreach($listsMenu as $key => $content) {
      $routesub = 'lists/'. $key;

      Route::get($routesub, 'ListController@index');
      Route::resource($routesub, 'ListController');
    }
    // =========================================================================

    // ROUTE REGION
    //Route::get('region/regions', 'RegionController@index');
    //Route::resource('region/regions', 'RegionController');
    //Route::get('province/regions', 'RegionController@index');
    //Route::resource('province/regions', 'RegionController');

    // NEWS
    //Route::get('news', 'NewsController@index');
    //Route::resource('news', 'NewsController');

    // GALLERY
    //Route::get('gallerys', 'GalleryController@index');
    //Route::resource('gallerys', 'GalleryController');

    // SUBSCRIBE
    //Route::get('subscribe', 'SubscribeController@index');
    //Route::get('history', 'SubscribeController@history');
    //Route::delete('subscribe/{id}', 'SubscribeController@delete');

    // MEDIAS
    //Route::get('download/gallerys', 'GallerysController@index');
    //Route::get('loadgallery/{id}', 'GallerysController@getGallery');
    //Route::resource('download/gallerys', 'GallerysController');

    // MANAGE STUDENT
    Route::get('student', 'StudentController@index');
    Route::post('student/save', 'StudentController@save');
    Route::resource('student', 'StudentController');

    // PAYMENT
    Route::get('payment', 'PaymentMastersController@index');
    Route::resource('payment', 'PaymentMastersController');
    Route::post('payment/save', 'PaymentMastersController@save');
    Route::post('payment/batchdelete', 'PaymentMastersController@batchdelete');
    Route::get('paymentdetail', 'PaymentDetailsController@index');
    Route::resource('paymentdetail', 'PaymentDetailsController');
    Route::post('paymentdetail/save', 'PaymentDetailsController@save');

    // IMPORT
    //Route::get('import/custInfo', 'ImportController@custinfo');
    //Route::get('import/invoice', 'ImportController@invoice');
    Route::post('import/uploadcustinfo' , 'ImportController@uploadCustInfo');
    Route::post('import/uploadinvoice' , 'ImportController@uploadInvoice');

    // Mail
    Route::get('template', 'TemplatesController@index');
    Route::resource('template', 'TemplatesController');
    Route::get('maillist', 'MaillistsController@index');
    Route::resource('maillist', 'MaillistsController');

    // Report
    Route::get('report', 'ReportsController@index');
    //Route::resource('report', 'ReportsController');
    Route::get('report/export', 'ReportsController@export');

    // gen mail
    Route::post('maillist/genmail', 'MaillistsController@genmail');
    Route::post('maillist/sendmail', 'MaillistsController@sendmail');
    Route::post('maillist/getfile', 'MaillistsController@getfile');

    // users
    Route::get('users', 'UserController@index');
    Route::resource('users', 'UserController');
});

/***
 * Home page
 */
Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['web', 'localeSessionRedirect', 'localizationRedirect'],
        'namespace' => 'Home'
    ]
    , function () {
    Route::get('/', 'HomeController@index');
      Route::get('home', 'HomeController@index');

    // CONTACT
    //Route::get('contact', 'ContactController@index');
    //Route::get('contact/{tab}', 'ContactController@index');
    //Route::post('contact/send', 'ContactController@contactSend');
    //Route::get('get_captcha/{config?}', function (\Mews\Captcha\Captcha $captcha, $config = 'default') {
    //  return $captcha->src($config);
    //});

    Route::get('invoice/{studentid}/{masterid}', 'HomeController@invoice');
    Route::get('payment/{encryptionstr}', 'HomeController@payment');

    Route::get('payment/result/success', 'HomeController@paymentsuccess');
    Route::get('payment/result/fail', 'HomeController@paymentfail');
    Route::get('payment/result/cancel', 'HomeController@paymentcancel');
    //Route::post('confirm', 'HomeController@paymentconfirm');
    //Route::get('paymentinfo', 'HomeController@paymentinfo');
});

Route::group(['middleware' => 'web', 'namespace' => 'Home'], function () {
    Route::get('/set/{value}', 'SessionController@set');
    Route::get('/get', 'SessionController@get');
    Route::get('/destroy', 'SessionController@destroy');

    Route::get('/en/set/{value}', 'SessionController@set');
    Route::get('/en/get', 'SessionController@get');
    Route::get('/en/destroy', 'SessionController@destroy');
});
