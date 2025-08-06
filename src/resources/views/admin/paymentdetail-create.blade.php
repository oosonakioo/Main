@extends('layouts.admin')

@section('header', trans('menu.payment-detail'))

@section('content')

  <div class="box box-primary">
    <div class="box-body">
      <form class="form-horizontal" enctype="multipart/form-data" method="POST"
            action="{{ $paymentdetail->exists
                      ? Helper::url('admin/paymentdetail/'.$paymentdetail->id)
                      : Helper::url('admin/paymentdetail')
                  }}">
          <input name="_method" type="hidden" value="{{ $paymentdetail->exists ? 'PUT' : 'POST' }}"/>
          {!! csrf_field() !!}

          @include('errors.validator')

          <div class="form-group">
              <label class="form-control-static col-md-2" for="docuno_id">Document No </label>
              <div class="col-md-3">
                  <select class="form-control" id="docuno_id" name="docuno_id">
                      @foreach($payment as $category)
                          <option value="{{ $category->docuno }}"
                              {{ Helper::isSelected($category->docuno == $paymentdetail->docuno_id) }}>{{ $category->docuno }} ({{ $category->custnameeng }})</option>
                      @endforeach
                  </select>
              </div>
          </div>
          <div class="form-group">
              <label class="form-control-static col-md-2" for="listno">List No <span class="text-danger">*</span></label>
              <div class="col-md-2">
                  <input class="form-control" type="number" id="listno" name="listno" maxlength="2"
                         value="{{ Helper::getValue($paymentdetail, 'listno', $errors) }}" required/>
              </div>
          </div>

          <div class="form-group">
              <label class="form-control-static col-md-2" for="goodprice2">Price <span class="text-danger">*</span></label>
              <div class="col-md-2">
                  <input class="form-control" type="number" id="goodprice2" name="goodprice2" maxlength="8"
                         value="{{ Helper::getValue($paymentdetail, 'goodprice2', $errors) }}" required/>
              </div>
          </div>

          <div class="form-group">
              <label class="form-control-static col-md-2" for="goodqty2">Quantity <span class="text-danger">*</span></label>
              <div class="col-md-2">
                  <input class="form-control" type="number" id="goodqty2" name="goodqty2" maxlength="8"
                         value="{{ Helper::getValue($paymentdetail, 'goodqty2', $errors) }}" required/>
              </div>
          </div>

          <div class="form-group">
              <label class="form-control-static col-md-2" for="goodcode">Code <span class="text-danger">*</span></label>
              <div class="col-md-6">
                  <input class="form-control" type="text" id="goodcode" name="goodcode"
                         value="{{ Helper::getValue($paymentdetail, 'goodcode', $errors) }}" required/>
              </div>
          </div>

          <div class="form-group">
              <label class="form-control-static col-md-2" for="goodname">Goods Name <span class="text-danger">*</span></label>
              <div class="col-md-6">
                  <input class="form-control" type="text" id="goodnameeng1" name="goodnameeng1"
                         value="{{ Helper::getValue($paymentdetail, 'goodnameeng1', $errors) }}" required/>
              </div>
          </div>

          <div class="form-group">
              <label class="form-control-static col-md-2" for="rematotalamnt">Total Amount <span class="text-danger">*</span></label>
              <div class="col-md-2">
                  <input class="form-control" type="number" id="rematotalamnt" name="rematotalamnt" maxlength="8"
                         value="{{ Helper::getValue($paymentdetail, 'rematotalamnt', $errors) }}" required/>
              </div>
          </div>
          <div class="form-group">
              <label class="form-control-static col-md-2" for="gooddiscamnt">Discount <span class="text-danger">*</span></label>
              <div class="col-md-2">
                  <input class="form-control" type="number" id="gooddiscamnt" name="gooddiscamnt" maxlength="8"
                         value="{{ Helper::getValue($paymentdetail, 'gooddiscamnt', $errors) }}" required/>
              </div>
          </div>

          <input type="hidden" id="active" name="active" value="active">

          <button type="submit" class="btn btn-success center-block">
              <span class="glyphicon glyphicon-ok-circle"></span>
              {{ $paymentdetail->exists ? trans('admin.contents-save') : trans('admin.contents-create') }}
          </button>
      </form>
    </div>
  </div>
@endsection

@section('script')

@endsection
