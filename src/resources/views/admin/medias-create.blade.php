@extends('layouts.admin')

@if ($menu == 'gallerys')
  @section('header', trans('admin.downloadgallerys'))
@else
  @section('header', trans('admin.downloadvideos'))
@endif

@section('content')

    {{--*/ $txt_delete = trans('admin.contents-delete'); /*--}}


    <form class="form-horizontal" enctype="multipart/form-data" method="POST"
          action="{{ $medias->exists
                    ? Helper::url('admin/download/'. $menu. '/'. $medias->id)
                    : Helper::url('admin/download/'. $menu)
                }}">
        <input name="_method" type="hidden" value="{{ $medias->exists ? 'PUT' : 'POST' }}"/>
        <input type="hidden" id="menu" name="menu" value="{{ $menu }}">
        {!! csrf_field() !!}

        @include('errors.validator')

        @foreach(LaravelLocalization::getSupportedLocales() as $locale => $properties)
            @include('admin.view.form-no-detail', [
                'obj' => $medias,
                'locale' => $locale])
        @endforeach

        <div class="form-group">
            <label class="form-control-static col-md-2" for="images">{{ trans('admin.contents-image') }} (360 x 400 pixels)</label>
            <div class="col-md-6">
                <div class="input-group">
                    <input class="form-control" type="text" id="images" name="images"
                           value="{{ Helper::getValue($medias, 'images', $errors) }}" readonly/>
                            <span class="input-group-btn">
                                <button class="btn popup_selector" data-inputid="images">{{ trans('admin.contents-browse') }}</button>
                            </span>
                </div>
            </div>
        </div>
        @if ($medias->images != "")
          <div class="form-group">
                <label class="form-control-static col-md-2"></label>
                <div class="col-md-4">
                    <a href="{{ url($medias->images) }}" target="_blank"><img src="{{ url($medias->images) }}" class="img-thumbnail"></a>
                </div>
          </div>
        @endif

        <input type="hidden" id="downloads" name="downloads" value="">
        @if ($menu == 'gallerys')
          <div class="form-group">
            <label class="form-control-static col-md-2">
                {{ trans('admin.gallery') }}
            </label>
            <div class="col-md-10">
              <input type="hidden" id="gallerycount" name="gallerycount" value="{{ $medias->exists ? $gallerycount : 0 }}">
              <table class="table table-hover table-bordered form-table" id="gallery" width="100%">
              	<tr valign="top">
              		<th width="60%" style="text-align:center;border: 1px solid #afafaf;"><label>{{ trans('admin.contents-image') }} (1600 x 1062 pixels)</label></th>
                  <th width="10%" style="text-align:center;border: 1px solid #afafaf;"><label>{{ trans('admin.contents-sort') }}</label></th>
                  <th width="10%" style="text-align:center;border: 1px solid #afafaf;"><a href="javascript:void(0);" id="addgallery">{{ trans('admin.addgallery') }}</a></th>
                </tr>
              </table>
            </div>
          </div>
        @else
        <div class="form-group">
            <label class="form-control-static col-md-2" for="videos">{{ trans('admin.contents-link') }}</label>
            <div class="col-md-4">
                <input class="form-control" type="text" id="videos" name="videos"
                       value="{{ Helper::getValue($medias, 'videos', $errors) }}"/>
            </div>
        </div>
        @endif

        <input type="hidden" id="pin_home_page" name="pin_home_page" value="0">
        <div class="form-group">
            <label class="form-control-static col-md-2" for="sort">{{ trans('admin.contents-sort') }}</label>
            <div class="col-md-2">
                <input class="form-control" type="number" id="sort" name="sort" maxlength="3"
                       value="{{ Helper::getValue($medias, 'sort', $errors) }}"/>
            </div>
        </div>

        @include('admin.view.view-status-submit', [ 'obj' => $medias])
    </form>
@endsection

@section('script')

    @if ($menu == 'gallerys')
      <script>
        $(document).ready(function(){
          var gallerycount = $("#gallerycount").val();
          var gallerynumber = gallerycount;

          $.get('{{ Helper::url('admin/loadgallery') }}/' + {{ $medias->exists ? $medias->id : -99 }}, function (html) {
              $("#gallery").append(html);
              if (html != '') {
                $(".removegallery").on('click',function(){
                  $(this).parent().parent().remove();
                });
              }
          });

          $("#addgallery").click(function(){
            gallerycount = $("#gallerycount").val();
            gallerycount++;
            gallerynumber++;

            var html_addgallery = '<tr valign="top"><td style="border: 1px solid #afafaf;" nowrap><div class="col-md-8 col-xs-6"><input class="form-control" type="text" id="imagesgallery' + gallerynumber + '" name="imagesgallery' + gallerynumber + '" value="" readonly/><span class="input-group-btn"></div><div class="col-md-4 col-xs-6"><button class="btn popup_selector" data-inputid="imagesgallery' + gallerynumber + '">{{ trans('admin.contents-browse') }}</button></span></div></td><td style="border: 1px solid #afafaf;"><input class="form-control" type="text" id="imagessort" name="imagessort[]" value="' + gallerycount + '" onkeypress="return isNumber(event)" required></td><td style="border: 1px solid #afafaf;" align="center"><a href="javascript:void(0);" class="removegallery" onclick="reductThis();"><span class="text-danger"><?php echo $txt_delete?></span></a><input type="hidden" id="galleryid" name="galleryid[]" value="-1"><input type="hidden" id="idrequest" name="idrequest[]" value="imagesgallery' + gallerynumber + '"></td></tr>';

            $("#gallery").append(html_addgallery);
            $(".removegallery").on('click',function(){
              $(this).parent().parent().remove();
            });
            $("#gallerycount").val(gallerycount);
          });
        });

        function reductThis() {
          gallerycount = $("#gallerycount").val();
          gallerycount--;
          //console.log(gallerycount);
          $("#gallerycount").val(gallerycount);
        }

        function isNumber(evt) {
          evt = (evt) ? evt : window.event;
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode > 31 && (charCode < 48 || charCode > 57)) {
              return false;
          } else {
            return true;
          }
        }
      </script>
    @endif
@endsection
