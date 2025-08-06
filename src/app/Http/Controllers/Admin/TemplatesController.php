<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Templates;
use Illuminate\Http\Request;
use Helper;

class TemplatesController extends AdminController
{
		public function index()
		{
				$template = Templates::orderBy('updated_at', 'desc')
					->get();

				return view('admin.template', [
					'template'	=> $template
				]);
		}

		public function create()
		{
				$template = new Templates();
				// set default
				$template->mailfrom = 'noreply@kis.ac.th';
				$template->mailreplyto = 'kisfinance@kis.ac.th';
				$template->mailcc = 'kisfinance@kis.ac.th';
				return view('admin.template-create', [
						'template' => $template,
				]);
		}

		public function store(Request $request)
		{
				$this->doValidate($request);
				$template = new Templates();
				$this->doSave($request, $template);
				return Helper::redirect('admin/template');
		}

		public function edit($id)
		{
				$template = Templates::find($id);
				return view('admin.template-create', [
						'template' => $template
				]);
		}

		public function update(Request $request, $id)
		{
				$this->doValidate($request);
				$template = Templates::find($id);
				$this->doSave($request, $template);
				return Helper::redirect('admin/template');
		}

		public function destroy($id)
		{
				$count = Templates::destroy($id);
				return $count == 1 ? $id : -1;
		}

		private function doValidate(Request $request)
		{
				$validate = [];
				$this->validate($request, $validate);
		}

		private function doSave(Request $request, $template)
		{
				$template->mailfrom = $request->mailfrom;
				$template->mailreplyto = $request->mailreplyto;
				$template->mailto = $request->mailto;
				$template->mailcc = $request->mailcc;
				$template->mailsubject = $request->mailsubject;
				$template->mailbody = $request->mailbody;
				$template->active = ($request->active === 'active');
				$template->save();
		}
}
