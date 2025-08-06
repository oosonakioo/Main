<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Issues;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class IssueController extends AdminController
{
  public function index()
  {
    $issues = Issues::orderBy('created_at', 'desc')->get();

    return view('admin.issues', [
		'menu' => 'issues',
		'issues' => $issues
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
