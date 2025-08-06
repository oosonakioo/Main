@extends('layouts.admin')

@section('header', trans('menu.manage-student'))

<script src="{{ asset('js/js-xlsx/xlsx.core.min.js') }}"></script>
@section('content')
    <link rel="stylesheet" href="{{ asset('css/jquery.loadmask.css') }}">
    <script src="{{ asset('js/jquery.loadmask.js') }}"></script>
    <div class="box box-solid">
        <div class="box-header with-border">
            <a class="btn btn-success" href="{{ Helper::url('admin/student/create') }}">
                <i class="fa fa-plus"></i> {{ trans('admin.contents-create') }}</a>

            <!-- IMPORT EXCEL -->
            <a class="btn btn-default" id="_fileInput" href="#"><i class="fa fa-upload"></i> {{ trans('layout.import-excel') }}</a>

            <button type="button" class="btn btn-info btn-lg" id="openModal" style="display: none;" data-toggle="modal" data-target="#myModal">Open Modal</button>
            <input type="file" class="btn btn-default" id="fileInput" style="display: none;" /><br/>

            <form id="uploadDataForm" method="POST">
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
                            <th>Import status</th>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Contract Name</th>
                            <th>E-mail</th>
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

        </div>
        <div class="box-body">
            <table id="table-result" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>Student ID</th>
                  <th>Contact Person</th>
                  <th>Student Name</th>
                  <th>E-mail</th>
                  <th>Update</th>
                  <th width="7%">{{ trans('admin.contents-status') }}</th>
                  <th class="no-sort" width="7%">{{ trans('admin.contents-edit') }}</th>
                  <th class="no-sort" width="7%">{{ trans('admin.contents-delete') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($student as $obj)
                    <tr id="row-{{ $obj->id }}">
                        <td>{{ $obj->custcode }}</td>
                        <td>{{ $obj->contactname }}</td>
                        <td>{{ $obj->custnameeng }}</td>
                        <td>{{ $obj->contemail }}</td>
                        <td>{{  Helper::thai_date(strtotime($obj->updated_at), "short") }}</td>
                        <td align="center">
                            @if($obj->active)
                                <span class="label label-success">{{ trans('admin.contents-active') }}</span>
                            @else
                                <span class="label label-danger">{{ trans('admin.contents-inactive') }}</span>
                            @endif
                        </td>
                        <td align="center">
                            <a class="btn btn-sm btn-warning"
                               href="{{ Helper::url('admin/student/' . $obj->id . '/edit') }}">{{ trans('admin.contents-edit') }}</a>
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
        $(function () {
          $('#table-result').DataTable({
            "order": [[ 4, "desc" ]],
            "columnDefs": [ {
              "targets": 'no-sort',
              "orderable": false,
            }]
          });

          @include('admin.view.view-delete-js', ['url' => 'admin/student'])

          $("#_fileInput").click(function(){
            $("#fileInput").click();
          });
          $('#myModal').on('hidden.bs.modal', function () {
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
        					var activeCols = ["custcode", "custid","custgroupcode","custnameeng", "custadd", "contfax", "contactname","contemail"];
        					var checkHeader = true;
                  var _headers = [];
        					for(var i = 0; i < headers.length; i++)
                    _headers[i] = headers[i].toLowerCase();
        					for(var i = 0; i < activeCols.length; i++)
        					{
        							if(_headers.indexOf(activeCols[i]) == -1)
        							{
        									checkHeader = false; break;
        							}
        					}
        					if(headers.indexOf("listno") == -1)
        					{
        							checkHeader = false;
        					}
        					if(checkHeader) {
        							var json = XLSX.utils.sheet_to_json(worksheet);
        							var datas = [];
        							for(var i = 0; i < json.length; i++)
        							{
                        var _json = {};
              					for(var h = 0; h < activeCols.length; h++)
                          _json[activeCols[h]] = "";
                          for (var prop in json[i]) {
                            _json[prop.toLowerCase()] = json[i][prop];
                          }
        									if(_json.listno == "1"){
        											var data = {};
        											for(var j = 0; j < activeCols.length; j++)
        													data[activeCols[j].toLowerCase()] = _json[activeCols[j]];
        											if(data['custcode'])
        													data['custcode'] = Number(data['custcode']);
        											else data['custcode'] = null;
        											if(data['custid'])
        													data['custid'] = Number(data['custid']);
        											else data['custid'] = null;
        											if(data['custgroupcode'])
        													data['custgroupcode'] = Number(data['custgroupcode']);
        											else data['custgroupcode'] = null;
                              data['active'] = 'active';
        											datas.push(data);
        									}
        							}
                      function doRowImport(data, index){
                        var row = $('<tr rowIndex="' + index + '"><td><span>In queue</span></td><td>' + data.custcode + '</td><td>' + data.custnameeng + '</td><td>' + data.contactname + '</td><td width="7%">' + data.contemail + '</td></tr>');
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
                        $("#uploadDataForm input:not(:first)").remove();
                        for (var prop in data) {
                          $("#uploadDataForm").append('<input type="hidden" name="' + prop + '" value="' + data[prop] + '">');
                        }
                        $.ajax({
                            type: "POST",
                            url: "{{ Helper::url('admin/student/save') }}",
                            data: $("#uploadDataForm").serialize(),
                            success: function (result)
                            {
                                if(result.status == "success"){
                                    $("td:first>span", rows[index]).text("Complete").css("color", "#00a65a");
                                }
                                else{
                                    $("td:first>span", rows[index]).text("Error: " + result.msg).css("color", "#dd4b39");
                                }
                                rows[index].addClass("imported");
                                doneImport();
                            },
                            error: function(data){
                              $("td:first>b", rows[index]).text("Error: " + data.statusText).css("color", "#dd4b39");
                              rows[index].addClass("imported");
                              doneImport();
                            }
                        });
                      }
                      $("#table-import tbody").empty();
                      $("#openModal").click();
                      if(datas.length == 0)
                        $("#table-import tbody").append('<td colspan="5">No import data.</td>');
                        else{
                    $("body").mask("Loading");
                        for(var index = 0; index < datas.length; index++){
                          var row = doRowImport(datas[index], index);
                          rows.push(row);
                          $("#table-import tbody").append(rows[index]);
                        }
                        importData(0, function(){
                          if($("#table-import tbody tr").length == $("#table-import tbody tr.imported").length)
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
