(function ($) {
    function focusComposeEditor($input) {
        $input.closest('.compose-editor-wrap').find('.compose-editor').focus();
    }

    $(document).on('input change', '.compose-toolbar .compose-font-color', function () {
        focusComposeEditor($(this));
        document.execCommand('foreColor', false, this.value);
    });

    $(document).on('input change', '.compose-toolbar .compose-highlight-color', function () {
        focusComposeEditor($(this));
        if (!document.execCommand('hiliteColor', false, this.value)) {
            document.execCommand('backColor', false, this.value);
        }
    });
})(jQuery);
