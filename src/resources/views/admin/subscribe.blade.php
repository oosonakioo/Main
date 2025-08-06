@extends('layouts.admin')

@section('header', trans('admin.subscribe'))

@section('content')
  <div class="box box-solid">
      <div class="box-body">
          <table id="table-result" class="table table-bordered table-hover">
              <thead>
              <tr>
                  <th>{{ trans('layout.email') }}</th>
                  <th>{{ trans('layout.subscribe-date') }}</th>
                  <th width="7%">{{ trans('admin.contents-delete') }}</th>
              </tr>
              </thead>
              <tbody>
              @foreach($subscribes as $cat)
                  <tr id="row-{{ $cat->id }}">
                      <td>{{ $cat->email }}</td>
                      <td>{{ Helper::datetime($cat->created_at, 'd F Y H:i:s') }}</td>
                      <td align="center">
                          <a class="btn btn-sm btn-danger delete" href="#" data-id="{{ $cat->id }}">{{ trans('admin.contents-delete') }}</a>
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
              "order": [[ 1, "desc" ]]
            });

            @include('admin.view.view-delete-js', ['url' => 'admin/subscribe']);
        });
    </script>
@endsection
