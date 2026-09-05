(function ($) {
    function focusComposeEditor($el) {
        var editor = $el.closest('.compose-editor-wrap').find('.compose-editor').get(0);
        if (editor) {
            editor.focus();
        }
        return editor;
    }

    function replaceFontSizeTags(editor, px) {
        if (!editor) {
            return;
        }
        $(editor).find('font[size="7"]').each(function () {
            var $font = $(this);
            $font.replaceWith(
                $('<span></span>').css('font-size', px).append($font.contents())
            );
        });
    }

    $(document).on('change', '.compose-toolbar .compose-font-family', function () {
        var editor = focusComposeEditor($(this));
        document.execCommand('styleWithCSS', false, true);
        document.execCommand('fontName', false, this.value);
        if (editor) {
            editor.focus();
        }
    });

    $(document).on('change', '.compose-toolbar .compose-font-size', function () {
        var px = this.value;
        var editor = focusComposeEditor($(this));
        document.execCommand('styleWithCSS', false, true);
        document.execCommand('fontSize', false, '7');
        replaceFontSizeTags(editor, px);
        if (editor) {
            editor.focus();
        }
    });
})(jQuery);
