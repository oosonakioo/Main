@extends('layouts.admin')

@section('header', 'Report')

@section('content')

    <div class="box box-solid">
        <div class="box-body">
          <form action="{{ Helper::url('admin/report/export') }}" target="_blank">

            @php
              $defaultstart = date('m/d/Y',strtotime("-10 days"));
              $defaultend = date('m/d/Y');
            @endphp
            <div class="row">
              <div class="form-group">
                <label class="form-control-static col-md-2" for="datemin" style="text-align: right;">Payment Start Date: </label>
                <div class="col-md-3">
                  <div class="input-group">
                    <input type="text" id="datemin" name="datemin" class="form-control" value="{{ $defaultstart }}" readonly>
                    <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                  </div>
                </div>
                <label class="form-control-static col-md-2" for="datemax" style="text-align: right;">Payment End Date: </label>
                <div class="col-md-3">
                  <div class="input-group">
                    <input type="text" id="datemax" name="datemax" class="form-control" value="{{ $defaultend }}" readonly>
                    <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                  </div>
                </div>
              </div>
            </div>
            <div class="row" style="padding-top:5px;">
              <div class="form-group">
                <label class="form-control-static col-md-2" for="min" style="text-align: right;">Minimum Student ID: </label>
                <div class="col-md-3">
                  <div class="input-group">
                    <input type="number" id="min" class="form-control" name="min">
                  </div>
                </div>
                <label class="form-control-static col-md-2" for="max" style="text-align: right;">Maximum Student ID: </label>
                <div class="col-md-3">
                  <div class="input-group">
                    <input type="number" id="max" class="form-control" name="max">
                  </div>
                </div>
              </div>
            </div>
            <br>

            <table id="tableresult" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>Payment Date</th>
                    <th class="no-sort">Payment Time</th>
                    <th>Document No</th>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th class="no-sort">Total Price</th>
                    <th class="no-sort" width="7%">Status</th>
                    <th class="no-sort">System Date</th>
                  </tr>
                </thead>
                <tbody>
                @foreach($paymentmaster as $obj)
                    @php
                      $date = new DateTime($obj->updated_at);
                    @endphp
                    <tr id="row-{{ $obj->id }}">
                        <td>
                          <span class="hidden">{{ strtotime($obj->updated_at) }}</span>
                          {{ Helper::thai_date(strtotime($obj->updated_at), "onlydate") }}
                        </td>
                        <td>{{ Helper::thai_date(strtotime($obj->updated_at), "onlytime") }}</td>
                        <td>{{ $obj->docuno }}</td>
                        <td>{{ $obj->custcode }}</td>
                        <td>{{ $obj->custnameeng }}</td>
                        <td>{{ $english_format_number = number_format($obj->price->sum('rematotalamnt'), 2) }}</td>
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
                        <td>
                          {{ $date->format('m/d/Y') }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <hr>
            <input type="submit" class="btn btn-success" value="{{ trans('admin.contents-export-excel') }}">
            <input type="hidden" id="sortcol" name="sortcol" value="">
            <input type="hidden" id="sortdir" name="sortdir" value="">
          </form>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        var datemin = "";
        var datemax = "";
        var min = "";
        var max = "";
        var sortcol = "0";
        var sprtdor = "desc";

        $.fn.dataTable.ext.search.push(
            function( settings, data, dataIndex ) {

                sortcol = $('#tableresult').dataTable().fnSettings().aaSorting[0][0];
                sortdir = $('#tableresult').dataTable().fnSettings().aaSorting[0][1];
                $('#sortcol').val(sortcol);
                $('#sortdir').val(sortdir);
                //console.log(sortcol);
                //console.log(sortdir);

                min = parseInt( $('#min').val(), 10 );
                max = parseInt( $('#max').val(), 10 );
                var age = parseFloat( data[3] ) || 0; // use data for the age column

                if ( ( isNaN( min ) && isNaN( max ) ) ||
                     ( isNaN( min ) && age <= max ) ||
                     ( min <= age   && isNaN( max ) ) ||
                     ( min <= age   && age <= max ) )
                {
                    return true;
                }
                return false;
            }
        );

        $.fn.dataTable.ext.search.push(
            function( settings, data, dataIndex ) {
                datemin = new Date($('#datemin').val());
                datemax = new Date($('#datemax').val());
                var date = new Date( data[7] );
                //console.log(datemin);
                //console.log(datemax);

                if ( datemin != "") {
                  if (datemax != "") {
                    if (datemin <= date && date <= datemax) {
                      return true;
                    }
                  } else {
                    if (datemin <= date) {
                      return true;
                    }
                  }
                }
                return false;
            }
        );


        $(function () {
            var table = $('#tableresult').DataTable({
              "order": [[ 0, "desc" ]],
              "columnDefs": [{
                "targets": 'no-sort',
                "orderable": false,
              }]
            });

            $('#min, #max').keyup( function() {
                table.draw();
            });


            $('#datemin').datepicker();
            $('#datemin').on('changeDate', function() {
                //datemin = $('#datemin').val();
                table.draw();
            });

            $('#datemax').datepicker();
            $('#datemax').on('changeDate', function() {
                //datemin = $('#datemax').val();
                table.draw();
            });

        });
    </script>
@endsection
