<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Models\Tests;
use App\Models\Categories;
use App\Models\CustInfoes;
use App\Models\PaymentMasters;
use App\Models\PaymentDetails;
use Helper;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use DB;

class ImportController extends Controller
{
      public function custinfo()
      {

        return view('import.custInfo', [

        ]);
      }
      public function invoice()
      {

        return view('import.invoice', [

        ]);
      }
      public function uploadCustInfo(Request $request)
      {
          $inputArray = $request->all();
          $json = (array) json_decode($inputArray['json']);
          foreach ($json as $value)
          {
            $hasData= Custinfoes::where('CustCode', $value->CustCode);
            if ($hasData->count() == 0)
            {
                $custinfo = new Custinfoes();
                foreach ($value as $key => $val) {
                    $custinfo->$key = $val;
                }
                $custinfo->active = true;
                $custinfo->save();
            }
            else
            {
                $custinfo = Custinfoes::find($hasData->first()->id);
                foreach ($value as $key => $val) {
                    $custinfo->$key = $val;
                }
                $custinfo->active = true;
                $custinfo->save();
              }
            }
            $response = array ('status' => 'success'/*, 'json' => $json*/);
            return response ()->json ($response);
      }
      public function uploadInvoice(Request $request)
      {
          $inputArray = $request->all();
          $json = (array) json_decode($inputArray['json']);
          foreach ($json as $value)
          {
            $hasMaster= PaymentMasters::where('docuno', $value->docuno);
            $hasDetail= PaymentDetails::where('docuno', $value->docuno);
            if ($hasMaster->count() == 0)
            {
                $master = new PaymentMasters();
                $master->docuno = $value->docuno;
                foreach ($value->PaymentMaster as $key => $val) {
                  // if has nickname
                  /*if ($key == 'custnameeng') {
                    $pos = strpos($val, '-');
                    if ($pos === true) {
                      $value = substr($value, $pos);
                      $value = trim($value);
                    }
                  }*/
                  $master->$key = $val;
                }
                $master->active = true;
                $master->save();
            }
            else
            {
                $master = PaymentMasters::find($hasMaster->first()->id);
                foreach ($value->PaymentMaster as $key => $val) {
                  $master->$key = $val;
                }
                $master->active = true;
                $master->save();
              }
              if ($hasDetail->count() == 0)
              {
                  $detail = new PaymentDetails();
                  $detail->docuno = $value->docuno;
                  foreach ($value->PaymentDetail as $key => $val) {
                      $detail->$key = $val;
                  }
                  $detail->active = true;
                  $detail->save();
              }
              else
              {
                  $detail = PaymentDetails::find($hasDetail->first()->id);
                  foreach ($value->PaymentDetail as $key => $val) {
                      $detail->$key = $val;
                  }
                  $detail->active = true;
                  $detail->save();
                }
            }
          $response = array ('status' => 'success'/*, 'json' => $json*/);
          return response ()->json ($response);
      }
}
