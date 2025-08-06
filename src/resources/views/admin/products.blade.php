@extends('layouts.admin')

@section('header', trans('admin.product-'. $menu))

@section('content')
    <div class="box box-solid">
        <div class="box-header with-border">
            <a class="btn btn-success" href="{{ Helper::url('admin/'. $menu. '/product/create') }}">
                <i class="fa fa-plus"></i> {{ trans('admin.contents-create') }}</a>
        </div>
        <div class="box-body">
            <table id="table-result" class="table table-bordered table-hover">
                <thead>
                <tr>
                  @if ($menu == 'payment')
                    <th>ลำดับที่</th>
                    <th>ชื่อ - นามสกุล</th>
                    <th>หมายเลข Ref.</th>
                    <th>จำนวนเงิน</th>
                    <th>เทมเพลท</th>
                    <th width="7%">{{ trans('admin.contents-status') }}</th>
                    <th>อัพเดต</th>
                    <th width="7%">{{ trans('admin.contents-edit') }}</th>
                    <th width="7%">{{ trans('admin.contents-delete') }}</th>
                  @else
                    <th>Title</th>
                    <th>หัวข้อ</th>
                    <th>{{ trans('admin.contents-category') }}</th>
                    <th>{{ trans('admin.contents-sort') }}</th>
                    <th width="7%">{{ trans('admin.contents-status') }}</th>
                    <th width="7%">{{ trans('admin.contents-edit') }}</th>
                    <th width="7%">{{ trans('admin.contents-delete') }}</th>
                  @endif
                </tr>
                </thead>
                <tbody>
                @foreach($products as $pro)
                    <tr id="row-{{ $pro->id }}">
                        @if ($menu == 'payment')
                          <td>{{ $pro->id }}</td>
                          <td>{{ $pro->translate('th')->title }}</td>
                          <td>{{ $pro->value }}</td>
                          <td>{{ $pro->image }}</td>
                          <td>
                            @foreach($categories as $cat)
                              @if($cat->id == $pro->categories_id)
                                {{ $cat->translate('th')->title }}
                              @endif
                            @endforeach
                          </td>
                          <td align="center">
                              @if($pro->active)
                                  <span class="label label-success">{{ trans('admin.contents-active') }}</span>
                              @else
                                  <span class="label label-danger">{{ trans('admin.contents-inactive') }}</span>
                              @endif
                          </td>
                          <td>{{  Helper::thai_date(strtotime($pro->updated_at), "short") }}</td>
                          <td align="center">
                              <a class="btn btn-sm btn-warning"
                                 href="{{ Helper::url('admin/'. $menu. '/product/' . $pro->id . '/edit') }}">{{ trans('admin.contents-edit') }}</a>
                          </td>
                          <td align="center">
                              <a class="btn btn-sm btn-danger delete" href="#" data-id="{{ $pro->id }}">{{ trans('admin.contents-delete') }}</a>
                          </td>
                        @else
                          <td>{{ $pro->translate('en')->title }}</td>
                          <td>{{ $pro->translate('th')->title }}</td>
                          <td>
                            @foreach($categories as $cat)
                              @if($cat->id == $pro->categories_id)
                                {{ $cat->translate('th')->title }}
                              @endif
                            @endforeach
                          </td>
                          <td>{{ $pro->sort }}</td>
                          <td align="center">
                              @if($pro->active)
                                  <span class="label label-success">{{ trans('admin.contents-active') }}</span>
                              @else
                                  <span class="label label-danger">{{ trans('admin.contents-inactive') }}</span>
                              @endif
                          </td>
                          <td align="center">
                              <a class="btn btn-sm btn-warning"
                                 href="{{ Helper::url('admin/'. $menu. '/product/' . $pro->id . '/edit') }}">{{ trans('admin.contents-edit') }}</a>
                          </td>
                          <td align="center">
                              <a class="btn btn-sm btn-danger delete" href="#" data-id="{{ $pro->id }}">{{ trans('admin.contents-delete') }}</a>
                          </td>
                        @endif
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
            $('#table-result').DataTable();

            @include('admin.view.view-delete-js', ['url' => 'admin/'. $menu. '/product'])
        });
    </script>
@endsection
