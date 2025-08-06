@extends('layouts.admin')

@if ($menu == 'gallerys')
  @section('header', trans('admin.downloadgallerys'))
@else
  @section('header', trans('admin.downloadvideos'))
@endif

@section('content')
    <div class="box box-solid">
        <div class="box-header with-border">
            <a class="btn btn-success" href="{{ Helper::url('admin/download/'. $menu. '/create') }}">
                <i class="fa fa-plus"></i> {{ trans('admin.contents-create') }}</a>
        </div>
        <div class="box-body">
            <table id="table-result" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>หัวข้อ</th>
                    <th>{{ trans('admin.contents-image') }}</th>
                    <th width="10%">{{ trans('admin.contents-sort') }}</th>
                    <th width="7%">{{ trans('admin.contents-status') }}</th>
                    <th width="7%">{{ trans('admin.contents-edit') }}</th>
                    <th width="7%">{{ trans('admin.contents-delete') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($medias as $n)
                    <tr id="row-{{ $n->id }}">
                        <td>{{ $n->translate('th')->title }}</td>
                        <td>
                          @if($n->images != '')
                            <img class="img-responsive" width="100px" src="{{ asset($n->images) }}" />
                          @endif
                        </td>
                        <td>{{ $n->sort }}</td>
                        <td align="center">
                            @if($n->active)
                                <p class="text-primary">{{ trans('admin.contents-active') }}</p>
                            @else
                                <p class="text-danger">{{ trans('admin.contents-inactive') }}</p>
                            @endif
                        </td>
                        <td align="center">
                            <a class="btn btn-sm btn-warning"
                               href="{{ Helper::url('admin/download/'. $menu. '/' . $n->id . '/edit') }}">{{ trans('admin.contents-edit') }}</a>
                        </td>
                        <td align="center">
                            <a class="btn btn-sm btn-danger delete" href="#" data-id="{{ $n->id }}">{{ trans('admin.contents-delete') }}</a>
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
            $('#table-result').DataTable()
                            .order([2, 'asc'])
                            .draw();

            @include('admin.view.view-delete-js', ['url' => 'admin/download/{{ $menu }}/'])
        });
    </script>
@endsection
