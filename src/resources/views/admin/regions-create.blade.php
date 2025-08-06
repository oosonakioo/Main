@extends('layouts.admin')

@section('header', trans('admin.categories-'. $menu))

@section('content')

    {{--*/
      $active_image = false;

      switch ($menu) {
        case 'region':
          $active_image = true;
          break;
        case 'province':
          $active_image = true;
      }
    /*--}}

    <form class="form-horizontal" enctype="multipart/form-data" method="POST"
          action="{{ $regions->exists
                    ? Helper::url('admin/'. $menu. '/regions/'.$regions->id)
                    : Helper::url('admin/'. $menu. '/regions/')
                }}">
        <input name="_method" type="hidden" value="{{ $regions->exists ? 'PUT' : 'POST' }}"/>
        {!! csrf_field() !!}

        @include('errors.validator')

        @foreach(LaravelLocalization::getSupportedLocales() as $locale => $properties)
            @include('admin.regions-form', [
                'regions' => $regions,
                'locale' => $locale])
        @endforeach

        <input type="hidden" name="menu" value="{{ $menu }}">

        @if($menu == 'region')
            <div class="form-group">
                <label class="form-control-static col-md-2" for="group_id">
                    {{ trans('admin.lists-group') }}
                </label>
                <div class="col-md-4">
                    <select class="form-control" id="group_id" name="group_id">
                        @foreach($groups as $value)
                            <option {{ Helper::isSelected($regions->group_id == $value->id) }}
                                value="{{ $value->id }}">{{ $value->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @else
          <input type="hidden" name="group_id" value="">
        @endif

        @if(isset($parents))
            <div class="form-group">
                <label class="form-control-static col-md-2" for="parents">
                    {{ $parentText }}
                </label>
                <div class="col-md-4">
                    <select class="form-control" id="parents" name="parents">
                        @foreach($parents as $value)
                            <option {{ Helper::isSelected($regions->parent_regions_id == $value->main_id) }}
                                value="{{ $value->main_id }}">{{ $value->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif

        @if($active_image)
          @include('admin.view.view-image-upload', [ 'obj' => $regions])
        @else
          <input type="hidden" name="image" value="">
        @endif

        <div class="form-group">
            <label class="form-control-static col-md-2" for="sort">{{ trans('admin.contents-sort') }}</label>
            <div class="col-md-2">
                <input class="form-control" type="number" id="sort" name="sort" maxlength="3"
                       value="{{ Helper::getValue($regions, 'sort', $errors) }}"/>
            </div>
        </div>

        @include('admin.view.view-status-submit', [ 'obj' => $regions])
    </form>
@endsection
