<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Helper;
use Illuminate\Http\Request;
use App\Models\Students;
use App\Models\Paymentmasters;
use App\Models\Paymentdetails;
use App\Models\Settings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class HomeController extends Controller
{
    public function index()
    {

      return view('home.index', [

      ]);
    }


    public function invoice($studentid, $masterid)
    {
      require 'Rundiz/Number/NumberEng.php';

      $grandtotal = 0;
      $penalty = Settings::firstOrCreate([Settings::KEY => Settings::WEB_PENALTY]);
      $method = Settings::firstOrCreate([Settings::KEY => Settings::WEB_METHOD]);
      $methodadd = Settings::firstOrCreate([Settings::KEY => Settings::WEB_METHODADD]);
      $bank01 = Settings::firstOrCreate([Settings::KEY => Settings::WEB_BANK01]);
      $bank02 = Settings::firstOrCreate([Settings::KEY => Settings::WEB_BANK02]);
      $bank03 = Settings::firstOrCreate([Settings::KEY => Settings::WEB_BANK03]);

      $taxid = Settings::firstOrCreate([Settings::KEY => Settings::WEB_TAXID]);
      $barcode = Settings::firstOrCreate([Settings::KEY => Settings::WEB_BARCODE]);
      $qrcode = Settings::firstOrCreate([Settings::KEY => Settings::WEB_QRCODE]);

      $student = Students::where('id', $studentid)->get();
      $paymentmaster = Paymentmasters::where('id', $masterid)->get();
      $paymentdetail = Paymentdetails::where('docuno_id', $paymentmaster[0]->docuno)->get();

      $studentname = $paymentmaster[0]->custnameeng;
      //$studentnameindex = strpos($studentname, "-", 0);
      //if($studentnameindex) {
      //  $studentname = substr($studentname, $studentnameindex + 1);
      //}

      foreach ($paymentdetail as $key => $objdetail) {
        $subtotal = (string)($objdetail->rematotalamnt);
        $grandtotal = $grandtotal + $subtotal;
      }

      $number_text = new \Rundiz\Number\NumberEng();
      $text_total = '('. strtoupper($number_text->convertNumber(floatval($grandtotal))). ' ONLY)';

      // Method of payment with line feed
      $methodpayment = str_replace(config('setting.linefeed'), '<br \>', $method->value);

      $totaltostring = (string)(number_format($grandtotal, 2));
      $totaltostring = str_replace(",", "", $totaltostring);
      $totaltostring = str_replace(".", "", $totaltostring);

      // Generate barcode and qrcode
      $barcode_gen = "";
      $qrcode_gen = "";
      $displaybarcodestr = $taxid->value. '&nbsp;'. $paymentmaster[0]->custcode. '&nbsp;'. $paymentmaster[0]->docuno. '&nbsp;'. $totaltostring;

      if ($barcode->value == "1") {
        $barcode_gen = $taxid->value. "\r". $paymentmaster[0]->custcode. "\r". $paymentmaster[0]->docuno. "\r". $totaltostring;
      }
      if ($qrcode->value == "1") {
        $qrcode_gen = $taxid->value. "%0D". $paymentmaster[0]->custcode. "%0D". $paymentmaster[0]->docuno. "%0D". $totaltostring;
      }

      return view('home.invoice', [
        'contfax' => $student[0]->contfax,
        'contactname' => $student[0]->contactname,
        'custadd' => $student[0]->custadd,
        'docuno'  => $paymentmaster[0]->docuno,
        'docudate'  => Carbon::parse($paymentmaster[0]->docudate)->format('d F Y'),
        'shipdate'  => Carbon::parse($paymentmaster[0]->shipdate)->format('d F Y'),
        'custcode'  => '#'. $paymentmaster[0]->custcode. ' '. $studentname,
        'penalty'   => $penalty->value,
        'grandtotal' => $grandtotal,
        'text_total'  => $text_total,
        'remark'  => $paymentmaster[0]->remark,
        'studentname' => $studentname,
        'custcode_'  => $paymentmaster[0]->custcode,
        'paymentdetail' => $paymentdetail,
        'method' => $methodpayment,
        'bank01' => $bank01->value,
        'bank02' => $bank02->value,
        'bank03' => $bank03->value,
        'barcode_gen' => $barcode_gen,
        'qrcode_gen'  => $qrcode_gen,
        'displaybarcodestr'  => $displaybarcodestr,
      ]);
    }


    public function payment($encryptionstr)
    {
        require 'Rundiz/Number/NumberEng.php';

        // Terms and Conditions
        $terms = Settings::firstOrCreate([Settings::KEY => Settings::WEB_TERMS]);

        $remark = Settings::firstOrCreate([Settings::KEY => Settings::WEB_REMARK]);
        $percent = Settings::firstOrCreate([Settings::KEY => Settings::WEB_PERCENT]);

        $encryptionstrtmp = $encryptionstr;
        $encryptionstr = hex2bin($encryptionstrtmp);
        $password = config('setting.encrypt-pass');
        $encryptiontype = config('setting.encrypt-type');
        $decryptionstr = openssl_decrypt($encryptionstr,$encryptiontype,$password);

        $json=(array) json_decode($decryptionstr);
        if (count($json) > 2) {

        } else {
          return view('home.paymenterror', [
              'errormsg' => 'Payment data not found.'
          ]);
        }
        $custcode = $json["custcode"];
        $custnameeng = $json["custnameeng"];
        $docuno = $json["docuno"];

    		$paymentmaster = Paymentmasters::where('paymentstatus', '<', 6)
          ->where('docuno', $docuno)
          ->first();
        if ($paymentmaster === null) {
          return view('home.paymenterror', [
              'errormsg' => 'this link was expired.'
          ]);
        } else {
          $student = Students::where('custcode', $custcode)->first();
          $paymentdetail = Paymentdetails::where('docuno_id', $paymentmaster->docuno)->get();

      		if (!is_null($paymentmaster) && !is_null($student) && !is_null($paymentdetail) && $paymentmaster->paymentstatus >= 3)
          {
            $studentname = $paymentmaster->custnameeng;
            //$studentnameindex = strpos($studentname, "-", 0);
            //if($studentnameindex) {
            //  $studentname = substr($studentname, $studentnameindex + 1);
            //}

            $grandtotal = 0;
            foreach ($paymentdetail as $key => $objdetail) {
              $subtotal = (string)($objdetail->rematotalamnt);
              $grandtotal = $grandtotal + $subtotal;
            }
            $priceadd = ($grandtotal * $percent->value) / 100;
            $totalwithfee = $grandtotal + $priceadd;

            $number_text = new \Rundiz\Number\NumberEng();
            $text_total = '('. ucfirst($number_text->convertNumber(floatval($grandtotal))). ')';
            //$text_total = str_replace('baht', 'Baht', $text_total);

            if($paymentmaster->paymentstatus == 4) {
              return view('home.payment-already', [
                'invoice' => $docuno,
                'duedate' => Carbon::parse($paymentmaster->updated_at)->format('d F Y'),
              ]);
            } else {
               return view('home.payment', [
                'invoice' => $docuno,
                'studentname' => $studentname,
                'duedate' => Carbon::parse($paymentmaster->shipdate)->format('d F Y'),
                'amount' => $grandtotal,
                'amounttext' => $text_total,
                'encryptionstr' => $encryptionstrtmp,
                'remark' => $remark->value,
                'priceadd' => $priceadd,
                'percent' => $percent->value,
                'totalwithfee' => $totalwithfee,
                'custcode'  => $custcode,
                'terms' => $terms->value,
              ]);
            }
          }
          else
          {
            return view('home.paymenterror', [
                'errormsg' => 'this link was expired.'
            ]);
          }
        }
    }


    public function paymentsuccess(Request $request) {

      require 'Rundiz/Number/NumberEng.php';

      $remark = Settings::firstOrCreate([Settings::KEY => Settings::WEB_REMARK]);
      $percent = Settings::firstOrCreate([Settings::KEY => Settings::WEB_PERCENT]);

      $paymentmaster = Paymentmasters::where('paymentstatus', '<', 6)
        ->where('docuno', $request->Ref)
        ->first();

      if ($paymentmaster === null) {
        return view('home.paymenterror', [
            'errormsg' => 'Payment data not found.'
        ]);
      } else {
        $student = Students::where('custcode', $paymentmaster->custcode)->first();
        $paymentdetail = Paymentdetails::where('docuno_id', $paymentmaster->docuno)->get();

        $studentname = $paymentmaster->custnameeng;
        //$studentnameindex = strpos($studentname, "-", 0);
        //if($studentnameindex){
        //  $studentname = substr($studentname, $studentnameindex + 1);
        //}

        $grandtotal = 0;
        foreach ($paymentdetail as $key => $objdetail) {
          $subtotal = (string)($objdetail->rematotalamnt);
          $grandtotal = $grandtotal + $subtotal;
        }
        $priceadd = ($grandtotal * $percent->value) / 100;
        $totalwithfee = $grandtotal + $priceadd;

        $number_text = new \Rundiz\Number\NumberEng();
        $text_total = '('. ucfirst($number_text->convertNumber(floatval($grandtotal))). ')';
        //$text_total = str_replace('baht', 'Baht', $text_total);

        // update status
        $paymentmaster->paymentstatus = 4;
        $paymentmaster->save();

        return view('home.payment-success', [
          'invoice' => $request->Ref,
          'studentname' => $studentname,
          'paiddate' => Carbon::now()->format('d F Y'),
          'amount' => $grandtotal,
          'amounttext' => $text_total,
          'remark' => $remark->value,
          'priceadd' => $priceadd,
          'percent' => $percent->value,
          'totalwithfee' => $totalwithfee,
        ]);
      }
    }

    public function paymentfail(Request $request) {
      return view('home.paymenterror', [
          'errormsg' => 'Payment transaction failed. We received an error processing your card. Please enter your information again or try a different card.'
      ]);
    }


    public function paymentcancel(Request $request) {
      return view('home.paymenterror', [
          'errormsg' => 'Payment transaction failed. We received an error processing your card. Please enter your information again or try a different card.'
      ]);
    }



    /*public function paymentconfirm(Request $request)
    {
        require 'Rundiz/Number/NumberEng.php';

        $remark = Settings::firstOrCreate([Settings::KEY => Settings::WEB_REMARK]);
        $percent = Settings::firstOrCreate([Settings::KEY => Settings::WEB_PERCENT]);

        $encryptionstrtmp = $request->encryptionstr;
        $encryptionstr = hex2bin($encryptionstrtmp);
        $password = config('setting.encrypt-pass');
        $encryptiontype = config('setting.encrypt-type');
        $decryptionstr = openssl_decrypt($encryptionstr, $encryptiontype, $password);

        $json = (array)json_decode($decryptionstr);
        $custcode = $json["custcode"];
        $custnameeng = $json["custnameeng"];
        $docuno = $json["docuno"];

    		$paymentmaster = Paymentmasters::where('docuno', $docuno)->first();
        $student = Students::where('custcode', $custcode)->first();
        $paymentdetail = Paymentdetails::where('docuno_id', $paymentmaster->docuno)->get();

    		if (!is_null($paymentmaster) && !is_null($student) && !is_null($paymentdetail) && $paymentmaster->paymentstatus >= 3)
        {
          $studentname = $paymentmaster->custnameeng;
          $studentnameindex = strpos($studentname, "-", 0);
          if($studentnameindex){
            $studentname = substr($studentname, $studentnameindex + 1);
          }

          $grandtotal = 0;
          foreach ($paymentdetail as $key => $objdetail) {
            $subtotal = (string)($objdetail->rematotalamnt);
            $grandtotal = $grandtotal + $subtotal;
          }
          $priceadd = ($grandtotal * $percent->value) / 100;
          $totalwithfee = $grandtotal + $priceadd;
          //$priceadd = Helper::numberFormatPrecision($priceadd, 2, '.');

          $number_text = new \Rundiz\Number\NumberEng();
          $text_total = '('. ucfirst($number_text->convertNumber($grandtotal)). ')';
          $text_total = str_replace('baht', 'Baht', $text_total);

          if($paymentmaster->paymentstatus == 4)
          {
            return view('home.paymentinfo', [
              'invoice' => $docuno,
              'studentname' => $studentname,
              'paiddate' => Carbon::parse($paymentmaster->updated_at)->format('d F Y'),
              'amount' => $grandtotal,
              'amounttext' => $text_total,
              'remark' => $remark->value,
              'priceadd' => $priceadd,
              'percent' => $percent->value,
              'totalwithfee' => $totalwithfee,
            ]);
          } else {
    				$paymentmaster->paymentstatus = 4;
    				$paymentmaster->save();
            return view('home.payment-confirm', [
              'invoice' => $docuno,
              'studentname' => $studentname,
              'paiddate' => Carbon::now()->format('d F Y'),
              'amount' => $grandtotal,
              'amounttext' => $text_total,
              'remark' => $remark->value,
              'priceadd' => $priceadd,
              'percent' => $percent->value,
              'totalwithfee' => $totalwithfee,
            ]);
          }
        }
        else
        {
          return view('home.paymenterror', [
              'errormsg' => 'Payment data not found.'
          ]);
        }
    }*/
}
