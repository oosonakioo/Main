<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Paymentdetails;
use App\Models\Paymentmasters;
use Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentDetailsController extends AdminController
{
    public function index(): View
    {
        $paymentdetail = Paymentdetails::where('active', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        $payment = Paymentmasters::where('paymentstatus', '<', 6)
            ->where('active', true)
            ->orderBy('custnameeng', 'asc')
            ->get();

        return view('admin.paymentdetail', [
            'payment' => $payment,
            'paymentdetail' => $paymentdetail,
        ]);
    }

    public function create(): View
    {
        $paymentdetail = new Paymentdetails;
        $payment = Paymentmasters::where('active', true)
            ->orderBy('custnameeng', 'asc')
            ->get();

        return view('admin.paymentdetail-create', [
            'payment' => $payment,
            'paymentdetail' => $paymentdetail,
        ]);
    }

    public function save(Request $request): JsonResponse
    {
        $data_ok = true;

        // $this->doValidate($request);
        if ($request->docuno_id > 0 && $request->listno > 0 && $request->goodprice2 != 'null' && $request->goodqty2 > 0 && $request->goodcode != '' && $request->goodnameeng1 != '' && $request->rematotalamnt != 'null' && $request->gooddiscamnt >= 0) {
            // OK
        } else {
            $response = ['status' => 'error', 'msg' => 'Incomplete information'];
            $data_ok = false;
        }

        if ($data_ok) {
            $hasMaster = Paymentmasters::where('docuno', $request->docuno_id);
            if ($hasMaster->count() == 0) {
                $response = ['status' => 'error', 'msg' => 'Payment not found.'];
            } else {
                $hasData = Paymentdetails::where('docuno_id', $request->docuno_id)->where('listno', $request->listno);
                if ($hasData->count() == 0) {
                    $paymentdetail = new Paymentdetails;
                    $this->doSave($request, $paymentdetail);
                } else {
                    $paymentdetail = Paymentdetails::find($hasData->first()->id);
                    if ($hasData->first()->active) {
                        $request->active = 'active';
                    }
                    $this->doSave($request, $paymentdetail);
                }
                $response = ['status' => 'success'/* , 'json' => $json */];
            }
        }

        return response()->json($response);
    }

    public function store(Request $request)
    {
        $redirect = 'admin/paymentdetail';

        $this->doValidate($request);
        $paymentdetail = new Paymentdetails;
        $this->doSave($request, $paymentdetail);

        return Helper::redirect($redirect);
    }

    public function edit($id): View
    {
        $paymentdetail = Paymentdetails::find($id);
        $payment = Paymentmasters::where('active', true)
            ->orderBy('custnameeng', 'asc')
            ->get();

        return view('admin.paymentdetail-create', [
            'payment' => $payment,
            'paymentdetail' => $paymentdetail,
        ]);
    }

    public function update(Request $request, $id)
    {
        $redirect = 'admin/paymentdetail';

        $this->doValidate($request);
        $paymentdetail = Paymentdetails::find($id);
        $this->doSave($request, $paymentdetail);

        return Helper::redirect($redirect);
    }

    public function destroy($id)
    {
        $count = Paymentdetails::destroy($id);

        return $count == 1 ? $id : -1;
    }

    private function doValidate(Request $request)
    {
        $validate = [];
        $this->validate($request, $validate);
    }

    private function doSave(Request $request, $paymentdetail)
    {
        $paymentdetail->docuno_id = $request->docuno_id;
        $paymentdetail->listno = $request->listno;
        $paymentdetail->goodprice2 = $request->goodprice2;
        $paymentdetail->goodqty2 = $request->goodqty2;
        $paymentdetail->goodcode = $request->goodcode;
        $paymentdetail->goodnameeng1 = $request->goodnameeng1;
        $paymentdetail->rematotalamnt = $request->rematotalamnt;
        $paymentdetail->gooddiscamnt = $request->gooddiscamnt;
        $paymentdetail->active = ($request->active === 'active');
        // $paymentdetail->active = $request->active;
        $paymentdetail->save();
    }
}
