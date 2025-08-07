<?php

namespace App\Http\Controllers\Admin;

use Illuminate\View\View;
use App\Http\Controllers\AdminController;
use App\Models\Issues;

class IssueController extends AdminController
{
    public function index(): View
    {
        $issues = Issues::orderBy('created_at', 'desc')->get();

        return view('admin.issues', [
            'menu' => 'issues',
            'issues' => $issues,
        ]);
    }

    public function destroy($id)
    {
        $issue = Issues::find($id);
        if ($issue->delete()) {
            return $id;
        }

        return -1;
    }
}
