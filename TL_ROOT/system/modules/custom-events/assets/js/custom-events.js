(function ($) {
    $(document).ready(function () {
        // Do not submit the fineuploader form if there wasn't uploaded any file.
        $('form').submit(function (event) {
            if ($(this).find('.widget-fineuploader').length) {
                if (!$(this).find('.widget-fineuploader .qq-upload-success').length) {
                    event.preventDefault();
                    alert('Bitte laden Sie vor dem Absenden des Formulars mindestens eine Datei auf den Server.');
                }
            }
        });


        // Delete images from the event_gallery in the FE
        $('.delete-event-image').click(function (ev) {
            ev.preventDefault();
            ev.stopPropagation();

            var el = this;
            var data = {
                'file': $(el).closest('*[data-file]').attr('data-file'),
                'delete_file': 'true'
            };

            $.ajax({
                url: window.location.href,
                data: data,
                success: function (data) {
                    if (data.status == 'success') {
                        $(el).closest('*[data-file]').remove();
                    }
                },
                dataType: "json"
            });
        });
    });
})(jQuery);