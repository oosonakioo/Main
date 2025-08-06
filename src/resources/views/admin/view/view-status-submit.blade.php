<div class="form-group">
    <label class="form-control-static col-md-2" for="active">{{ trans('admin.contents-status') }}</label>
    <div class="col-md-6">
        <input class="form-control-static" type="checkbox" id="active" name="active"
               value="active" {{
                    $obj->exists
                        ? Helper::isSelected($obj->active, 'checked')
                        : Helper::isSelected(old('active'), 'checked')  }} />
    </div>
</div>
<button type="submit" class="btn btn-success center-block">
    <span class="glyphicon glyphicon-ok-circle"></span>
    {{ $obj->exists ? trans('admin.contents-save') : trans('admin.contents-create') }}
</button>
