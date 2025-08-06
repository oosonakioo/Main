<div class="form-group">
    <label class="form-control-static col-md-2" for="image">{{ trans('admin.contents-image') }} {{ $recommend_size }}</label>
    <div class="col-md-6">
        <div class="input-group">
            <input class="form-control" type="text" id="image" name="image"
                   value="{{ Helper::getValue($obj, 'image', $errors) }}" readonly/>
                    <span class="input-group-btn">
                        <button class="btn popup_selector" data-inputid="image">{{ trans('admin.contents-browse') }}</button>
                    </span>
        </div>
    </div>
</div>

@if ($obj->image != "")
    <div class="form-group">
        <div class="col-md-2"></div>
        <div class="col-md-4">
            <img src="{{ url($obj->image) }}" class="img-thumbnail">
        </div>
    </div>
@endif
