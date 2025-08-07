<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Subscribe;

class SubscribeController extends AdminController
{
    public function index()
    {
        $subscribes = Subscribe::all();

        return view('admin.subscribe', [
            'subscribes' => $subscribes,
        ]);
    }

    public function history()
    {
        $subscribes = Subscribe::onlyTrashed()->get();

        return view('admin.history', [
            'subscribes' => $subscribes,
        ]);
    }

    public function delete($id)
    {
        $subscribe = Subscribe::find($id);
        $subscribe->delete();
        if ($subscribe->trashed()) {
            return $id;
        } else {
            return -1;
        }
    }
}
