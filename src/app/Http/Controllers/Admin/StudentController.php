<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Students;
use Illuminate\Http\Request;
use Helper;

class StudentController extends AdminController
{
    public function index()
    {
        $student = Students::orderBy('updated_at', 'desc')->get();
        return view('admin.student', [
            'student' => $student
        ]);
    }

    public function create()
    {
        $student = new Students();
        return view('admin.student-create', [
            'student' => $student,
        ]);
    }
    public function save(Request $request)
    {
        $data_ok = true;
        //$this->doValidate($request);
        if ($request->custcode > 0 && $request->custid > 0 && $request->custgroupcode > 0 && $request->custnameeng != "" && $request->contfax != "" && $request->contactname <> "" && $request->contemail <> "") {
          if(preg_match("/[a-z]/i", $request->custnameeng)){

          } else {
            $response = array ('status' => 'error', 'msg' => 'Name no contains English alphabet');
            $data_ok = false;
          }
        } else {
          $response = array ('status' => 'error', 'msg' => 'Incomplete information');
          $data_ok = false;
        }

        if ($data_ok) {
            $hasData= Students::where('custcode', $request->custcode);
            if ($hasData->count() == 0) {
                $student = new Students();
                $this->doSave($request, $student);
            } else {
                $student = Students::find($hasData->first()->id);
      					if($hasData->first()->active)
      						$request->active = 'active';
                $this->doSave($request, $student);
            }
            $response = array ('status' => 'success'/*, 'json' => $json*/);
        }
        return response ()->json ($response);
    }

    public function store(Request $request)
    {
        $this->doValidate($request);
        $student = new Students();
        $this->doSave($request, $student);
        return Helper::redirect('admin/student');
    }

    public function edit($id)
    {
        $student = Students::find($id);
        return view('admin.student-create', [
            'student' => $student,
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->doValidate($request);
        $student = Students::find($id);
        $this->doSave($request, $student);
        return Helper::redirect('admin/student');
    }

    public function destroy($id)
    {
        $count = Students::destroy($id);
        return $count == 1 ? $id : -1;
    }

    private function doValidate(Request $request)
    {
        $validate = [];
        $this->validate($request, $validate);
    }

    private function doSave(Request $request, $student)
    {
        $student->custcode = $request->custcode;
        $student->custid = $request->custid;
        $student->custgroupcode = $request->custgroupcode;
        $student->custnameeng = $request->custnameeng;
        $student->custadd = $request->custadd;
        $student->contfax = $request->contfax;
        $student->contactname = $request->contactname;
        $student->contemail = $request->contemail;
        $student->active = ($request->active === 'active');
        $student->save();
    }
}
