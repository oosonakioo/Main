<div class="form-group">
    <label class="form-control-static col-md-2" for="images">{{ trans('admin.contents-image') }}</label>
    <div class="col-md-6">
        <div class="input-group">
            <input class="form-control" type="text" id="images" name="images"
                   value="{{ Helper::getValue($obj, 'images', $errors) }}" readonly/>
                    <span class="input-group-btn">
                        <button class="btn popup_selector" data-inputid="images">{{ trans('admin.contents-browse') }}</button>
                    </span>
        </div>
    </div>
</div>

@if ($obj->images != "")
    <div class="form-group">
        <div class="col-md-2"></div>
        <div class="col-md-4">
            <img src="{{ url($obj->images) }}" class="img-thumbnail">
        </div>
    </div>
@endif
