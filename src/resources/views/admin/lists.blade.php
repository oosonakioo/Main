@extends('layouts.admin')

@section('header', trans('admin.lists-'. $menu))

@section('content')

    {{--*/
      $active_image = false;
      $firstorder = 1;
      $img_width = "";

      switch ($menu) {
        case 'products':
          $firstorder = 1;
          break;
      }
    /*--}}

    <div class="box box-solid">
        <div class="box-header with-border">
            <a class="btn btn-success" href="{{ Helper::url('admin/lists/'. $menu. '/create') }}">
                <i class="fa fa-plus"></i> {{ trans('admin.contents-create') }}</a>
        </div>
        <div class="box-body">
            <table id="table-result" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>หัวข้อ</th>
                    @if ($active_image)
                      <th>{{ trans('admin.contents-image') }}</th>
                    @endif
                    <th>{{ trans('admin.contents-sort') }}</th>
                    <th width="7%">{{ trans('admin.contents-status') }}</th>
                    <th width="7%">{{ trans('admin.contents-edit') }}</th>
                    <th width="7%">{{ trans('admin.contents-delete') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($lists as $cat)
                    <tr id="row-{{ $cat->id }}">
                        <td>{{ $cat->translate('th')->title }}</td>
                        @if ($active_image)
                          <td>
                            @if ($cat->image != '')
                              <img src="{{ asset($cat->image) }}" class="img-thumbnail" {{ $img_width }}>
                            @else
                              <span class="text-danger">{{ trans('admin.contents-noimage') }}</span>
                            @endif
                          </td>
                        @endif
                        <td>{{ $cat->sort }}</td>
                        <td align="center">
                            @if($cat->active)
                                <span class="label label-success">{{ trans('admin.contents-active') }}</span>
                            @else
                                <span class="label label-danger">{{ trans('admin.contents-inactive') }}</span>
                            @endif
                        </td>
                        <td align="center">
                            <a class="btn btn-sm btn-warning"
                               href="{{ Helper::url('admin/lists/'. $menu. '/' . $cat->id . '/edit') }}">{{ trans('admin.contents-edit') }}</a>
                        </td>
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
              "order": [[ {{ $firstorder }}, "asc" ]]
            });

            @include('admin.view.view-delete-js', ['url' => 'admin/lists/'. $menu])
        });
    </script>
@endsection
