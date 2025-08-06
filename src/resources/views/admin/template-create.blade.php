@extends('layouts.admin')

@section('header', trans('menu.templates'))

@section('content')

  <div class="box box-primary">
    <div class="box-body">
      <form class="form-horizontal" enctype="multipart/form-data" method="POST"
            action="{{ $template->exists
                      ? Helper::url('admin/template/'.$template->id)
                      : Helper::url('admin/template')
                  }}">
          <input name="_method" type="hidden" value="{{ $template->exists ? 'PUT' : 'POST' }}"/>
          {!! csrf_field() !!}

          @include('errors.validator')

          <div class="form-group">
              <label class="form-control-static col-md-2" for="mailfrom">Mail From <span class="text-danger">*</span></label>
              <div class="col-md-6">
                  <input class="form-control" type="text" id="mailfrom" name="mailfrom" maxlength="250"
                         value="{{ Helper::getValue($template, 'mailfrom', $errors) }}" required/>
              </div>
          </div>
          <div class="form-group">
              <label class="form-control-static col-md-2" for="mailreplyto">Mail Reply To <span class="text-danger">*</span></label>
              <div class="col-md-6">
                  <input class="form-control" type="text" id="mailreplyto" name="mailreplyto" maxlength="250"
                         value="{{ Helper::getValue($template, 'mailreplyto', $errors) }}" required/>
              </div>
          </div>
          <!--<div class="form-group">
              <label class="form-control-static col-md-2" for="mailto">Mail To <span class="text-danger">*</span></label>
              <div class="col-md-6">
                  <input class="form-control" type="text" id="mailto" name="mailto" maxlength="250"
                         value="{{ Helper::getValue($template, 'mailto', $errors) }}" required/>
              </div>
          </div>-->
          <input type="hidden" name="mailto" id="mailto">

          <div class="form-group">
              <label class="form-control-static col-md-2" for="mailcc">Mail CC <span class="text-danger">*</span></label>
              <div class="col-md-6">
                  <input class="form-control" type="text" id="mailcc" name="mailcc" maxlength="250"
                         value="{{ Helper::getValue($template, 'mailcc', $errors) }}" required/>
              </div>
          </div>
          <div class="form-group">
              <label class="form-control-static col-md-2" for="mailsubject">Mail Subject <span class="text-danger">*</span></label>
              <div class="col-md-6">
                  <input class="form-control" type="text" id="mailsubject" name="mailsubject" maxlength="250"
                         value="{{ Helper::getValue($template, 'mailsubject', $errors) }}" required/>
              </div>
          </div>
          <div>
              <label for="mailbody">Mail Body
                  <span class="text-danger">*</span></label>
              <textarea class="form-control" id="mailbody" name="mailbody"
                        required>{{ Helper::getValue($template, 'mailbody', $errors) }}</textarea>
          </div>


          @include('admin.view.view-status-submit', [ 'obj' => $template])
      </form>
    </div>
  </div>
@endsection

@section('script')
  <script type="text/javascript">
      $(function () {
          CKEDITOR.replace('mailbody', {
              language: 'en'
          });
      });
  </script>
@endsection
