@extends('layouts.admin')

@section('header', trans('menu.templates'))

@section('content')
    <div class="box box-solid">
        <div class="box-header with-border">
            <a class="btn btn-success" href="{{ Helper::url('admin/template/create') }}">
                <i class="fa fa-plus"></i> {{ trans('admin.contents-create') }}</a>
        </div>
        <div class="box-body">
            <table id="table-result" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Subject</th>
                    <th>Update</th>
                    <th width="7%">{{ trans('admin.contents-status') }}</th>
                    <th width="7%">{{ trans('admin.contents-edit') }}</th>
                    <th width="7%">{{ trans('admin.contents-delete') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($template as $obj)
                    <tr id="row-{{ $obj->id }}">
                        <td>{{ $obj->id }}</td>
                        <td>{{ $obj->mailsubject }}</td>
                        <td>{{ Helper::thai_date(strtotime($obj->updated_at), "short") }}</td>
                        <td align="center">
                            @if($obj->active)
                                <span class="label label-success">{{ trans('admin.contents-active') }}</span>
                            @else
                                <span class="label label-danger">{{ trans('admin.contents-inactive') }}</span>
                            @endif
                        </td>
                        <td align="center">
                            <a class="btn btn-sm btn-warning"
                               href="{{ Helper::url('admin/template/' . $obj->id . '/edit') }}">{{ trans('admin.contents-edit') }}</a>
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
              "order": [[ 0, "asc" ]]
            });

            @include('admin.view.view-delete-js', ['url' => 'admin/template'])
        });
    </script>
@endsection
