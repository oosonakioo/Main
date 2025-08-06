@extends('layouts.admin')

@section('header', trans('menu.payment-master'))

@section('content')

  <div class="box box-primary">
    <div class="box-body">
      <form class="form-horizontal" enctype="multipart/form-data" method="POST"
            action="{{ $paymentmaster->exists
                      ? Helper::url('admin/payment/'.$paymentmaster->id)
                      : Helper::url('admin/payment')
                  }}">
          <input name="_method" type="hidden" value="{{ $paymentmaster->exists ? 'PUT' : 'POST' }}"/>
          {!! csrf_field() !!}

          @include('errors.validator')

          <div class="form-group">
              <label class="form-control-static col-md-2" for="docuno">Document No <span class="text-danger">*</span></label>
              <div class="col-md-2">
                  <input class="form-control" type="text" id="docuno" name="docuno" maxlength="9"
                         value="{{ Helper::getValue($paymentmaster, 'docuno', $errors) }}" required/>
              </div>
          </div>
          <div class="form-group">
              <label class="form-control-static col-md-2" for="docudate">Document Date <span class="text-danger">*</span></label>
              <div class="col-md-2">
                  <div class="input-group">
                      <input class="form-control datepicker" type="text" id="docudate" name="docudate" readonly
                             value="{{ Helper::datetime($paymentmaster->docudate, 'Y/m/d', true) }}" placeholder="" required/>
                  </div>
              </div>
          </div>
          <div class="form-group">
              <label class="form-control-static col-md-2" for="shipdate">Payment Due Date <span class="text-danger">*</span></label>
              <div class="col-md-2">
                  <div class="input-group">
                      <input class="form-control datepicker" type="text" id="shipdate" name="shipdate" readonly
                             value="{{ Helper::datetime($paymentmaster->shipdate, 'Y/m/d', true) }}" placeholder="" required/>
                  </div>
              </div>
          </div>
          <div class="form-group">
              <label class="form-control-static col-md-2" for="custcode">Student ID <span class="text-danger">*</span></label>
              <div class="col-md-2">
                  <input class="form-control" type="number" id="custcode" name="custcode" maxlength="4"
                         value="{{ Helper::getValue($paymentmaster, 'custcode', $errors) }}" required/>
              </div>
          </div>
          <div class="form-group">
              <label class="form-control-static col-md-2" for="custnameeng">Student Name <span class="text-danger">*</span></label>
              <div class="col-md-6">
                  <input class="form-control" type="text" id="custnameeng" name="custnameeng" maxlength="250"
                         value="{{ Helper::getValue($paymentmaster, 'custnameeng', $errors) }}" required/>
              </div>
          </div>
          <div class="form-group">
              <label class="form-control-static col-md-2" for="templateno">Template ID <span class="text-danger">*</span></label>
              <div class="col-md-2">
                  <input class="form-control" type="number" id="templateno" name="templateno" maxlength="2"
                         value="{{ Helper::getValue($paymentmaster, 'templateno', $errors) }}" required/>
              </div>
          </div>
          <div class="form-group">
              <label class="form-control-static col-md-2" for="remark">Remark</label>
              <div class="col-md-6">
                  <input class="form-control" type="text" id="remark" name="remark" maxlength="250"
                         value="{{ Helper::getValue($paymentmaster, 'remark', $errors) }}" required/>
              </div>
          </div>

          <input type="hidden" id="active" name="active" value="active">
          <input type="hidden" id="paymentstatus" name="paymentstatus" value="{{ Helper::getValue($paymentmaster, 'paymentstatus', $errors) }}">

          <button type="submit" class="btn btn-success center-block">
              <span class="glyphicon glyphicon-ok-circle"></span>
              {{ $paymentmaster->exists ? trans('admin.contents-save') : trans('admin.contents-create') }}
          </button>
      </form>
    </div>
  </div>
@endsection

@section('script')
    <script>
    $(function () {
        $('#docudate').datepicker({
            format: "yyyy/mm/dd",
            language: "en"
        });
        $('#shipdate').datepicker({
            format: "yyyy/mm/dd",
            language: "en"
        });
    });
    </script>
@endsection
