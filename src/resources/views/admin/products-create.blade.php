@extends('layouts.admin')

@section('header', trans('admin.product-'. $menu))

@section('content')
    <form class="form-horizontal" enctype="multipart/form-data" method="POST"
          action="{{ $product->exists
                    ? Helper::url('admin/'. $menu. '/product/'.$product->id)
                    : Helper::url('admin/'. $menu. '/product/')
                }}">
        <input name="_method" type="hidden" value="{{ $product->exists ? 'PUT' : 'POST' }}"/>
        {!! csrf_field() !!}

        @include('errors.validator')

        @foreach(LaravelLocalization::getSupportedLocales() as $locale => $properties)
            @include('admin.products-form', [
                'product' => $product,
                'locale' => $locale])
        @endforeach

        <div class="form-group">
            <label class="form-control-static col-md-2" for="category">{{ trans('admin.contents-category') }}</label>
            <div class="col-md-3">
                <select class="form-control" id="category" name="category">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ Helper::isSelected($category->id == $product->categories_id) }}>{{ $category->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!--<div class="form-group">
            <label class="form-control-static col-md-2" for="price">ราคา</label>
            <div class="col-md-3">
                <input class="form-control" type="number" id="price" name="price"
                       value="{{ Helper::getValue($product, 'price', $errors) }}" required/>
            </div>
            <span class="form-control-static col-md-1">บาท</span>
        </div>-->

        <input type="hidden" name="menu" value="{{ $menu }}">

        <div class="form-group">
            <label class="form-control-static col-md-2" for="sort">{{ trans('admin.contents-sort') }}</label>
            <div class="col-md-2">
                <input class="form-control" type="number" id="sort" name="sort" maxlength="3"
                       value="{{ Helper::getValue($product, 'sort', $errors) }}"/>
            </div>
        </div>

        @if ($menu == 'faq')
          <input type="hidden" name="link" value="">
          <input type="hidden" name="images" value="">
        @else
          <div class="form-group">
              <label class="form-control-static col-md-2" for="link">{{ trans('admin.contents-link') }}</label>
              <div class="col-md-3">
                  <input class="form-control" type="text" id="link" name="link"
                         value="{{ Helper::getValue($product, 'link', $errors) }}"/>
              </div>
          </div>

          @include('admin.view.view-image-upload', [ 'obj' => $product])
        @endif

        @include('admin.view.view-status-submit', [ 'obj' => $product])
    </form>
@endsection
