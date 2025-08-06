@extends('layouts.admin')

@section('header', trans('admin.categories-'. $menu))

@section('content')
    <form class="form-horizontal" enctype="multipart/form-data" method="POST"
          action="{{ $category->exists
                    ? Helper::url('admin/'. $menu. '/categories/'.$category->id)
                    : Helper::url('admin/'. $menu. '/categories/')
                }}">
        <input name="_method" type="hidden" value="{{ $category->exists ? 'PUT' : 'POST' }}"/>
        {!! csrf_field() !!}

        @include('errors.validator')

        @foreach(LaravelLocalization::getSupportedLocales() as $locale => $properties)
            @include('admin.categories-form', [
                'category' => $category,
                'locale' => $locale])
        @endforeach

        <input type="hidden" name="menu" value="{{ $menu }}">

        @if ($menu == 'contact-issue-topic')
          <div class="form-group">
              <label class="form-control-static col-md-2" for="value">{{ trans('admin.contents-contactpoint') }} <span class="text-danger">*</span></label>
              <div class="col-md-4">
                  <input class="form-control" type="text" id="value" name="value"
                         value="{{ Helper::getValue($category, 'value', $errors) }}" required="required"/>
              </div>
          </div>
        @else
          <input type="hidden" name="value" value="">
        @endif

        <div class="form-group">
            <label class="form-control-static col-md-2" for="sort">{{ trans('admin.contents-sort') }}</label>
            <div class="col-md-2">
                <input class="form-control" type="number" id="sort" name="sort" maxlength="3"
                       value="{{ Helper::getValue($category, 'sort', $errors) }}"/>
            </div>
        </div>

        @include('admin.view.view-status-submit', [ 'obj' => $category])
    </form>
@endsection
