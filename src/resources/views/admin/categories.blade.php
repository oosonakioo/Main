@extends('layouts.admin')

@section('header', trans('admin.categories-'. $menu))

@section('content')
    <div class="box box-solid">
        <div class="box-header with-border">
            <a class="btn btn-success" href="{{ Helper::url('admin/'. $menu. '/categories/create') }}">
                <i class="fa fa-plus"></i> {{ trans('admin.contents-create') }}</a>
        </div>
        <div class="box-body">
            <table id="table-result" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Title</th>
                    <th>หัวข้อ</th>
                    @if ($menu == 'contact-issue-topic')
                      <th>{{ trans('admin.contents-contactpoint') }}</th>
                    @endif
                    <th>{{ trans('admin.contents-sort') }}</th>
                    <th width="7%">{{ trans('admin.contents-status') }}</th>
                    <th width="7%">{{ trans('admin.contents-edit') }}</th>
                    <th width="7%">{{ trans('admin.contents-delete') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($categories as $cat)
                    <tr id="row-{{ $cat->id }}">
                        <td>{{ $cat->translate('en')->title }}</td>
                        <td>{{ $cat->translate('th')->title }}</td>
                        @if ($menu == 'contact-issue-topic')
                          <td>{{ $cat->value }}</td>
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
                               href="{{ Helper::url('admin/'. $menu. '/categories/' . $cat->id . '/edit') }}">{{ trans('admin.contents-edit') }}</a>
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

    @if ($menu == 'contact-issue-topic')
      {{--*/ $startitem = 3 /*--}}
    @else
      {{--*/ $startitem = 2 /*--}}
    @endif
@endsection

@section('script')
    <script type="text/javascript">
        $(function () {
            $('#table-result').DataTable({
              "order": [[ {{ $startitem }}, "asc" ]]
            });

            @include('admin.view.view-delete-js', ['url' => 'admin/'. $menu. '/categories'])
        });
    </script>
@endsection
