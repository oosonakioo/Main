<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Paymentmasters;
use App\Models\Paymentdetails;
use App\Models\Students;
use App\Models\Maillists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Helper;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use DateTime;

class ReportsController extends AdminController
{
	public function index()
	{
		$paymentmaster = Paymentmasters::where('paymentstatus', 4)
			->orderBy('updated_at', 'desc')
			->get();

		return view('admin.report', [
			'paymentmaster' 	=> $paymentmaster,
		]);
	}

	public function create()
	{

	}

	public function save(Request $request)
	{

	}

	public function store(Request $request)
	{

	}

	public function edit($id)
	{

	}

	public function update(Request $request, $id)
	{

	}

	public function destroy($id)
	{

	}

	private function doValidate(Request $request)
	{
		$validate = [];
		$this->validate($request, $validate);
	}

	private function doSave(Request $request, $paymentmaster)
	{

	}


	public function export(Request $request)
	{
		$datemin = $request->datemin;
		$datemax = $request->datemax;
		$min = $request->min;
		$max = $request->max;
		$limit = $request->tableresult_length;
		$sortcol = $request->sortcol;
		$sortdir = $request->sortdir;

		$loop = 2;
		$datereport = "N/A";
		$idreport = "N/A";
		$datenow = Helper::thai_date(strtotime(date('Y-m-d')), "onlydate"). ' '. date("h:i:sa");

		switch ($sortcol) {
			case 0:
				$sortcol = "updated_at";
				break;
			//case 1:
			//	$sortcol = "updated_at";
			//	break;
			case 2:
				$sortcol = "docuno";
				break;
			case 3:
				$sortcol = "custcode";
				break;
			case 4:
				$sortcol = "custnameeng";
				break;
			//case 5:
			//	$sortcol = "updated_at";
			//	break;
		}

		$query = Paymentmasters::where('paymentstatus', 4);
		if ($datemin != "") {
			$datereport = Helper::thai_date(strtotime($datemin), "onlydate");
			$datemin = date('Y-m-d', strtotime($datemin));
			$query->where('updated_at', '>=', $datemin);
		}
		if ($datemax != "") {
			$datereport = $datereport. " - ". Helper::thai_date(strtotime($datemax), "onlydate");
			$datemax = date('Y-m-d', strtotime($datemax));

			$datemodify = new DateTime($datemax);
			$datemodify->modify('+1 day');

			$query->where('updated_at', '<', $datemodify);
		}
		if ($min != "") {
			$idreport = $min;
			$query->where('custcode', '>=', $min);
		}
		if ($max != "") {
			$idreport = $idreport. " - ". $max;
			$query->where('custcode', '<=', $max);
		}
		if($limit > 0) {
			$query->limit($limit);
		}
		if ($sortcol != "") {
			$query->orderBy($sortcol, $sortdir);
		}

		$data = array();

		// Header
		$col01 = "Payment Date";
		$col02 = "Payment Time";
		$col03 = "Document No";
		$col04 = "Student ID";
		$col05 = "Student Name";
		$col06 = "Total Price";
		$data[] = [$col01, $col02, $col03, $col04, $col05, $col06];

		//DB::enableQueryLog();
		// Row data
		$reportList = $query->get();
		//dd(DB::getQueryLog());

		foreach ($reportList as $value) {
			$loop++;
			$data[] = [Helper::thai_date(strtotime($value->updated_at), "onlydate"),
				Helper::thai_date(strtotime($value->updated_at), "onlytime"),
				$value->docuno,
				$value->custcode,
				$value->custnameeng,
				$english_format_number = number_format($value->price->sum('rematotalamnt'), 2)];
		}
		$data[] = ["",
			"",
			"",
			"",
			"",
			""];
		$data[] = ["Report Date",
			$datenow,
			"",
			"",
			"",
			""];
		$data[] = ["Payment Date",
			$datereport,
			"",
			"",
			"",
			""];
		$data[] = ["Student ID",
			$idreport,
			"",
			"",
			"",
			""];

		// Remove all data, which generated to excel
		//$query->delete();
		$loop++;
		$row01 = 'B'. $loop. ':F'. $loop;
		$loop++;
		$row02 = 'B'. $loop. ':F'. $loop;
		$loop++;
		$row03 = 'B'. $loop. ':F'. $loop;

		// Export to excel
		$fileName = 'report_'.date('YmdHi');
		Excel::create($fileName, function ($excel) use ($data, $row01, $row02, $row03, $loop) {
			$excel->sheet('Report', function ($sheet) use ($data, $row01, $row02, $row03, $loop) {
				$sheet->fromArray($data, null, 'A1', false, false);
				$sheet->row(1, function ($row) {
					$row->setFontWeight('bold');
				});
				$sheet->setPageMargin(array(
						0.25, 0.30, 0.25, 0.30
				));
				$sheet->setBorder('A1:F'. $loop, 'thin');
				$sheet->mergeCells($row01);
				$sheet->mergeCells($row02);
				$sheet->mergeCells($row03);
				$sheet->freezeFirstRow();
			});
		})->download('xlsx');
	}
}
