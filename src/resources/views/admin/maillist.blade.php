@extends('layouts.admin')

@section('header', 'Mail Lists')
@section('content')
<link rel="stylesheet" href="{{ asset('css/jquery.loadmask.css') }}">
<script src="{{ asset('js/jquery.loadmask.js') }}"></script>
    <div class="box box-solid">
        <div class="box-header with-border">
            <!--<a class="btn btn-success" href="{{ Helper::url('admin/maillists/create') }}">
                <i class="fa fa-plus"></i> {{ trans('admin.contents-create') }}</a>-->
            <a class="btn btn-success" id="sendmail" href="#"><i class="fa fa-send"></i> Send Mail</a>
            <!-- Generate -->
            <a class="btn btn-default" id="genmail" href="#"><i class="fa fa-upload"></i> Generate Mail</a>
            <form id="genmailDataForm" method="POST">
              {!! csrf_field() !!}
            </form>
            <form id="sendmailDataForm" method="POST">
              {!! csrf_field() !!}
            </form>
            <form id="getFileForm" method="POST" action="{{ Helper::url('admin/maillist/getfile') }}" target="_blank">
              {!! csrf_field() !!}
              <input type="hidden" name="name" value="" />
            </form>
        </div>
        <div class="box-body">
            <table id="table-result" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th class="no-sort"><input type="checkbox" class="selectAll" /></th>
                    <th>Document No</th>
                    <th>Student Name</th>
                    <th>PDF</th>
                    <th>Update</th>
                    <th class="no-sort" width="7%">&nbsp;</th>
                    <th class="no-sort" width="7%">&nbsp;</th>
                    <th class="no-sort" width="7%">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                @foreach($maillists as $obj)
                    <tr id="row-{{ $obj->id }}" objid="{{ $obj->id }}">
                        <th><input type="checkbox" class="selectRow" data-id="{{ $obj->id }}" /></th>
                        <td>{{ $obj->docuno }}</td>
                        <td>{{ $obj->custname }}</td>
                        <td>{{ $obj->attach_pdf }}</td>
                        <td>{{ Helper::thai_date(strtotime($obj->updated_at), "short") }}</td>
                        <td align="center">
                            <a class="btn btn-sm btn-info view" href="#" data-id="{{ $obj->id }}">{{ trans('admin.contents-view') }}</a>
                        </td>
                        <td align="center">
                            <a class="btn btn-sm btn-success send" href="#" data-id="{{ $obj->id }}">{{ trans('admin.contents-send') }}</a>
                        </td>
                        <td align="center">
                            <a class="btn btn-sm btn-danger delete" href="#" data-id="{{ $obj->id }}">{{ trans('admin.contents-delete') }}</a>
                        </td>
                    </tr>
                    <button type="button" class="btn btn-info btn-lg" id="openModal-{{ $obj->id }}" style="display: none;" data-toggle="modal" data-target="#myModal-{{ $obj->id }}">Open Modal</button>
                    <div id="myModal-{{ $obj->id }}" class="modal fade" role="dialog">
                      <div class="modal-dialog" style="width: 90%; overflow: auto;">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">{{ trans('admin.mail-infomation') }}</h4>
                          </div>
                          <div class="modal-body">
                            <div class="row" style="margin: 10px 0;">
                                <label class="form-control-static col-md-3 text-right">From</label>
                                <div class="col-md-5">
                                    <input class="form-control" type="text" readonly value="{{ $obj->mailfrom }}" placeholder=""/>
                                </div>
                            </div>
                            <div class="row" style="margin: 10px 0;">
                                <label class="form-control-static col-md-3 text-right">To</label>
                                <div class="col-md-5">
                                    <input class="form-control" type="text" readonly value="{{ $obj->mailto }}" placeholder=""/>
                                </div>
                            </div>
                            <div class="row" style="margin: 10px 0;">
                                <label class="form-control-static col-md-3 text-right">CC</label>
                                <div class="col-md-5">
                                    <input class="form-control" type="text" readonly value="{{ $obj->mailcc }}" placeholder=""/>
                                </div>
                            </div>
                            <div class="row" style="margin: 10px 0;">
                                <label class="form-control-static col-md-3 text-right">Reply to</label>
                                <div class="col-md-5">
                                    <input class="form-control" type="text" readonly value="{{ $obj->mailreplyto }}" placeholder=""/>
                                </div>
                            </div>
                            <div class="row" style="margin: 10px 0;">
                                <label class="form-control-static col-md-3 text-right">Subject</label>
                                <div class="col-md-5">
                                    <input class="form-control" type="text" readonly value="{{ $obj->mailsubject }}" placeholder=""/>
                                </div>
                            </div>
                            <div class="row" style="margin: 10px 0;">
                                <label class="form-control-static col-md-3 text-right">Body</label>
                                <div class="col-md-8">
                                  <div style="border: 1px solid #d2d6de;background-color: #eeeeee;max-height: calc(100vh - 540px);overflow: auto;">
                                      {!! $obj->mailbody !!}
                                  </div>
                                </div>
                            </div>
                            <div class="row" style="margin: 10px 0;">
                                <label class="form-control-static col-md-3 text-right">Attachment</label>
                                <div class="col-md-5">
                                    <input class="form-control getFileModal" type="text" readonly value="{{ $obj->attach_pdf }}" data-filename="{{ Helper::url(). '/'. config('setting.pdf-path'). $obj->attach_pdf }}" placeholder=""/>
                                    <!--<input class="form-control getFileModal" type="text" readonly value="{{ $obj->attach_jpg }}" data-filename="{{ Helper::url(). '/'. config('setting.image-path'). $obj->attach_jpg }}" placeholder=""/>-->
                                </div>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-success sendMailModal" data-id="{{ $obj->id }}">Send</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                          </div>
                        </div>
                      </div>
                    </div>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(function () {
            $('#table-result').DataTable({
              "order": [[ 4, "desc" ]],
              "columnDefs": [ {
                "targets": 'no-sort',
                "orderable": false,
              }]
            });
            @include('admin.view.view-delete-js', ['url' => 'admin/maillist'])
            $("#table-result").on("click", ".selectRow", function(e) {
              if($(".selectRow:checked").length == $(".selectRow").length)
                $(".selectAll")[0].checked = true;
              else $(".selectAll")[0].checked = false;
            });
            $(".selectAll").click(function(e) {
              if($(".selectAll")[0].checked)
              {
                $(".selectRow:not(:checked)").each(function(){
                    this.checked = true;
                });
              }
              else{
                $(".selectRow:checked").each(function(){
                    this.checked = false;
                });
              }
            });
            function sendMail(ids){
              $("body").mask("Loading");
              $("#sendmailDataForm input:not(:first)").remove();
              for(var i = 0; i < ids.length; i++)
                $("#sendmailDataForm").append("<input name='ids[" + i + "]' type='hidden' value='" + ids[i] + "' />");
              $.ajax({
                  type: "POST",
                  url: "{{ Helper::url('admin/maillist/sendmail') }}",
                  data: $("#sendmailDataForm").serialize(),
                  success: function (result)
                  {
                      if(result.status == "success"){
                        var alertmsg = result.data ? result.data + " email(s) has/have been sent." : "";
                        if(result.mailerror)
                          for(var i =0; i < result.mailerror.length; i++)
                            alertmsg += (alertmsg == "" ? "" : "<br />") + mailerror[i];
                        alert(alertmsg);
                        if(result.data) window.location.reload();
                        else $("body").unmask();
                      }
                      else{
                        alert(result.msg);
                        $("body").unmask();
                      }
                  },
                  error: function(data){
                    alert("Error!");
                    $("body").unmask();
                  }
              });
            }
            $("#sendmail").click(function(){
                var ids = [];
                $(".selectRow:checked").each(function(){
                    ids.push($(this).data('id'));
                });
                if(ids.length == 0){
                  alert("{{trans('admin.mail-pleaseselect')}}");
                }
                else sendMail(ids);
            });
            $("#table-result").on("click", ".send", function(e) {
                var id = $(this).data('id');
                sendMail([id]);
            });
            $(".sendMailModal").click(function(e) {
                var id = $(this).data('id');
                sendMail([id]);
            });
            $(".getFileModal").click(function(e) {
                var filename = $(this).data('filename');
                //$("#getFileForm input[name='name']").val($(this).data('filename'));
                $('#getFileForm').prop('action', filename);
                $("#getFileForm").submit();
            });
            $("#table-result").on("click", ".view", function(e) {
                var id = $(this).data('id');
                $("#openModal-" + id).click();
            });
            $("#genmail").click(function(){
              $("body").mask("Loading");
              $.ajax({
                  type: "POST",
                  url: "{{ Helper::url('admin/maillist/genmail') }}",
                  data: $("#genmailDataForm").serialize(),
                  timeout: 0,
                  success: function (result)
                  {
                    if(result.data){
                      alert(result.data + " email(s) has/have been generated.");
                      window.location.reload();
                    }
                    else{
                      alert("No payment data to generate.");
                      $("body").unmask();
                    }
                  },
                  error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError);
                    $("body").unmask();
                  }
              });
            });
        });
    </script>
@endsection
