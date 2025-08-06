@extends('layouts.admin')

@section('header', trans('menu.payment-detail'))

<script src="{{ asset('js/js-xlsx/xlsx.core.min.js') }}"></script>
@section('content')
    <link rel="stylesheet" href="{{ asset('css/jquery.loadmask.css') }}">
    <script src="{{ asset('js/jquery.loadmask.js') }}"></script>
    <div class="box box-solid">
        <div class="box-header with-border">
            <a class="btn btn-success" href="{{ Helper::url('admin/paymentdetail/create') }}">
                <i class="fa fa-plus"></i> {{ trans('admin.contents-create') }}</a>

            <!-- IMPORT EXCEL -->
            <!--<a class="btn btn-default" id="_fileInput" href="#"><i class="fa fa-upload"></i> {{ trans('layout.import-excel') }}</a>-->

        </div>
        <div class="box-body">
            <table id="table-result" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th nowrap>Document No</th>
                    <th>Goods Name</th>
                    <th nowrap>Price</th>
                    <th nowrap>Update</th>
                    <!--<th width="7%">{{ trans('admin.contents-status') }}</th>-->
                    <th class="no-sort" width="7%">{{ trans('admin.contents-edit') }}</th>
                    <th class="no-sort" width="7%">{{ trans('admin.contents-delete') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($paymentdetail as $obj)
                    <tr id="row-{{ $obj->id }}">
                        <td>
                          @foreach($payment as $cat)
                            @if($cat->docuno == $obj->docuno_id)
                              {{ $cat->docuno }} ({{ $cat->custnameeng }})
                            @endif
                          @endforeach
                        </td>
                        <td>{{ $obj->goodnameeng1 }}</td>
                        <td>{{ $english_format_number = number_format($obj->rematotalamnt, 2) }}</td>
                        <td>{{ Helper::thai_date(strtotime($obj->updated_at), "short") }}</td>
                        <!--<td align="center">
                            @if($obj->active)
                                <span class="label label-success">{{ trans('admin.contents-active') }}</span>
                            @else
                                <span class="label label-danger">{{ trans('admin.contents-inactive') }}</span>
                            @endif
                        </td>-->
                        <td align="center">
                            <a class="btn btn-sm btn-warning"
                               href="{{ Helper::url('admin/paymentdetail/' . $obj->id . '/edit') }}">{{ trans('admin.contents-edit') }}</a>
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
              "order": [[ 3, "desc" ]],
              "columnDefs": [ {
                "targets": 'no-sort',
                "orderable": false,
              }]
            });

            @include('admin.view.view-delete-js', ['url' => 'admin/paymentdetail'])

        });
    </script>
@endsection
