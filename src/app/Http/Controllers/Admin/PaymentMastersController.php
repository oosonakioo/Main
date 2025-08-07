<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Maillists;
use App\Models\Paymentdetails;
use App\Models\Paymentmasters;
use App\Models\Students;
use DB;
use File;
use Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentMastersController extends AdminController
{
    public function index(): View
    {
        $paymentmaster = Paymentmasters::where('paymentstatus', '<', 6)
            ->where('active', true)
            ->orderBy('updated_at', 'desc')->get();

        return view('admin.payment', [
            'paymentmaster' => $paymentmaster,
        ]);
    }

    public function create(): View
    {
        $paymentmaster = new Paymentmasters;

        return view('admin.payment-create', [
            'paymentmaster' => $paymentmaster,
        ]);
    }

    public function batchdelete(): JsonResponse
    {
        $del_data = Paymentmasters::where('paymentstatus', '<', 6)
            ->whereRaw('shipdate < date_add(now(), interval -1 month)')->get();
        $del_count = 0;

        if ($del_data->count() > 0) {
            foreach ($del_data as $value) {
                $del_count++;

                // delete maillist
                $maillist = Maillists::where('docuno', $value->docuno)->get();
                if ($maillist->count() > 0) {
                    foreach ($maillist as $key => $mailobj) {
                        File::delete(config('setting.pdf-path').$mailobj->attach_pdf, config('setting.excel-path').substr($mailobj->attach_pdf, 0, -4).'.xls');
                    }
                }
                DB::table('maillists')->where('docuno', $value->docuno)->delete();

                // update delete paymentdetail
                DB::table('paymentdetails')->where('docuno_id', $value->docuno)->update(['active' => 0]);
                // DB::table('Paymentdetails')->where('docuno_id', $value->docuno)->delete();

                // update status to 6
                $value->paymentstatus = 6;
                $value->active = 0;
                $value->save();
            }

        }

        $response = ['status' => 'success', 'record' => $del_count];

        return response()->json($response);

    }

    public function save(Request $request): JsonResponse
    {
        $data_ok = true;
        // $this->doValidate($request);
        if ($request->docuno != '' && $request->docudate != '' && $request->shipdate != '' && $request->custcode > 0 && $request->custnameeng != '' && $request->templateno > 0 && $request->remark != '') {
            // OK
        } else {
            $response = ['status' => 'error', 'msg' => 'Incomplete information'];
            $data_ok = false;
        }

        if ($data_ok) {
            $hasStudent = students::where('custcode', $request->custcode);

            if ($hasStudent->count() == 0) {
                $response = ['status' => 'error', 'msg' => 'Student not found.'];
            } else {
                $hasData = Paymentmasters::where('docuno', $request->docuno);
                if ($hasData->count() == 0) {
                    $paymentmaster = new Paymentmasters;
                    $this->doSave($request, $paymentmaster);
                } else {
                    $paymentmaster = Paymentmasters::find($hasData->first()->id);
                    if ($hasData->first()->active) {
                        $request->active = 'active';
                    }
                    $this->doSave($request, $paymentmaster);
                }
                $response = ['status' => 'success'];
            }
        }

        return response()->json($response);
    }

    public function store(Request $request)
    {
        $redirect = 'admin/payment';

        $this->doValidate($request);
        $paymentmaster = new Paymentmasters;
        $this->doSave($request, $paymentmaster);

        return Helper::redirect($redirect);
    }

    public function edit($id): View
    {
        $paymentmaster = Paymentmasters::find($id);

        return view('admin.payment-create', [
            'paymentmaster' => $paymentmaster,
        ]);
    }

    public function update(Request $request, $id)
    {
        $redirect = 'admin/payment';

        $this->doValidate($request);
        $paymentmaster = Paymentmasters::find($id);
        $this->doSave($request, $paymentmaster);

        return Helper::redirect($redirect);
    }

    public function destroy($id)
    {
        $payment_del = Paymentmasters::find($id);
        $docuno_del = $payment_del->docuno;

        $maillists = Maillists::where('docuno', $docuno_del)->delete();
        $deleted = DB::table('paymentdetails')->where('docuno_id', $docuno_del)->delete();

        $count = Paymentmasters::destroy($id);

        return $count == 1 ? $id : -1;
    }

    private function doValidate(Request $request)
    {
        $validate = [];
        $this->validate($request, $validate);
    }

    private function doSave(Request $request, $paymentmaster)
    {
        $paymentmaster->docuno = $request->docuno;
        $paymentmaster->docudate = $request->docudate;
        $paymentmaster->shipdate = $request->shipdate;
        $paymentmaster->custcode = $request->custcode;
        $paymentmaster->custnameeng = $request->custnameeng;
        $paymentmaster->templateno = $request->templateno;
        $paymentmaster->remark = $request->remark;
        // $paymentmaster->active = ($request->active === 'active');
        $paymentmaster->active = $request->active === 'active';
        $paymentmaster->paymentstatus = $request->paymentstatus;
        $paymentmaster->save();
    }
}
