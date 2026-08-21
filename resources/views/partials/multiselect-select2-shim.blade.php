{{-- Bootstrap Multiselect → Select2 shim (Tailwind v2). Keeps legacy .multiselect() calls working. --}}
<script>
(function ($) {
    if (!$ || !$.fn || typeof $.fn.select2 !== 'function') {
        return;
    }

    if (typeof window.initializeSearchableFilterMultiselect !== 'function') {
        window.initializeSearchableFilterMultiselect = function (selector, options) {
            var incoming = options || {};
            var onChange = incoming.onChange;
            var onSelectAll = incoming.onSelectAll;
            var onDeselectAll = incoming.onDeselectAll;
            var legacyKeys = {
                enableCaseInsensitiveFiltering: true,
                includeResetOption: true,
                resetText: true,
                filterPlaceholder: true,
                maxHeight: true,
                buttonWidth: true,
                nonSelectedText: true,
                numberDisplayed: true,
                nSelectedText: true,
                buttonText: true,
                buttonTitle: true,
                onChange: true,
                onSelectAll: true,
                onDeselectAll: true,
                includeSelectAllOption: true,
                enableFiltering: true,
                buttonClass: true,
                templates: true,
                allSelectedText: true
            };

            var settings = $.extend({
                placeholder: incoming.nonSelectedText || 'Click here',
                allowClear: incoming.includeResetOption !== false,
                width: incoming.buttonWidth || '100%',
                closeOnSelect: false,
                minimumResultsForSearch: 0
            }, incoming);

            Object.keys(legacyKeys).forEach(function (key) {
                delete settings[key];
            });

            $(selector).each(function () {
                var $select = $(this);

                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.off('.searchableFilter');
                    $select.select2('destroy');
                }

                if (!$select.parent().hasClass('searchable-filter-wrapper')) {
                    $select.wrap('<div class="searchable-filter-wrapper"></div>');
                }

                $select.select2(settings);

                $select.off('change.searchableFilter').on('change.searchableFilter', function () {
                    if ($select.data('suppress-searchable-change')) {
                        return;
                    }

                    if (typeof onChange === 'function') {
                        onChange();
                        return;
                    }

                    var values = $select.val() || [];
                    var optionCount = $select.find('option').not('[value=""]').length;

                    if (values.length === 0 && typeof onDeselectAll === 'function') {
                        onDeselectAll();
                        return;
                    }

                    if (values.length === optionCount && optionCount > 0 && typeof onSelectAll === 'function') {
                        onSelectAll();
                    }
                });
            });
        };
    }

    if (typeof window.clearSearchableFilterMultiselect !== 'function') {
        window.clearSearchableFilterMultiselect = function (selector, triggerChange) {
            $(selector).each(function () {
                var $select = $(this);
                $select.find('option').prop('selected', false);
                $select.val(null);

                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.data('suppress-searchable-change', true);
                    $select.trigger('change');
                    $select.removeData('suppress-searchable-change');
                }

                if (triggerChange !== false) {
                    $select.trigger('change');
                }
            });
        };
    }

    function syncColumnPickerPanel($select) {
        var $panel = $select.data('mcColumnPickerPanel');
        if (!$panel || !$panel.length) {
            return;
        }

        $panel.find('input[data-mc-col-value]').each(function () {
            var value = String($(this).attr('data-mc-col-value'));
            var selected = $select.find('option').filter(function () {
                return String(this.value) === value;
            }).prop('selected');
            $(this).prop('checked', !!selected);
        });
    }

    function initColumnPicker($select, options) {
        options = options || {};

        if ($select.data('mcColumnPickerReady')) {
            syncColumnPickerPanel($select);
            return;
        }

        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }

        var $parent = $select.parent();
        if ($parent.hasClass('searchable-filter-wrapper')) {
            $select.unwrap();
            $parent = $select.parent();
        }

        $select.addClass('mc-column-picker-native').attr('aria-hidden', 'true').hide();

        var $wrap = $('<div class="mc-column-picker"></div>');
        var $btn = $('<button type="button" class="mc-column-picker__btn" title="Show / hide filters" aria-label="Show / hide filters"><i class="ti-filter"></i></button>');
        var $panel = $('<div class="mc-column-picker__panel" role="menu"></div>');

        $select.find('option').each(function () {
            var value = String(this.value);
            var label = $(this).text();
            var id = 'mc-col-' + value.replace(/[^a-z0-9]+/gi, '-').toLowerCase();
            var $row = $(
                '<label class="mc-column-picker__row" for="' + id + '">' +
                    '<input type="checkbox" id="' + id + '" data-mc-col-value="' + $('<div>').text(value).html() + '">' +
                    '<span>' + $('<div>').text(label).html() + '</span>' +
                '</label>'
            );
            $row.find('input').prop('checked', $(this).prop('selected'));
            $panel.append($row);
        });

        $wrap.append($btn).append($panel);
        $select.after($wrap);

        $select.data('mcColumnPickerReady', true);
        $select.data('mcColumnPickerPanel', $panel);
        $select.data('mcColumnPickerBtn', $btn);
        $select.data('mcColumnPickerOptions', options);

        function notifyChange(option, checked) {
            if (typeof options.onChange === 'function') {
                options.onChange(option, checked);
            }
        }

        $btn.on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $('.mc-column-picker__panel').not($panel).hide();
            $panel.toggle();
        });

        $panel.on('click', function (e) {
            e.stopPropagation();
        });

        $panel.on('change', 'input[data-mc-col-value]', function () {
            var value = String($(this).attr('data-mc-col-value'));
            var checked = $(this).is(':checked');
            var $option = $select.find('option').filter(function () {
                return String(this.value) === value;
            });
            $option.prop('selected', checked);
            $select.trigger('change');
            notifyChange($option, checked);
        });

        $(document).on('click.mcColumnPicker', function () {
            $panel.hide();
        });
    }

    if ($.fn.multiselect && $.fn.multiselect.__mcSelect2Shim) {
        return;
    }

    $.fn.multiselect = function (methodOrOptions) {
        var $els = this;

        if (typeof methodOrOptions === 'string') {
            var method = methodOrOptions;

            $els.each(function () {
                var $select = $(this);

                if (method === 'selectAll') {
                    $select.find('option').prop('selected', true);
                    var values = $select.find('option').map(function () {
                        return this.value;
                    }).get();
                    $select.val(values);
                    syncColumnPickerPanel($select);
                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.trigger('change');
                    } else {
                        $select.trigger('change');
                    }
                    return;
                }

                if (method === 'deselectAll' || method === 'clearSelection') {
                    if ($select.data('mcColumnPickerReady')) {
                        $select.find('option').prop('selected', false);
                        $select.val([]);
                        syncColumnPickerPanel($select);
                        $select.trigger('change');
                        return;
                    }
                    window.clearSearchableFilterMultiselect($select, true);
                    return;
                }

                if (method === 'updateButtonText' || method === 'refresh' || method === 'rebuild') {
                    syncColumnPickerPanel($select);
                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.trigger('change.select2');
                    }
                    return;
                }

                if (method === 'destroy') {
                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.select2('destroy');
                    }
                }
            });

            return $els;
        }

        var options = methodOrOptions || {};

        $els.each(function () {
            var $select = $(this);

            // Column visibility picker — icon button + checkbox panel (not Select2 chips)
            if ($select.is('#filter-multiselect') || $select.data('mcColumnPicker') === true) {
                initColumnPicker($select, options);
                return;
            }

            window.initializeSearchableFilterMultiselect($select, options);
        });

        return $els;
    };

    $.fn.multiselect.__mcSelect2Shim = true;
})(window.jQuery);
</script>
