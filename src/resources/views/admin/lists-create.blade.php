@extends('layouts.admin')

@section('header', trans('admin.lists-'. $menu))

@section('content')

    {{--*/
      $active_image = false;
      $active_value = false;
      $active_option = false;
      $recommend_size = "";

      $txt_label = "";
      $txt_placeholder = "";
      switch ($menu) {
        case 'contact':
            $active_value = true;
            $txt_label = trans('admin.subcontent-location'). ' ('. trans('admin.subcontent-lat'). ', '. trans('admin.subcontent-lng'). ')';
            $txt_placeholder = trans('admin.subcontent-lat'). ', '. trans('admin.subcontent-lng');
            break;

        case 'careers' :
            $active_option = true;
            break;
      }
    /*--}}

    <form class="form-horizontal" enctype="multipart/form-data" method="POST"
          action="{{ $lists->exists
                    ? Helper::url('admin/lists/'. $menu. '/'.$lists->id)
                    : Helper::url('admin/lists/'. $menu. '/')
                }}">
        <input name="_method" type="hidden" value="{{ $lists->exists ? 'PUT' : 'POST' }}"/>
        {!! csrf_field() !!}

        @include('errors.validator')

        @if ($menu == 'award' || $menu == 'experience')
          @foreach(LaravelLocalization::getSupportedLocales() as $locale => $properties)
              @include('admin.lists-form-no-detail', [
                  'lists' => $lists,
                  'locale' => $locale])
          @endforeach
        @else
          @foreach(LaravelLocalization::getSupportedLocales() as $locale => $properties)
              @include('admin.lists-form', [
                  'lists' => $lists,
                  'locale' => $locale])
          @endforeach
        @endif

        <input type="hidden" name="menu" value="{{ $menu }}">

        @if($active_option)
        <div class="form-group">
            <label class="form-control-static col-md-2" for="option">{{ trans('admin.subcontent-avaiable') }}</label>
            <div class="col-md-2">
                <input class="form-control" type="number" id="option" name="option" maxlength="4"
                       value="{{ Helper::getValue($lists, 'option', $errors) }}"/>
            </div>
        </div>
        @else
          <input type="hidden" name="option" value="">
        @endif


        @if($active_value)
        <div class="form-group">
            <label class="form-control-static col-md-2" for="value">
                {{ $txt_label }}
            </label>
            <div class="col-md-5">
                <input class="form-control" type="text" id="value" name="value"
                        placeholder="{{ $txt_placeholder }}"
                        value="{{ Helper::getValue($lists, 'value', $errors) }}"/>
            </div>
        </div>
        @else
          <input type="hidden" name="value" value="">
        @endif


        @if($active_image)
          @include('admin.view.view-image-list', [ 'obj' => $lists, 'recommend_size' => $recommend_size])
        @else
          <input type="hidden" name="image" value="">
        @endif

        <div class="form-group">
            <label class="form-control-static col-md-2" for="sort">{{ trans('admin.contents-sort') }}</label>
            <div class="col-md-2">
                <input class="form-control" type="number" id="sort" name="sort" maxlength="3"
                       value="{{ Helper::getValue($lists, 'sort', $errors) }}"/>
            </div>
        </div>

        @include('admin.view.view-status-submit', [ 'obj' => $lists])

    </form>
@endsection
