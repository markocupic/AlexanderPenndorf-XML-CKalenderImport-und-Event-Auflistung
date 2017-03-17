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
    });
})(jQuery);