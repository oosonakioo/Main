@extends('layouts.admin')

@section('header', trans('admin.news'))

@section('content')
    <form class="form-horizontal" enctype="multipart/form-data" method="POST"
          action="{{ $news->exists
                    ? Helper::url('admin/news/'.$news->id)
                    : Helper::url('admin/news')
                }}">
        <input name="_method" type="hidden" value="{{ $news->exists ? 'PUT' : 'POST' }}"/>
        {!! csrf_field() !!}

        @include('errors.validator')

        @foreach(LaravelLocalization::getSupportedLocales() as $locale => $properties)
            @include('admin.view.form', [
                'obj' => $news,
                'locale' => $locale])
        @endforeach

        <div class="form-group">
            <label class="form-control-static col-md-2" for="category">{{ trans('admin.contents-category') }}</label>
            <div class="col-md-3">
                <select class="form-control" id="category" name="category">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ Helper::isSelected($category->id == $news->news_category_id) }}>{{ $category->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @include('admin.view.view-image-upload', [ 'obj' => $news])

        <div class="form-group">
            <label class="form-control-static col-md-2" for="news_date">{{ trans('admin.date') }}</label>
            <div class="col-md-2">
                <div class="input-group">
                    <input class="form-control datepicker" type="text" id="news_date" name="news_date" readonly
                           value="{{ Helper::datetime($news->news_date, 'Y/m/d', true) }}" placeholder="" required/>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-control-static col-md-2" for="sort">{{ trans('admin.contents-sort') }}</label>
            <div class="col-md-2">
                <input class="form-control" type="number" id="sort" name="sort" maxlength="3"
                       value="{{ Helper::getValue($news, 'sort', $errors) }}"/>
            </div>
        </div>

        <div class="form-group">
            <label class="form-control-static col-md-2" for="pin_home_page">{{ trans('admin.pin-to-home') }}</label>
            <div class="col-md-6">
                <input class="form-control-static" type="checkbox" id="pin_home_page" name="pin_home_page"
                       value="pin_home_page" {{
                    $news->exists
                        ? Helper::isSelected($news->pin_home_page, 'checked')
                        : Helper::isSelected(old('pin_home_page'), 'checked')  }} />
            </div>
        </div>

        @include('admin.view.view-status-submit', [ 'obj' => $news])
    </form>
@endsection

@section('script')
    <script>
        $(function () {
            $('#news_date').datepicker({
                format: "yyyy/mm/dd",
                language: "{{ LaravelLocalization::getCurrentLocale() }}"
            });
        });
    </script>
@endsection
