@extends('layouts.admin')

@section('header', trans('menu.payment-master'))

<script src="{{ asset('js/js-xlsx/xlsx.core.min.js') }}"></script>
@section('content')
    <link rel="stylesheet" href="{{ asset('css/jquery.loadmask.css') }}">
    <script src="{{ asset('js/jquery.loadmask.js') }}"></script>
    <div class="box box-solid">
        <div class="box-header with-border">
            <a class="btn btn-success" href="{{ Helper::url('admin/payment/create') }}">
                <i class="fa fa-plus"></i> {{ trans('admin.contents-create') }}</a>

            <!-- IMPORT EXCEL -->
            <a class="btn btn-default" id="_fileInput" href="#">
              <i class="fa fa-upload"></i> {{ trans('layout.import-excel') }}</a>

            <!-- BATCH DELETE -->
            <a class="btn btn-danger" style="float: right;" id="_batchdelete" href="#">
              <i class="fa fa-trash"></i> Batch Delete</a>

            <button type="button" class="btn btn-info btn-lg" id="openModal" style="display: none;" data-toggle="modal" data-target="#myModal">Open Modal</button>
            <button type="button" class="btn btn-info btn-lg" id="open_batchdel_modal" style="display: none;" data-toggle="modal" data-target="#batchdel_modal">Open Modal</button>
            <input type="file" class="btn btn-default" id="fileInput" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" style="display: none;" /><br/>

            <form id="batchdeleteForm" method="POST">
          		{!! csrf_field() !!}
          	</form>
            <form id="uploadMasterForm" method="POST">
          		{!! csrf_field() !!}
          	</form>
            <form id="uploadDetailForm" method="POST">
          		{!! csrf_field() !!}
          	</form>

            <div id="myModal" class="modal fade" role="dialog">
              <div class="modal-dialog" style="width: 90%; overflow: auto;">
                <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('admin.import-infomation') }}</h4>
                  </div>
                  <div class="modal-body">
                    <table id="table-import" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                          <th>Payment Master</th>
                          <th>Payment Detail</th>
                          <th>Document No</th>
                          <th>Document Date</th>
                          <th>Student ID</th>
                          <th>Student Name</th>
                          <th>Total Price</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>

            <div id="batchdel_modal" class="modal fade" role="dialog">
              <div class="modal-dialog" style="width: 90%; overflow: auto;">
                <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Delete Information</h4>
                  </div>
                  <div class="modal-body">
                    <span id="batch_result"></span>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>

        </div>
        <div class="box-body">
            <table id="table-result" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Document No</th>
                    <th>Document Date</th>
                    <th>Payment Due Date</th>
                    <th>Student Name</th>
                    <th>Total Price</th>
                    <th>Update</th>
                    <th width="7%">Status</th>
                    <th class="no-sort" width="7%">{{ trans('admin.contents-edit') }}</th>
                    <th class="no-sort" width="7%">{{ trans('admin.contents-delete') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($paymentmaster as $obj)
                    <tr id="row-{{ $obj->id }}">
                        <td>{{ $obj->docuno }}</td>
                        <td>{{ Helper::thai_date(strtotime($obj->docudate), "onlydate") }}</td>
                        <td>{{ Helper::thai_date(strtotime($obj->shipdate), "onlydate") }}</td>
                        <td>{{ $obj->custnameeng }}</td>
                        <td>{{ $english_format_number = number_format($obj->price->sum('rematotalamnt'), 2) }}</td>
                        <td>{{ Helper::thai_date(strtotime($obj->updated_at), "short") }}</td>
                        <td align="center">
                          <?php switch ($obj->paymentstatus) {
                            case 1:
                              echo '<a class="btn btn-sm btn-default view" href="#">Import</a>';
                              break;
                            case 2:
                              echo '<a class="btn btn-sm btn-info view" href="#">Create Mail</a>';
                              break;
                            case 3:
                              echo '<a class="btn btn-sm btn-info view" href="#">Send Mail</a>';
                              break;
                            case 4:
                              echo '<a class="btn btn-sm btn-success view" href="#">Paid</a>';
                              break;
                            default:
                              # code...
                              break;
                          } ?>
                        </td>
                        <td align="center">
                            <a class="btn btn-sm btn-warning"
                               href="{{ Helper::url('admin/payment/' . $obj->id . '/edit') }}">{{ trans('admin.contents-edit') }}</a>
                        </td>
                        <td align="center">
                            <a class="btn btn-sm btn-danger delete" href="#" data-id="{{ $obj->id }}">{{ trans('admin.contents-delete') }}</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">

        var offset = new Date().getTimezoneOffset()*60*1000;
        var txt_show = '';

        $(function () {
            $('#table-result').DataTable({
              "order": [[ 5, "desc" ]],
              "columnDefs": [ {
                "targets": 'no-sort',
                "orderable": false,
              }]
            });

            @include('admin.view.view-delete-js', ['url' => 'admin/payment'])

            $("#_fileInput").click(function(){
              $("#fileInput").click();
            });

            $("#_batchdelete").on("click", function(e) {
              e.preventDefault();
              if (confirm("Are you sure to delete ?")) {
                $.ajax({
                    type: "POST",
                    url: "{{ Helper::url('admin/payment/batchdelete') }}",
                    data: $("#batchdeleteForm").serialize(),
                    success: function (result)
                    {
                      $("#open_batchdel_modal").click();
                      if(result.status == "success"){
                        if(result.record > 0) {
                          txt_show = 'Delete ' + result.record;
                          if(result.record > 1) {
                            txt_show = txt_show + ' records successfully.';
                          } else {
                            txt_show = txt_show + ' record successfully.';
                          }
                          $('#batch_result').text(txt_show).css("color", "#00a65a");
                        } else {
                          txt_show = 'No data found to delete.';
                          $('#batch_result').text(txt_show).css("color", "#dd4b39");
                        }

                      }
                      else{

                      }
                    },
                    error: function(data){
                      txt_show = "<font color='red'>Error: " + data.statusText + "</font>";
                      $('#batch_result').innerHTML = txt_show;
                    }
                });
              }
            });

            $('#myModal').on('hidden.bs.modal', function () {
                window.location.reload();
            })

            $('#batchdel_modal').on('hidden.bs.modal', function () {
                window.location.reload();
            })

          	function fixdata(data) {
          	  var o = "", l = 0, w = 10240;
          	  for(; l<data.byteLength/w; ++l) o+=String.fromCharCode.apply(null,new Uint8Array(data.slice(l*w,l*w+w)));
          	  o+=String.fromCharCode.apply(null, new Uint8Array(data.slice(l*w)));
          	  return o;
          	}

          	var rABS = true; // true: readAsBinaryString ; false: readAsArrayBuffer
          	function get_header_row(sheet) {
              var headers = [];
              var range = XLSX.utils.decode_range(sheet['!ref']);
              var C, R = range.s.r; /* start in the first row */
              /* walk every column in the range */
              for(C = range.s.c; C <= range.e.c; ++C) {
                  var cell = sheet[XLSX.utils.encode_cell({c:C, r:R})] /* find the cell in the first row */

                  var hdr = "UNKNOWN " + C; // <-- replace with your desired default
                  if(cell && cell.t) hdr = XLSX.utils.format_cell(cell);

                  headers.push(hdr);
              }
              return headers;
            }

          	function handleFile(e) {
          	  var files = e.target.files;
          	  var i,f;
          	  for (i = 0; i != files.length; ++i) {
          	    f = files[i];
          			var arr = f.name.split('.');
          			if(arr[arr.length - 1] != 'xls' && arr[arr.length - 1] != 'xlsx'){
          					alert('Please select xls or xlsx file.');
          					return false;
          			}
          	    var reader = new FileReader();
          	    var name = f.name;
          	    reader.onload = function(e) {
          	      var data = e.target.result;

          	      var workbook;
          	      if(rABS) {
          	        /* if binary string, read with type 'binary' */
          	        workbook = XLSX.read(data, {type: 'binary'});
          	      } else {
          	        /* if array buffer, convert to base64 */
          	        var arr = fixdata(data);
          	        workbook = XLSX.read(btoa(arr), {type: 'base64'});
          	      }
          				var first_sheet_name = workbook.SheetNames[0];
          					/* Get worksheet */
          					var worksheet = workbook.Sheets[first_sheet_name];
          					var headers = get_header_row(worksheet);
          					var masterCols = ["docuno","docudate","shipdate","custcode","custnameeng","templateno", "remark"];
          					var detailCols = ["docuno","listno","goodprice2","goodqty2","goodcode","goodnameeng1","rematotalamnt","gooddiscamnt"];
          					var checkHeader = true;
                    var _headers = [];
          					for(var i = 0; i < headers.length; i++)
                      _headers[i] = headers[i].toLowerCase();
          					for(var i = 0; i < masterCols.length; i++)
          					{
          							if(_headers.indexOf(masterCols[i]) == -1)
          							{
          									checkHeader = false; break;
          							}
          					}
                    for(var i = 0; i < detailCols.length; i++)
                    {
                        if(_headers.indexOf(detailCols[i]) == -1)
                        {
                            checkHeader = false; break;
                        }
                    }
          					if(_headers.indexOf("listno") == -1)
          					{
          							checkHeader = false;
          					}
          					if(checkHeader) {
          							var json = XLSX.utils.sheet_to_json(worksheet);
          							var datas = [];
          							for(var i = 0; i < json.length; i++)
          							{
                          var _master = {};
                          var _detail = {};
                					for(var h = 0; h < masterCols.length; h++)
                            _master[masterCols[h]] = "";
                  					for(var h = 0; h < detailCols.length; h++){
                              if(detailCols[h] == "docuno")
                                _detail["docuno_id"] = "";
                              else
                                _detail[detailCols[h]] = "";
                            }
                            for (var prop in json[i]) {
                              if(masterCols.indexOf(prop.toLowerCase()) != -1)
                                _master[prop.toLowerCase()] = json[i][prop];
                              if(detailCols.indexOf(prop.toLowerCase()) != -1){
                                if(prop.toLowerCase() == "docuno")
                                  _detail["docuno_id"] = json[i][prop];
                                else
                                  _detail[prop.toLowerCase()] = json[i][prop];
                              }
                            }
          									if(_detail.listno != ""){
          											var data = {
          													PaymentMaster: _master,
          													PaymentDetail: _detail
          											};

                                // Payment Master
          											if (data.PaymentMaster['docuno']) {
                                    data.PaymentMaster['docuno'] = data.PaymentMaster['docuno'];
                                } else {
                                    data.PaymentMaster['docuno'] = null;
                                }
                                if (data.PaymentMaster['docudate']) {
                                    data.PaymentMaster['docudate'] = new Date(Date.parse(data.PaymentMaster['docudate']) - offset).toISOString().substring(0, 10);
                                } else {
                                    data.PaymentMaster['docudate'] = null;
                                }
                                if (data.PaymentMaster['shipdate']) {
                                    data.PaymentMaster['shipdate'] = new Date(Date.parse(data.PaymentMaster['shipdate']) - offset).toISOString().substring(0, 10);
                                } else {
                                    data.PaymentMaster['shipdate'] = null;
                                }
                                if (data.PaymentMaster['custcode']) {
                                    data.PaymentMaster['custcode'] = parseInt(data.PaymentMaster['custcode']);
                                } else {
                                    data.PaymentMaster['custcode'] = null;
                                }
                                if (data.PaymentMaster['custnameeng']) {
                                    data.PaymentMaster['custnameeng'] = data.PaymentMaster['custnameeng'];
                                } else {
                                    data.PaymentMaster['custnameeng'] = null;
                                }
                                if (data.PaymentMaster['templateno']) {
                                    data.PaymentMaster['templateno'] = parseInt(data.PaymentMaster['templateno']);
                                } else {
                                    data.PaymentMaster['templateno'] = null;
                                }
                                if (data.PaymentMaster['remark']) {
                                    data.PaymentMaster['remark'] = data.PaymentMaster['remark'];
                                } else {
                                    data.PaymentMaster['remark'] = null;
                                }

                                // Payment Detail
                                if (data.PaymentDetail['docuno_id']) {
                                    data.PaymentDetail['docuno_id'] = data.PaymentMaster['docuno'];
                                } else {
                                    data.PaymentDetail['docuno_id'] = null;
                                }
                                if (data.PaymentDetail['listno']) {
                                    data.PaymentDetail['listno'] = parseInt(data.PaymentDetail['listno']);
                                } else {
                                    data.PaymentDetail['listno'] = null;
                                }
                                if (data.PaymentDetail['goodprice2']) {
                                    data.PaymentDetail['goodprice2'] = parseInt(data.PaymentDetail['goodprice2']);
                                } else {
                                    data.PaymentDetail['goodprice2'] = null;
                                }
                                if (data.PaymentDetail['goodqty2']) {
                                    data.PaymentDetail['goodqty2'] = parseInt(data.PaymentDetail['goodqty2']);
                                } else {
                                    data.PaymentDetail['goodqty2'] = null;
                                }
                                if (data.PaymentDetail['goodcode']) {
                                    data.PaymentDetail['goodcode'] = data.PaymentDetail['goodcode'];
                                } else {
                                    data.PaymentDetail['goodcode'] = null;
                                }
                                if (data.PaymentDetail['goodnameeng1']) {
                                    data.PaymentDetail['goodnameeng1'] = data.PaymentDetail['goodnameeng1'];
                                } else {
                                    data.PaymentDetail['goodnameeng1'] = null;
                                }
                                if (data.PaymentDetail['rematotalamnt']) {
                                    data.PaymentDetail['rematotalamnt'] = parseInt(data.PaymentDetail['rematotalamnt']);
                                } else {
                                    data.PaymentDetail['rematotalamnt'] = null;
                                }
                                if (data.PaymentDetail['gooddiscamnt']) {
                                    data.PaymentDetail['gooddiscamnt'] = parseInt(data.PaymentDetail['gooddiscamnt']);
                                } else {
                                    data.PaymentDetail['gooddiscamnt'] = null;
                                }
                                data.PaymentMaster['active'] = 'active';
                                data.PaymentDetail['active'] = 'active';
                                data.PaymentMaster['paymentstatus'] = 1;
          											datas.push(data);
          									}
          							}
                        function doRowImport(data, index){
                            var row = $('<tr rowIndex="' + index + '"><td class="impMaster"><b>In queue</b></td><td class="impDetail"><b>In queue</b></td><td>' + data.PaymentMaster.docuno + '</td><td>' + data.PaymentMaster.docudate + '</td><td>' + data.PaymentMaster.custcode + '</td><td>' + data.PaymentMaster.custnameeng + '</td><td>' + data.PaymentDetail.rematotalamnt + '</td></tr>');
                          return row;
                        }
                        var rows = [];
                        function importData(index, callback){
                          function doneImport(){
                            if(index < datas.length - 1)
                              importData(++index, callback);
                            else if(callback) callback();
                          }
                          var data = datas[index];
                          $("#uploadMasterForm input:not(:first)").remove();
                          for (var prop in data.PaymentMaster) {
                            $("#uploadMasterForm").append('<input type="hidden" name="' + prop + '" value="' + data.PaymentMaster[prop] + '">');
                          }
                            $.ajax({
                                type: "POST",
                                url: "{{ Helper::url('admin/payment/save') }}",
                                data: $("#uploadMasterForm").serialize(),
                                success: function (result)
                                {
                                    if(result.status == "success"){
                                        $(".impMaster", rows[index]).text("Complete").css("color", "#00a65a");
                                    }
                                    else{
                                        $(".impMaster", rows[index]).text("Error: " + result.msg).css("color", "#dd4b39");
                                    }
                                    $(".impMaster", rows[index]).addClass("imported");
                                    importDetail();
                                },
                                error: function(data){
                                  $(".impMaster", rows[index]).text("Error: " + data.statusText).css("color", "#dd4b39");
                                  $(".impMaster", rows[index]).addClass("imported");
                                  importDetail();
                                }
                            });
                          function importDetail(){
                            $("#uploadDetailForm input:not(:first)").remove();
                            for (var prop in data.PaymentDetail) {
                              $("#uploadDetailForm").append('<input type="hidden" name="' + prop + '" value="' + data.PaymentDetail[prop] + '">');
                            }
                            $.ajax({
                                type: "POST",
                                url: "{{ Helper::url('admin/paymentdetail/save') }}",
                                data: $("#uploadDetailForm").serialize(),
                                success: function (result)
                                {
                                    if(result.status == "success"){
                                        $(".impDetail", rows[index]).text("Complete").css("color", "#00a65a");
                                    }
                                    else{
                                        $(".impDetail", rows[index]).text("Error: " + result.msg).css("color", "#dd4b39");
                                    }
                                    $(".impDetail", rows[index]).addClass("imported");
                                    doneImport();
                                },
                                error: function(data){
                                  $(".impDetail", rows[index]).text("Error: " + data.statusText).css("color", "#dd4b39");
                                  $(".impDetail", rows[index]).addClass("imported");
                                  doneImport();
                                }
                            });
                          }
                        }
                        $("#table-import tbody").empty();
                        $("#openModal").click();
                        if(datas.length == 0) {
                          $("#table-import tbody").append('<td colspan="8">No import data.</td>');
                        } else {
                          $("body").mask("Loading");
                          for(var index = 0; index < datas.length; index++){
                            var row = doRowImport(datas[index], index);
                            rows.push(row);
                            $("#table-import tbody").append(rows[index]);
                          }
                          importData(0, function(){
                            if($("#table-import tbody .impMaster").length == $("#table-import tbody .impMaster.imported").length && $("#table-import tbody .impDetail").length == $("#table-import tbody .impDetail.imported").length)
                              $("body").unmask();
                          });
                      }
                        $("#fileInput").val('');
          					}
          					else {
          						alert("Invalid file format.");
          					}
          	    };
          	    reader.readAsBinaryString(f);
          	  }
          	}
          	$("#fileInput").change(handleFile);
        });
    </script>
@endsection
