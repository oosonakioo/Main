@extends('layouts.admin')

@section('header', trans('admin.delete-history'))

@section('content')
<div class="box box-solid">
    <div class="box-body">
        <table id="table-result" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th>{{ trans('layout.email') }}</th>
                <th>{{ trans('layout.subscribe-date') }}</th>
                <th>{{ trans('layout.subscribe-delete') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($subscribes as $cat)
                <tr id="row-{{ $cat->id }}">
                    <td>{{ $cat->email }}</td>
                    <td>{{ Helper::datetime($cat->created_at, 'd F Y H:i:s') }}</td>
                    <td>{{ Helper::datetime($cat->deleted_at, 'd F Y H:i:s') }}</td>
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
              "order": [[ 2, "desc" ]]
            });
        });
    </script>
@endsection
