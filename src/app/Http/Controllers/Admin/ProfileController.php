<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Auth;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends AdminController
{
    public function index(): View
    {
        $user = Auth::user();

        return view('admin.profile', [
            'user' => $user,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:6',
        ]);

        if ($request->user()) {
            $password = $request->password;

            $user = Auth::user();
            $user->password = Hash::make($password);
            $user->save();
            Auth::login($user);

            return Helper::redirect('admin/profile')->with('completed', trans('admin.profile-update-completed'));
        }

        return Helper::redirect('admin/profile')->with('failed', trans('admin.profile-update-failed'));
    }
}
