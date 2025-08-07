<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\User;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class UserController extends AdminController
{
    public function index()
    {
        $returnview = 'admin.users';

        $users = User::where('id', '>', '1')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view($returnview, [
            'users' => $users,
        ]);
    }

    public function create()
    {
        $returnview = 'admin.users-create';

        $users = new User;

        return view($returnview, [
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $redirect = 'admin/users';

        $this->doValidate($request);
        $users = new User;
        $this->doSave($request, $users);

        return Helper::redirect($redirect);
    }

    public function edit($id)
    {
        $returnview = 'admin.users-create';

        $users = User::find($id);

        return view($returnview, [
            'users' => $users,
        ]);
    }

    public function update(Request $request, $id)
    {
        $redirect = 'admin/users';

        $this->doValidate($request);
        $users = User::find($id);
        $this->doSave($request, $users);

        return Helper::redirect($redirect);
    }

    public function destroy($id)
    {
        $count = User::destroy($id);

        return $count == 1 ? $id : -1;
    }

    private function doValidate(Request $request)
    {
        $validate = [];
        foreach (LaravelLocalization::getSupportedLocales() as $locale => $properties) {
            // $title = 'title_' . $locale;
            // $detail = 'detail_' . $locale;

            $validate['name'] = 'required';
            $validate['email'] = 'required|email';
            $validate['password'] = 'required';
        }
        $this->validate($request, $validate);
    }

    private function doSave(Request $request, $users)
    {
        foreach (LaravelLocalization::getSupportedLocales() as $locale => $properties) {
            // $title = 'title_' . $locale;
            // $detail = 'detail_' . $locale;

            // $lists->translateOrNew($locale)->title = $request[$title];
            // $lists->translateOrNew($locale)->detail = $request[$detail];
        }

        if (Input::get('permission') == '') {
            $permission = '';
        } else {
            $permission = implode(',', Input::get('permission'));
            $permission = ','.$permission.',';
        }

        $users->name = $request->name;
        $users->email = $request->email;
        $users->password = bcrypt($request->password);
        $users->permission = $permission;
        $users->save();
    }
}
