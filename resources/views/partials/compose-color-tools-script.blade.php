(function ($) {
    var savedRange = null;

    function getEditor($picker) {
        return $picker.closest('.compose-editor-wrap').find('.compose-editor').get(0);
    }

    function saveSelection(editor) {
        var sel = window.getSelection();
        if (!editor || !sel || !sel.rangeCount) {
            return;
        }
        if (!editor.contains(sel.anchorNode)) {
            return;
        }
        savedRange = sel.getRangeAt(0).cloneRange();
    }

    function restoreSelection(editor) {
        if (!editor || !savedRange) {
            return;
        }
        editor.focus();
        var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(savedRange);
    }

    function closeAllPalettes() {
        $('.compose-color-picker.is-open').each(function () {
            var $picker = $(this);
            $picker.removeClass('is-open');
            $picker.find('.compose-color-palette').prop('hidden', true);
            $picker.find('.compose-color-tool-btn').attr('aria-expanded', 'false');
        });
    }

    function setIndicator($picker, color) {
        var mode = $picker.data('color-mode');
        var $letter = $picker.find('.compose-color-letter');
        if (mode === 'highlight') {
            if (!color || color === 'transparent') {
                $letter.css({ background: '#fef9c3', 'border-bottom-color': '#eab308' });
            } else {
                $letter.css({ background: color, 'border-bottom-color': color });
            }
            return;
        }
        $letter.css('border-bottom-color', color || '#dc2626');
    }

    function markSelected($palette, color) {
        $palette.find('.compose-color-swatch').removeClass('is-selected');
        if (!color || color === 'transparent') {
            return;
        }
        $palette.find('.compose-color-swatch').filter(function () {
            return String($(this).data('color')).toLowerCase() === String(color).toLowerCase();
        }).first().addClass('is-selected');
    }

    function applyColor($picker, color) {
        var editor = getEditor($picker);
        var mode = $picker.data('color-mode');
        restoreSelection(editor);

        if (mode === 'highlight') {
            if (color === 'transparent') {
                if (!document.execCommand('hiliteColor', false, 'transparent')) {
                    document.execCommand('backColor', false, 'transparent');
                }
            } else if (!document.execCommand('hiliteColor', false, color)) {
                document.execCommand('backColor', false, color);
            }
        } else {
            document.execCommand('foreColor', false, color);
        }

        setIndicator($picker, color);
        markSelected($picker.find('.compose-color-palette'), color);
        $picker.find('.compose-color-native').val(color === 'transparent' ? '#ffffff' : color);
        closeAllPalettes();
        if (editor) {
            editor.focus();
        }
    }

    $(document).on('mousedown', '.compose-toolbar .compose-color-tool-btn', function () {
        saveSelection(getEditor($(this).closest('.compose-color-picker')));
    });

    $(document).on('click', '.compose-toolbar .compose-color-tool-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $picker = $(this).closest('.compose-color-picker');
        var $palette = $picker.find('.compose-color-palette');
        var willOpen = $palette.prop('hidden');

        closeAllPalettes();
        if (!willOpen) {
            return;
        }

        $picker.addClass('is-open');
        $palette.prop('hidden', false);
        $(this).attr('aria-expanded', 'true');
        markSelected($palette, $picker.find('.compose-color-native').val());
    });

    $(document).on('click', '.compose-toolbar .compose-color-swatch, .compose-toolbar .compose-color-auto', function (e) {
        e.preventDefault();
        e.stopPropagation();
        applyColor($(this).closest('.compose-color-picker'), String($(this).data('color')));
    });

    $(document).on('click', '.compose-toolbar .compose-color-more', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var native = $(this).closest('.compose-color-picker').find('.compose-color-native').get(0);
        if (native && typeof native.showPicker === 'function') {
            try {
                native.showPicker();
                return;
            } catch (err) {
                // Fall through to click().
            }
        }
        if (native) {
            native.click();
        }
    });

    $(document).on('input change', '.compose-toolbar .compose-color-native', function () {
        applyColor($(this).closest('.compose-color-picker'), this.value);
    });

    $(document).on('mousedown', '.compose-toolbar .compose-color-palette', function (e) {
        e.preventDefault();
    });

    $(document).on('click', function () {
        closeAllPalettes();
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAllPalettes();
        }
    });
})(jQuery);
