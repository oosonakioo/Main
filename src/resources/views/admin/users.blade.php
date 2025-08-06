@extends('layouts.admin')

@section('header', 'User')

@section('content')
    <div class="box box-solid">
        <div class="box-header with-border">
            <a class="btn btn-success" href="{{ Helper::url('admin/users/create') }}">
                <i class="fa fa-plus"></i> Register</a>
        </div>
        <div class="box-body">
            <table id="table-result" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>E-mail</th>
                    <th>Updated</th>
                    <th class="no-sort" width="7%">{{ trans('admin.contents-edit') }}</th>
                    <th class="no-sort" width="7%">{{ trans('admin.contents-delete') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $result)
                    <tr id="row-{{ $result->id }}">
                        <td>{{ $result->name }}</td>
                        <td>{{ $result->email }}</td>
                        <td>{{ Helper::thai_date(strtotime($result->updated_at), "short") }}</td>
                        <td align="center">
                            <a class="btn btn-sm btn-warning"
                               href="{{ Helper::url('admin/users/' . $result->id . '/edit') }}">{{ trans('admin.contents-edit') }}</a>
                        </td>
                        <td align="center">
                            <a class="btn btn-sm btn-danger delete" href="#" data-id="{{ $result->id }}">{{ trans('admin.contents-delete') }}</a>
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
              "order": [[ 2, "asc" ]],
              "columnDefs": [ {
                "targets": 'no-sort',
                "orderable": false,
              }]
            });

            @include('admin.view.view-delete-js', ['url' => 'admin/users'])
        });
    </script>
@endsection
