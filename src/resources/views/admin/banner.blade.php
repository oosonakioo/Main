@extends('layouts.admin')

@section('header', trans('admin.banners'))

@section('content')
    <form class="form-horizontal" method="POST">
        <div class="box box-primary">
            <div class="box-body">

                @include('errors.validator')

                {{--*/
                  $loop = 0;
                  $shownumber = 0;
                /*--}}

                @foreach($banners as $index => $banner)
                    {{--*/
                      $loop++;
                      $shownumber++;
                    /*--}}

                    @if($loop == 1)
                      <h3>{{ trans('admin.contents-pc') }}</h3>
                    @endif

                    @if($loop == 6)
                      {{--*/
                        $shownumber = $shownumber - 5;
                      /*--}}
                      <h3>{{ trans('admin.contents-mobile') }}</h3>
                    @endif

                    @if ($banner->images != "")
                      <div class="form-group">
                          <label class="form-control-static col-md-2 text-right col-md-2 col-xs-4 col-sm-4 col-lg-2"></label>
                          <div class="col-md-6 col-xs-6 col-sm-6 col-lg-6">
                              <img src="{{ url($banner->images) }}" class="img-thumbnail">
                          </div>
                      </div>
                    @endif

                    <div class="form-group">
                        <label class="form-control-static text-right col-md-2 col-xs-4 col-sm-4 col-lg-2" for="image{{ $index }}">{{ trans('admin.banners') }}&nbsp;#{{ $shownumber }}</label>
                        <div class="col-md-6 col-xs-6 col-sm-6 col-lg-6">
                            <div class="input-group">
                                <input class="form-control" type="text" id="image{{ $index }}" name="image{{ $index }}"
                                       value="{{ Helper::getValue($banner, 'images', $errors) }}" readonly/>
                                        <span class="input-group-btn">
                                            <button class="btn popup_selector" data-inputid="image{{ $index }}">{{ trans('admin.contents-browse') }}</button>
                                        </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-control-static text-right col-md-2 col-xs-4 col-sm-4 col-lg-2" for="link{{ $index }}">{{ trans('admin.contents-link') }}</label>
                        <div class="col-md-6">
                            <input class="form-control" type="text" id="link{{ $index }}" name="link{{ $index }}" placeholder=""
                                   value="{{ Helper::getValue($banner, 'link', $errors) }}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-control-static text-right col-md-2 col-xs-4 col-sm-4 col-lg-2" for="active{{ $index }}">{{ trans('admin.contents-status') }}</label>
                        <div class="col-md-6 col-xs-6 col-sm-6 col-lg-6">
                            <input class="form-control-static" type="checkbox" id="active{{ $index }}" name="active{{ $index }}"
                                   value="active" {{
                                        $banner->exists
                                            ? Helper::isSelected($banner->active, 'checked')
                                            : Helper::isSelected(old('active' . $index), 'checked')  }} />
                        </div>
                    </div>

                    <hr />
                @endforeach
            </div>
            <div class="box-footer">
                <button type="submit" class="btn bg-olive btn-flat center-block">{{ trans('admin.save') }}</button>
            </div>
        </div>
        {{ csrf_field() }}
    </form>
@endsection
