@extends('layouts.admin')

@section('header', trans('admin.settings'))

@section('content')
    <form class="form-horizontal" method="POST" action="{{ url('admin/settings') }}">
        <div class="box box-primary">
            <div class="box-body">

                @include('errors.validator')
                <div class="form-group">
                    <label for="title" class="col-sm-2 control-label">{{ trans('admin.settings-web-name') }}
                        <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="title" name="title" value="{{ $title }}" required/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="desc" class="col-sm-2 control-label">{{ trans('admin.settings-web-desc') }}
                        <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="desc" name="desc" value="{{ $desc }}" required/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="keyword" class="col-sm-2 control-label">{{ trans('admin.settings-web-keyword') }}
                        <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="keyword" name="keyword"
                               placeholder="{{ trans('admin.settings-hint-web-keyword') }}"
                               value="{{ $keyword }}" required/>
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    <label for="penalty" class="col-sm-2 control-label">Late Penalty Charge</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="penalty" name="penalty"
                             placeholder="" value="{{ $penalty }}"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="method" class="col-sm-2 control-label">Method of Payment</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="method" name="method" rows="15">{{ $method }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label"></label>
                  <div class="col-sm-6">
                    <div class="alert alert-info" role="alert">
                      * Use {{ config('setting.linefeed') }} and (ship + enter) for set new line on excel or pdf file.
                    </div>
                  </div>
                </div>
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">Remark</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="remark" name="remark">{{ $remark }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="percent" class="col-sm-2 control-label">Credit Card Fee (%)
                        <span class="text-danger">*</span></label>
                    <div class="col-sm-2">
                        <input type="number" class="form-control" id="percent" name="percent"
                           placeholder="" value="{{ $percent }}" step="0.01" required/>
                    </div>
                </div>

                <hr>
                <div class="form-group">
                    <label for="bank01" class="col-sm-2 control-label">Bank #1 SCB</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="bank01" name="bank01"
                             placeholder="" value="{{ $bank01 }}"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="bank02" class="col-sm-2 control-label">Bank #2 BBL</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="bank02" name="bank02"
                            placeholder="" value="{{ $bank02 }}"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="bank03" class="col-sm-2 control-label nowrap">Bank #3 KBANK</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="bank03" name="bank03"
                            placeholder="" value="{{ $bank03 }}"/>
                    </div>
                </div>

                <hr>
                <div class="form-group">
                    <label for="terms" class="col-sm-2 control-label">Terms & Conditions</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="terms" name="terms" rows="10">{{ $terms }}</textarea>
                    </div>
                </div>

                <hr>
                <div class="form-group">
                    <label for="taxid" class="col-sm-2 control-label">Tax ID</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="taxid" name="taxid"
                             placeholder="" value="{{ $taxid }}"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="barcode">Display Barcode</label>
                    <div class="col-sm-6">
                        <input class="form-control-static" type="checkbox" id="barcode" name="barcode"
                               value="active" {{ Helper::isSelected($barcode, 'checked') }} />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="qrcode">Display QR Code</label>
                    <div class="col-sm-6">
                        <input class="form-control-static" type="checkbox" id="qrcode" name="qrcode"
                               value="active" {{ Helper::isSelected($qrcode, 'checked') }} />
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn bg-olive btn-flat center-block">{{ trans('admin.save') }}</button>
            </div>
        </div>
        {{ csrf_field() }}
    </form>

    <script type="text/javascript">
        $(function () {
            CKEDITOR.replace('terms', {
                language: 'en'
            });
        });
    </script>
@endsection
