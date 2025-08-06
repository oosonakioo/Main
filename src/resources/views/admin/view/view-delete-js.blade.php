$("#table-result").on("click", ".delete", function(e) {
    e.preventDefault();
    if (confirm("{{ trans('layout.confirm-delete') }}")) {
        var id = $(this).data('id');
        $.ajax({
            url: '{{ url($url) }}/' + id,
            type: 'DELETE',
            data: {
                '_token': '{{ csrf_token() }}',
            }
        }).done(function (data) {
            if (data == id) {
                $('#row-' + data).remove();
            }
            else {
                alert('{{ trans("error.try") }}');
            }
        });
    }
    return false;
});
