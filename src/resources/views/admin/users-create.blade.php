@extends('layouts.admin')

@section('header', 'User')

@section('content')
<div class="box box-primary">
  <div class="box-body">
    <form class="form-horizontal" enctype="multipart/form-data" method="POST"
          action="{{ $users->exists
                    ? Helper::url('admin/users/'.$users->id)
                    : Helper::url('admin/users/')
                }}">
        <input name="_method" type="hidden" value="{{ $users->exists ? 'PUT' : 'POST' }}"/>
        {!! csrf_field() !!}

        @include('errors.validator')

        <div class="form-group">
            <label class="form-control-static col-md-2" for="name">Name <span class="text-danger">*</span></label>
            <div class="col-md-6">
                <input class="form-control" type="text" id="name" name="name" maxlength="50"
                       value="{{ Helper::getValue($users, 'name', $errors) }}" required/>
            </div>
        </div>
        <div class="form-group">
            <label class="form-control-static col-md-2" for="email">E-mail <span class="text-danger">*</span></label>
            <div class="col-md-6">
                <input class="form-control" type="email" id="email" name="email" maxlength="50"
                       value="{{ Helper::getValue($users, 'email', $errors) }}" required/>
            </div>
        </div>
        <div class="form-group">
            <label class="form-control-static col-md-2" for="password">Password <span class="text-danger">*</span></label>
            <div class="col-md-6">
                <input class="form-control" type="text" id="password" name="password" maxlength="8"
                       value="" required/>
            </div>
        </div>

        <div class="form-group">
            <label class="form-control-static col-md-2" for="permission">
                Permission Menu
            </label>
            <div class="col-md-10">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="permission[]" id="permission" value="1" {{ Helper::isChecked($users->permission, 1) }}>
                    Setting
                  </label>
                </div>
              </div>
              <!--<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="permission[]" id="permission" value="2" {{ Helper::isChecked($users->permission, 2) }}>
                    Banners
                  </label>
                </div>
              </div>-->
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="permission[]" id="permission" value="3" {{ Helper::isChecked($users->permission, 3) }}>
                    Medias
                  </label>
                </div>
              </div>
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="permission[]" id="permission" value="4" {{ Helper::isChecked($users->permission, 4) }}>
                    Manage Student
                  </label>
                </div>
              </div>
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="permission[]" id="permission" value="5" {{ Helper::isChecked($users->permission, 5) }}>
                    Payment
                  </label>
                </div>
              </div>
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="permission[]" id="permission" value="6" {{ Helper::isChecked($users->permission, 6) }}>
                    Mail Template
                  </label>
                </div>
              </div>
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="permission[]" id="permission" value="7" {{ Helper::isChecked($users->permission, 7) }}>
                    Mail Lists
                  </label>
                </div>
              </div>
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="permission[]" id="permission" value="8" {{ Helper::isChecked($users->permission, 8) }}>
                    Report
                  </label>
                </div>
              </div>
            </div>
        </div>

        <hr>
        <button type="submit" class="btn btn-success center-block">
            <span class="glyphicon glyphicon-ok-circle"></span>
            {{ $users->exists ? 'Save' : 'Register' }}
        </button>
    </form>
  </div>
</div>
@endsection
