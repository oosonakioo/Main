@extends('layouts.admin')

@section('header', $title)

@section('content')
    @include('errors.validator')

    <form class="form-horizontal" method="POST">
        @foreach(LaravelLocalization::getSupportedLocales() as $locale => $properties)
            @include('admin.contents-form', [
                'content' => $content,
                'locale' => $locale])
        @endforeach
        {{ csrf_field() }}
        <button type="submit" class="btn btn-primary">{{ trans('admin.save') }}</button>
    </form>
@endsection
