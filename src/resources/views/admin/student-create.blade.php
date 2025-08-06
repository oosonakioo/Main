@extends('layouts.admin')

@section('header', trans('menu.manage-student'))

@section('content')

  <div class="box box-primary">
    <div class="box-body">
      <form class="form-horizontal" enctype="multipart/form-data" method="POST"
            action="{{ $student->exists
                      ? Helper::url('admin/student/'.$student->id)
                      : Helper::url('admin/student')
                  }}">
          <input name="_method" type="hidden" value="{{ $student->exists ? 'PUT' : 'POST' }}"/>
          {!! csrf_field() !!}

          @include('errors.validator')

          <div class="form-group">
              <label class="form-control-static col-md-2" for="custcode">Student ID <span class="text-danger">*</span></label>
              <div class="col-md-2">
                  <input class="form-control" type="number" id="custcode" name="custcode" maxlength="4"
                         value="{{ Helper::getValue($student, 'custcode', $errors) }}" required/>
              </div>
          </div>
          <div class="form-group">
              <label class="form-control-static col-md-2" for="custid">CustNo <span class="text-danger">*</span></label>
              <div class="col-md-2">
                  <input class="form-control" type="number" id="custid" name="custid" maxlength="4"
                         value="{{ Helper::getValue($student, 'custid', $errors) }}" required/>
              </div>
          </div>
          <div class="form-group">
              <label class="form-control-static col-md-2" for="custgroupcode">Class of <span class="text-danger">*</span></label>
              <div class="col-md-2">
                  <input class="form-control" type="number" id="custgroupcode" name="custgroupcode" maxlength="4"
                         value="{{ Helper::getValue($student, 'custgroupcode', $errors) }}" required/>
              </div>
          </div>
          <div class="form-group">
              <label class="form-control-static col-md-2" for="custnameeng">Student Name <span class="text-danger">*</span></label>
              <div class="col-md-6">
                  <input class="form-control" type="text" id="custnameeng" name="custnameeng" maxlength="250"
                         value="{{ Helper::getValue($student, 'custnameeng', $errors) }}" required/>
              </div>
          </div>

          <div class="form-group">
              <label class="form-control-static col-md-2" for="custadd">Address</label>
              <div class="col-md-6">
                <textarea class="form-control" id="custadd" name="custadd" rows="5">{{ Helper::getValue($student, 'custadd', $errors) }}</textarea>
              </div>
          </div>

          <div class="form-group">
              <label class="form-control-static col-md-2" for="contfax">Family ID <span class="text-danger">*</span></label>
              <div class="col-md-6">
                  <input class="form-control" type="text" id="contfax" name="contfax" maxlength="250"
                         value="{{ Helper::getValue($student, 'contfax', $errors) }}" required/>
              </div>
          </div>
          <div class="form-group">
              <label class="form-control-static col-md-2" for="contactname">Contact Name <span class="text-danger">*</span></label>
              <div class="col-md-6">
                  <input class="form-control" type="text" id="contactname" name="contactname" maxlength="250"
                         value="{{ Helper::getValue($student, 'contactname', $errors) }}" required/>
              </div>
          </div>
          <div class="form-group">
              <label class="form-control-static col-md-2" for="contemail">Contact E-mail <span class="text-danger">*</span></label>
              <div class="col-md-6">
                  <input class="form-control" type="email" id="contemail" name="contemail" maxlength="250"
                         value="{{ Helper::getValue($student, 'contemail', $errors) }}" multiple required/>
              </div>
          </div>

          @include('admin.view.view-status-submit', [ 'obj' => $student])
      </form>
    </div>
  </div>
@endsection

@section('script')
    <script>
    </script>
@endsection
