<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
	const KEY = "key";
	const VALUE = "value";

	const WEB_TITLE = "web-title";
	const WEB_DESC = "web-desc";
	const WEB_KEYWORD = "web-keyword";

	const WEB_PENALTY = "web-penalty";
 	const WEB_METHOD = "web-method";
	const WEB_METHODADD = "web-methodadd";
 	const WEB_PERCENT = "web-percent";
 	const WEB_REMARK = "web-remark";
 	const WEB_BANK01 = "web-bank01";
 	const WEB_BANK02 = "web-bank02";
	const WEB_BANK03 = "web-bank03";

	const WEB_TERMS = "web-terms";

	const WEB_TAXID = "web-taxid";
	const WEB_BARCODE = "web-barcode";
	const WEB_QRCODE = "web-qrcode";

	protected $fillable = [Settings::KEY];
}
