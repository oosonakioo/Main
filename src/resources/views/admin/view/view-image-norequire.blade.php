<?php
    $imageField = empty($key) ? "images" : $key;
?>
<div class="form-group">
    <label class="form-control-static col-md-2" for="{{ $imageField }}">{{ trans('admin.contents-image') }} {!! $label ?? "" !!}</label>
    <div class="col-md-6">
        <div class="input-group">
            <input class="form-control" type="text" id="{{ $imageField }}" name="{{ $imageField }}"
                   value="{{ Helper::getValue($obj, $imageField, $errors) }}"/>
                    <span class="input-group-btn">
                        <button class="btn popup_selector" data-inputid="{{ $imageField }}">{{ trans('admin.contents-browse') }}</button>
                    </span>
        </div>
    </div>
</div>

@if ($obj[$imageField] != "")
    <div class="form-group">
        <div class="col-md-2"></div>
        <div class="col-md-2">
            <img src="{{ url($obj[$imageField]) }}" class="img-thumbnail">
        </div>
    </div>
@endif
