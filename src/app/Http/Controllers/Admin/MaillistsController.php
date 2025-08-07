<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Maillists;
use App\Models\Paymentdetails;
use App\Models\Paymentmasters;
use App\Models\Settings;
use App\Models\Students;
use App\Models\Templates;
use Carbon\Carbon;
use File;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Screen\Capture;

class MaillistsController extends AdminController
{
    public function index()
    {
        $maillists = Maillists::orderBy('updated_at', 'desc')->get();

        return view('admin.maillist', [
            'maillists' => $maillists,
        ]);
    }

    public function genmail()
    {
        // File::requireOnce('Rundiz/Number/NumberEng.php');
        require 'Rundiz/Number/NumberEng.php';

        $mailcount = 0;
        $response = ['status' => 'success'];
        $paymentmaster = Paymentmasters::where('paymentstatus', 1)->get();
        if ($paymentmaster->count() > 0) {
            foreach ($paymentmaster as $key => $value) {
                $subtotal = 0;
                $grandtotal = 0;

                $student = Students::where('custcode', $value->custcode)->first();
                $template = Templates::find($value->templateno);
                $paymentdetail = Paymentdetails::where('docuno_id', $value->docuno)->get();

                $penalty = Settings::firstOrCreate([Settings::KEY => Settings::WEB_PENALTY]);
                $method = Settings::firstOrCreate([Settings::KEY => Settings::WEB_METHOD]);
                // $methodadd = Settings::firstOrCreate([Settings::KEY => Settings::WEB_METHODADD]);
                $bank01 = Settings::firstOrCreate([Settings::KEY => Settings::WEB_BANK01]);
                $bank02 = Settings::firstOrCreate([Settings::KEY => Settings::WEB_BANK02]);
                $bank03 = Settings::firstOrCreate([Settings::KEY => Settings::WEB_BANK03]);

                $taxid = Settings::firstOrCreate([Settings::KEY => Settings::WEB_TAXID]);
                $barcode = Settings::firstOrCreate([Settings::KEY => Settings::WEB_BARCODE]);
                $qrcode = Settings::firstOrCreate([Settings::KEY => Settings::WEB_QRCODE]);

                if (! is_null($student) && ! is_null($paymentdetail) && ! is_null($template)) {
                    // GENERATE EXCEL -------------------------------------------------------- //
                    $fileName = preg_replace('/\s+/', '', $value->custcode).'_'.str_replace(' ', '-', trim(preg_replace('/[^a-z0-9_ ]/i', '', $value->custnameeng))).'_'.preg_replace('/\s+/', '', $value->docuno);
                    // Excel::load(config('setting.template-path'). 'invoice.xls',
                    /*function ($excel) use ($student, $value, $paymentdetail, $penalty, $method, $bank01, $bank02, $bank03) {

                        $studentname = $value->custnameeng;
                        //$studentnameindex = strpos($studentname, "-", 0);
                        //if($studentnameindex) {
                        //	$studentname = substr($studentname, $studentnameindex + 1);
                        //}
                        $sheet = $excel->getActiveSheet();
                    $sheet->setCellValue('B4', $student->contfax);
                    $sheet->setCellValue('D5', $student->contactname);
                    $sheet->setCellValue('D6', $student->custadd);
                    $sheet->setCellValue('J5', $value->docuno);
                    $sheet->setCellValue('L40', $value->docuno);
                        $sheet->setCellValue('M5', Carbon::parse($value->docudate)->format('d F Y'));
                        $sheet->setCellValue('L7', Carbon::parse($value->shipdate)->format('d F Y'));
                    $sheet->setCellValue('D8', '#'. $value->custcode. ' '. $studentname);
                        $sheet->setCellValue('I8', $penalty->value);

                        $number_text = new \Rundiz\Number\NumberEng();
                        // list
                        $loop = 10;
                        $grandtotal = 0;
                        foreach ($paymentdetail as $key => $objdetail) {
                            $loop++;
                            $subtotal = (string)($objdetail->rematotalamnt);
                            $grandtotal = $grandtotal + $subtotal;

                            $pos1 = 'B'. $loop;
                            $pos2 = 'E'. $loop;
                            $pos3 = 'M'. $loop;
                            $sheet->setCellValue($pos1, $objdetail->goodcode);
                            $sheet->setCellValue($pos2, $objdetail->goodnameeng1);
                            $sheet->setCellValue($pos3, $subtotal);
                        }

                        // Method of payment with line feed
                        $methodpayment = str_replace(config('setting.linefeed'), '', $method->value);
                        $text_total = '('. strtoupper($number_text->convertNumber($grandtotal)). ')';

                        $sheet->setCellValue('M26', $grandtotal);
                        $sheet->setCellValue('B26', $text_total);
                        $sheet->setCellValue('B44', $text_total);
                        $sheet->setCellValue('B28', $value->remark);
                        $sheet->setCellValue('L38', $studentname);
                        $sheet->setCellValue('M39', $value->custcode);
                        $sheet->setCellValue('B32', $methodpayment);
                        //$sheet->setCellValue('B33', $methodadd->value);
                        if ($bank01->value != ''){
                            $sheet->setCellValue('C38', $bank01->value);
                        } else {
                            $sheet->setCellValue('B38', '');
                            $sheet->setCellValue('C38', '');
                        }
                        if ($bank02->value != ''){
                            $sheet->setCellValue('C39', $bank02->value);
                        } else {
                            $sheet->setCellValue('B39', '');
                            $sheet->setCellValue('C39', '');
                        }
                        if ($bank03->value != ''){
                            $sheet->setCellValue('C40', $bank03->value);
                        } else {
                            $sheet->setCellValue('B40', '');
                            $sheet->setCellValue('C40', '');
                        }
                    })->setFilename($fileName)->store('xls', config('setting.excel-path'), true);
                    chmod(config('setting.excel-path'). $fileName. ".xls", 0777);*/
                    // GENERATE EXCEL -------------------------------------------------------- //

                    // GENERATE JPG ---------------------------------------------------------- //
                    /*$url = Helper::url('invoice'). '/'. $student->id. "/". $value->id;
                    $screenCapture = new Capture();
                    $screenCapture->setImageType('jpg');
                    $screenCapture->setUrl($url);
                    $screenCapture->setWidth(1024);
                    $screenCapture->setHeight(600);
                    $screenCapture->setBackgroundColor('#ffffff');

                    $imageLocation = config('setting.image-path'). $fileName. ".jpg";
                    $screenCapture->save($imageLocation);
                    chmod($imageLocation, 0777);*/
                    // GENERATE JPG ---------------------------------------------------------- //

                    // GENERATE PDF ---------------------------------------------------------- //
                    $studentname = $value->custnameeng;
                    // $studentnameindex = strpos($studentname, "-", 0);
                    // if($studentnameindex) {
                    //  $studentname = substr($studentname, $studentnameindex + 1);
                    // }
                    // list
                    $grandtotal = 0;
                    foreach ($paymentdetail as $key => $objdetail) {
                        $subtotal = (string) ($objdetail->rematotalamnt);
                        $grandtotal = $grandtotal + $subtotal;
                    }
                    $number_text = new \Rundiz\Number\NumberEng;
                    $text_total = '('.strtoupper($number_text->convertNumber(floatval($grandtotal))).' ONLY)';

                    // Method of payment with line feed
                    $methodpayment = str_replace(config('setting.linefeed'), '<br \>', $method->value);

                    $totaltostring = (string) (number_format($grandtotal, 2));
                    $totaltostring = str_replace(',', '', $totaltostring);
                    $totaltostring = str_replace('.', '', $totaltostring);

                    // Generate barcode and qrcode
                    $barcode_gen = '';
                    $qrcode_gen = '';
                    $displaybarcodestr = $taxid->value.'&nbsp;'.$value->custcode.'&nbsp;'.$value->docuno.'&nbsp;'.$totaltostring;

                    if ($barcode->value == '1') {
                        $barcode_gen = $taxid->value."\r".$value->custcode."\r".$value->docuno."\r".$totaltostring;
                    }
                    if ($qrcode->value == '1') {
                        $qrcode_gen = $taxid->value.'%0D'.$value->custcode.'%0D'.$value->docuno.'%0D'.$totaltostring;
                    }

                    $data = [
                        'contfax' => $student->contfax,
                        'contactname' => $student->contactname,
                        'custadd' => $student->custadd,
                        'docuno' => $value->docuno,
                        'docudate' => Carbon::parse($value->docudate)->format('d F Y'),
                        'shipdate' => Carbon::parse($value->shipdate)->format('d F Y'),
                        'custcode' => '#'.$value->custcode.' '.$studentname,
                        'penalty' => $penalty->value,
                        'grandtotal' => $grandtotal,
                        'text_total' => $text_total,
                        'remark' => $value->remark,
                        'studentname' => $studentname,
                        'custcode_' => $value->custcode,
                        'paymentdetail' => $paymentdetail,
                        'method' => $methodpayment,
                        // 'methodadd' => $methodadd->value,
                        'bank01' => $bank01->value,
                        'bank02' => $bank02->value,
                        'bank03' => $bank03->value,
                        'barcode_gen' => $barcode_gen,
                        'qrcode_gen' => $qrcode_gen,
                        'displaybarcodestr' => $displaybarcodestr,
                    ];
                    $urlview = 'home.invoice';
                    PDF::loadView($urlview, $data, [], [])->save(config('setting.pdf-path').$fileName.'.pdf');
                    chmod(config('setting.pdf-path').$fileName.'.pdf', 0777);
                    // GENERATE PDF ---------------------------------------------------------- //

                    // GENERATE MAILLIST ----------------------------------------------------- //
                    $password = config('setting.encrypt-pass');
                    $encryptiontype = config('setting.encrypt-type');
                    $encryptionstr = openssl_encrypt('{"custcode":"'.$value->custcode.'","custnameeng":"'.$value->custnameeng.'","docuno":"'.$value->docuno.'"}', $encryptiontype, $password);
                    $encryptionstr = bin2hex($encryptionstr);
                    $link = Helper::url('payment').'/'.$encryptionstr;

                    $custname = $value->custnameeng;
                    // $custnameindex = strpos($custname, "-", 0);
                    // if($custnameindex) {
                    //	$custname = substr($custname, $custnameindex + 1);
                    // }

                    $body = str_replace('{J-contfax}', $student->contfax, $template->mailbody);
                    $body = str_replace('{O-contactname}', $student->contactname, $body);
                    $body = str_replace('{F-CustAdd}', $student->custadd, $body);
                    $body = str_replace('{A-docuno}', $value->docuno, $body);
                    $body = str_replace('{C-docudate}', Carbon::parse($value->docudate)->format('d F Y'), $body);
                    $body = str_replace('{D-ShipDate}', Carbon::parse($value->shipdate)->format('d F Y'), $body);
                    $body = str_replace('{D-shipdate}', $value->shipdate, $body);
                    $body = str_replace('{N-CustName}', $custname, $body);
                    $body = str_replace('{N-custnameeng}', $custname, $body);
                    $body = str_replace('{Config-Penalty}', Config::get('excel.genmail.penalty'), $body);
                    $body = str_replace('{SUM-Total}', $english_format_number = number_format($grandtotal, 2), $body);
                    $body = str_replace('{SUM-TotalAmount}', $grandtotal, $body);
                    $body = str_replace('{Z-Remark}', $value->remark, $body);
                    $body = str_replace('{L-custcode}', $value->custcode, $body);
                    $body = str_replace('{PaymentGateway-Invoice-Link}', $link, $body);

                    $mail = new Maillists;
                    $mail->mailfrom = $template->mailfrom;
                    $mail->mailreplyto = $template->mailreplyto;
                    $mail->mailto = $student->contemail;
                    $mail->mailcc = $template->mailcc;
                    $mail->mailsubject = str_replace('{student-id}', $value->custcode, $template->mailsubject);
                    $mail->mailbody = $body;
                    $mail->docuno = $value->docuno;
                    $mail->docudate = $value->docudate;
                    $mail->shipdate = $value->shipdate;
                    $mail->custcode = $value->custcode;
                    $mail->custname = $custname;
                    $mail->templates_id = $value->templateno;
                    $mail->remark = $value->remark;
                    $mail->sumtotal = $grandtotal;
                    $mail->attach_pdf = $fileName.'.pdf';
                    $mail->attach_jpg = $fileName.'.jpg';
                    $mail->active = true;
                    $mail->save();

                    // update paymentstatus
                    $value->paymentstatus = 2;
                    $value->save();
                    $mailcount++;
                    // GENERATE MAILLIST ----------------------------------------------------- //
                }
            }
        }

        $response['data'] = $mailcount;

        return response()->json($response);
    }

