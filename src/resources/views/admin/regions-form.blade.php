<?php
$transDomain = 'message';
$titleId = 'title_' . $locale;
$detailId = 'detail_' . $locale;
?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">{{ trans('admin.language', [], $transDomain, $locale) }}</h3>
    </div>
    <div class="box-body">
        <div>
            <label for="{{ $titleId }}">{{ trans('admin.contents-title', [], $transDomain, $locale)}}
                <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="{{ $titleId }}" name="{{ $titleId }}"
                   value="{{ Helper::getValue($regions, 'title', $errors, $locale) }}" required/>
        </div>
        <br/>
        <div>
            <label for="{{ $detailId }}">{{ trans('admin.contents-detail', [], $transDomain, $locale) }}
            </label>
            <textarea class="form-control" id="{{ $detailId }}" name="{{ $detailId }}"
                    >{{ Helper::getValue($regions, 'detail', $errors, $locale) }}</textarea>
        </div>
        <!--<input type="hidden" name="{{ $detailId }}" id="{{ $detailId }}" value="">-->
    </div>
</div>
<script type="text/javascript">
    $(function () {
        CKEDITOR.replace('{{ $detailId }}', {
            language: '{{ $locale }}'
        });
    });
</script>
