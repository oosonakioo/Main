<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Settings;
use Helper;
use Illuminate\Http\Request;

class SettingController extends AdminController
{
    public function index()
    {
        $title = Settings::firstOrCreate([Settings::KEY => Settings::WEB_TITLE]);
        $desc = Settings::firstOrCreate([Settings::KEY => Settings::WEB_DESC]);
        $keyword = Settings::firstOrCreate([Settings::KEY => Settings::WEB_KEYWORD]);

        $penalty = Settings::firstOrCreate([Settings::KEY => Settings::WEB_PENALTY]);
        $method = Settings::firstOrCreate([Settings::KEY => Settings::WEB_METHOD]);
        // $methodadd = Settings::firstOrCreate([Settings::KEY => Settings::WEB_METHODADD]);
        $remark = Settings::firstOrCreate([Settings::KEY => Settings::WEB_REMARK]);
        $bank01 = Settings::firstOrCreate([Settings::KEY => Settings::WEB_BANK01]);
        $bank02 = Settings::firstOrCreate([Settings::KEY => Settings::WEB_BANK02]);
        $bank03 = Settings::firstOrCreate([Settings::KEY => Settings::WEB_BANK03]);
        $percent = Settings::firstOrCreate([Settings::KEY => Settings::WEB_PERCENT]);
        $terms = Settings::firstOrCreate([Settings::KEY => Settings::WEB_TERMS]);

        $taxid = Settings::firstOrCreate([Settings::KEY => Settings::WEB_TAXID]);
        $barcode = Settings::firstOrCreate([Settings::KEY => Settings::WEB_BARCODE]);
        $qrcode = Settings::firstOrCreate([Settings::KEY => Settings::WEB_QRCODE]);

        return view('admin.settings', [
            'title' => $title->value,
            'desc' => $desc->value,
            'keyword' => $keyword->value,
            'penalty' => $penalty->value,
            'method' => $method->value,
            // 'methodadd' => $methodadd->value,
            'remark' => $remark->value,
            'bank01' => $bank01->value,
            'bank02' => $bank02->value,
            'bank03' => $bank03->value,
            'percent' => $percent->value,
            'terms' => $terms->value,
            'taxid' => $taxid->value,
            'barcode' => $barcode->value,
            'qrcode' => $qrcode->value,
        ]);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'desc' => 'required',
            'keyword' => 'required',
        ]);

        $title = Settings::firstOrNew([Settings::KEY => Settings::WEB_TITLE]);
        $title->value = $request->title;
        $title->save();

        $desc = Settings::firstOrNew([Settings::KEY => Settings::WEB_DESC]);
        $desc->value = $request->desc;
        $desc->save();

        $keyword = Settings::firstOrNew([Settings::KEY => Settings::WEB_KEYWORD]);
        $keyword->value = $request->keyword;
        $keyword->save();

        $penalty = Settings::firstOrNew([Settings::KEY => Settings::WEB_PENALTY]);
        $penalty->value = $request->penalty;
        $penalty->save();

        $method = Settings::firstOrNew([Settings::KEY => Settings::WEB_METHOD]);
        $method->value = $request->method;
        $method->save();

        // $methodadd = Settings::firstOrNew([Settings::KEY => Settings::WEB_METHODADD]);
        // $methodadd->value = $request->methodadd;
        // $methodadd->save();

        $remark = Settings::firstOrNew([Settings::KEY => Settings::WEB_REMARK]);
        $remark->value = $request->remark;
        $remark->save();

        $percent = Settings::firstOrNew([Settings::KEY => Settings::WEB_PERCENT]);
        $percent->value = $request->percent;
        $percent->save();

        $bank01 = Settings::firstOrNew([Settings::KEY => Settings::WEB_BANK01]);
        $bank01->value = $request->bank01;
        $bank01->save();

        $bank02 = Settings::firstOrNew([Settings::KEY => Settings::WEB_BANK02]);
        $bank02->value = $request->bank02;
        $bank02->save();

        $bank03 = Settings::firstOrNew([Settings::KEY => Settings::WEB_BANK03]);
        $bank03->value = $request->bank03;
        $bank03->save();

        $terms = Settings::firstOrNew([Settings::KEY => Settings::WEB_TERMS]);
        $terms->value = $request->terms;
        $terms->save();

        $taxid = Settings::firstOrNew([Settings::KEY => Settings::WEB_TAXID]);
        $taxid->value = $request->taxid;
        $taxid->save();

        $barcode = Settings::firstOrNew([Settings::KEY => Settings::WEB_BARCODE]);
        $barcode->value = ($request->barcode === 'active');
        $barcode->save();

        $qrcode = Settings::firstOrNew([Settings::KEY => Settings::WEB_QRCODE]);
        $qrcode->value = ($request->qrcode === 'active');
        $qrcode->save();

        return Helper::redirect('admin/settings');
    }
}