    public function sendmail(Request $request)
    {

        // File::requireOnce('mail/PHPMailerAutoload.php');

        $mailcount = 0;
        $mailerror = [];
        $response = ['status' => 'error', 'msg' => 'Data not found.'];
        if (! is_null($request->ids)) {
            $response = ['status' => 'success'];
            $arr_length = count($request->ids);
            for ($i = 0; $i < $arr_length; $i++) {
                $id = intval($request->ids[$i]);
                $maillist = Maillists::find($id);
                if (is_null($maillist)) {
                    $mailerror[count($mailerror)] = 'Error '.$id.' : mail data not found.';

                    continue;
                }
                $paymentmaster = Paymentmasters::where('docuno', $maillist->docuno)->first();
                if (is_null($paymentmaster)) {
                    $mailerror[count($mailerror)] = 'Error '.$id.' : payment data not found.';

                    continue;
                }

                $mail_from = $maillist->mailfrom;
                $mail_to = explode(',', $maillist->mailto);
                $mail_reply = $maillist->mailreplyto;
                $mail_cc = $maillist->mailcc;

                // SET THIS FOR TEST
                // $mail_from = "jostatus@yahoo.com";
                // $mail_to = explode(',', 'jostatusziz@gmail.com, jostatus@hotmail.com');
                // $mail_reply = "jostatusziz@gmail.com";
                // $mail_cc = "jostatus@yahoo.com";

                $mail_title = $maillist->mailsubject;
                $mail_attachpdf = Helper::url().'/'.config('setting.pdf-path').$maillist->attach_pdf;
                $mail_attachjpg = Helper::url().'/'.config('setting.image-path').$maillist->attach_jpg;

                // Comment : Thongchai.l 2020/06/02 18:05
                // Fixed Error cannot find root https >>> to upload server path

                // $mailerror[count($mailerror)] = "Error ". str_replace('https://paymentgateway.kis.ac.th','.', $mail_attachpdf);
                $mail_attachpdf = str_replace('https://paymentgateway.kis.ac.th', '.', $mail_attachpdf);

                if ($mail_to != '') {
                    /*$mail = new \PHPMailer();
                    $mail->CharSet = 'utf-8';
                    $mail->Debugoutput = 'html';
                    $mail->isSMTP();
                    $mail->SMTPDebug = 0;
                    $mail->isHTML(true);
                    $mail->Host     = 'smtp.gmail.com'; //env('MAIL_HOST');
                    $mail->Username = env('MAIL_USERNAME');
                    $mail->Password = env('MAIL_PASSWORD');

                    $mail->Port       = 587; //env('MAIL_PORT');;
                    $mail->SMTPSecure = 'tls'; //env('MAIL_ENCRYPTION');
                    $mail->SMTPAuth   = true;

                    $mail->AddAddress($mail_to); // name is optional
                    $mail->addReplyTo($mail_reply, "Reply-To: KIS Mail");
                    $mail->addCC($mail_cc);
                    $mail->addAttachment($mail_attachpdf);     // Add attachments
                    $mail->addAttachment($mail_attachjpg);    // Optional name
                    $mail->From     = $mail_from;
                    $mail->FromName = "KIS Mail";

                    $mail->Subject  = $maillist->mailsubject;
                    $mail->Body     = $maillist->mailbody;*/
                    // $mail->Send();

                    Mail::send('email.send', ['title' => $mail_title, 'body' => $maillist->mailbody],
                        function ($message) use ($maillist, $mail_to, $mail_cc, $mail_reply, $mail_attachpdf) {
                            $message->subject($maillist->mailsubject);
                            $message->from($maillist->mailfrom, 'kisfinance');
                            $message->to($mail_to)->cc($mail_cc)->replyTo($mail_reply, $name = null);
                            $message->attach($mail_attachpdf, ['mime' => 'application/pdf']);
                            // $message->attach($mail_attachjpg, ['mime' => 'image/jpeg']);
                        });

                } else {
                    $mailerror[count($mailerror)] = 'Error '.'Destination email not found.';
                }

                $mailcount++;
                $paymentmaster->paymentstatus = 3;
                $paymentmaster->save();

                // $maillist->send_status = 1;
                // $maillist->save();

                // delete file attachment
                File::delete(config('setting.pdf-path').$maillist->attach_pdf, config('setting.excel-path').substr($maillist->attach_pdf, 0, -4).'.xls');

                // delte maillist
                Maillists::destroy($id);

            }
        }
        $response['data'] = $mailcount;
        $response['mailerror'] = $mailerror;

        return response()->json($response);
    }

    public function getfile(Request $request) {}

    public function create() {}

    public function store(Request $request) {}

    public function edit($id) {}

    public function update(Request $request, $id) {}

    public function destroy($id)
    {
        $count = 0;
        $mail = Maillists::find($id);
        if (! is_null($mail)) {
            $paymentmaster = Paymentmasters::where('docuno', $mail->docuno)->first();
            if (! is_null($paymentmaster)) {
                $paymentmaster->paymentstatus = 1;
                $paymentmaster->save();
            }
            $count = Maillists::destroy($id);
        }

        return $count == 1 ? $id : -1;
    }

    private function doValidate(Request $request)
    {
        $validate = [];
        $this->validate($request, $validate);
    }

    private function doSave(Request $request, $lists) {}
}
