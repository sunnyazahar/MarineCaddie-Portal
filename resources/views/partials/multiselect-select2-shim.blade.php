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

    function getMcColumnPickerUserId() {
        var fromBody = document.body && document.body.getAttribute('data-mc-user-id');
        if (fromBody) {
            return String(fromBody);
        }

        var meta = document.querySelector('meta[name="mc-user-id"]');
        if (meta && meta.content) {
            return String(meta.content);
        }

        return 'guest';
    }

    function getMcColumnPickerOptions($select) {
        return $select.data('mcColumnPickerOptions') || {};
    }

    function getMcColumnPickerStorageKey($select, options) {
        options = options || getMcColumnPickerOptions($select);
        var pageKey = options.storageKey
            || $select.attr('data-storage-key')
            || (window.location.pathname + '::' + ($select.attr('id') || $select.attr('name') || 'column-picker'));

        return 'mc.colPicker.' + getMcColumnPickerUserId() + '.' + pageKey;
    }

    function shouldPersistMcColumnPicker($select, options) {
        options = options || getMcColumnPickerOptions($select);
        return options.persistColumnPicker !== false;
    }

    function normalizeMcColumnPickerSavedValues($select, saved) {
        if (!Array.isArray(saved)) {
            return null;
        }

        var available = {};
        $select.find('option').each(function () {
            available[String(this.value)] = true;
        });

        var normalized = saved.filter(function (value) {
            return !!available[String(value)];
        });

        if (saved.length > 0 && normalized.length === 0) {
            return null;
        }

        if (normalized.length === 0) {
            return null;
        }

        return normalized;
    }

    function loadMcColumnPickerSelection($select, options) {
        if (!shouldPersistMcColumnPicker($select, options)) {
            return null;
        }

        try {
            var raw = window.localStorage.getItem(getMcColumnPickerStorageKey($select, options));
            if (raw === null || raw === '') {
                return null;
            }

            return normalizeMcColumnPickerSavedValues($select, JSON.parse(raw));
        } catch (e) {
            return null;
        }
    }

    function saveMcColumnPickerSelection($select, options) {
        if (!shouldPersistMcColumnPicker($select, options)) {
            return;
        }

        if ($select.data('mcColumnPickerSuppressSave')) {
            return;
        }

        options = options || getMcColumnPickerOptions($select);
        var values = $select.find('option:selected').map(function () {
            return String(this.value);
        }).get();

        try {
            window.localStorage.setItem(getMcColumnPickerStorageKey($select, options), JSON.stringify(values));
        } catch (e) {
            // ignore quota / private browsing
        }
    }

    function setMcColumnPickerSelection($select, values) {
        var valueMap = {};
        (values || []).forEach(function (value) {
            valueMap[String(value)] = true;
        });

        $select.find('option').each(function () {
            $(this).prop('selected', !!valueMap[String(this.value)]);
        });
        $select.val(values || []);
        syncColumnPickerPanel($select);
    }

    function tryApplyMcColumnPickerSavedSelection($select, options) {
        var saved = loadMcColumnPickerSelection($select, options);
        if (saved === null) {
            return false;
        }

        $select.data('mcColumnPickerSuppressSave', true);
        setMcColumnPickerSelection($select, saved);
        $select.removeData('mcColumnPickerSuppressSave');
        return true;
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

        var showSelectAll = options.includeSelectAllOption !== false;
        var showClearAll = options.includeResetOption !== false;
        if (showSelectAll || showClearAll) {
            var $tools = $('<div class="mc-column-picker__tools"></div>');
            if (showSelectAll) {
                $tools.append('<button type="button" class="mc-column-picker__select-all">Select all</button>');
            }
            if (showClearAll) {
                var clearLabel = options.resetText || 'Clear all';
                $tools.append('<button type="button" class="mc-column-picker__clear-all">' + clearLabel + '</button>');
            }
            $panel.append($tools);
        }

        var $list = $('<div class="mc-column-picker__list"></div>');
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
            $list.append($row);
        });

        $panel.append($list);
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

        function selectAllColumns() {
            $list.find('input[data-mc-col-value]').prop('checked', true);
            $select.find('option').prop('selected', true);
            $select.val($select.find('option').map(function () {
                return this.value;
            }).get());
            $select.trigger('change');
            saveMcColumnPickerSelection($select, options);
            if (typeof options.onSelectAll === 'function') {
                options.onSelectAll();
            }
        }

        function clearAllColumns() {
            $list.find('input[data-mc-col-value]').prop('checked', false);
            $select.find('option').prop('selected', false);
            $select.val([]);
            $select.trigger('change');
            try {
                window.localStorage.removeItem(getMcColumnPickerStorageKey($select, options));
            } catch (e) {
                // ignore quota / private browsing
            }
            if (typeof options.onDeselectAll === 'function') {
                options.onDeselectAll();
            }
        }

        $btn.on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $('.mc-column-picker__panel').not($panel).removeClass('is-open');
            $panel.toggleClass('is-open');
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
            saveMcColumnPickerSelection($select, options);
            notifyChange($option, checked);
        });

        $panel.on('click', '.mc-column-picker__select-all', function (e) {
            e.preventDefault();
            e.stopPropagation();
            selectAllColumns();
        });

        $panel.on('click', '.mc-column-picker__clear-all', function (e) {
            e.preventDefault();
            e.stopPropagation();
            clearAllColumns();
        });

        $(document).on('click.mcColumnPicker', function () {
            $panel.removeClass('is-open');
        });

        tryApplyMcColumnPickerSavedSelection($select, options);
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
                    if ($select.data('mcColumnPickerReady')) {
                        var pickerOptions = getMcColumnPickerOptions($select);
                        if (tryApplyMcColumnPickerSavedSelection($select, pickerOptions)) {
                            $select.trigger('change');
                            return;
                        }
                    }

                    $select.find('option').prop('selected', true);
                    var values = $select.find('option').map(function () {
                        return this.value;
                    }).get();
                    $select.val(values);
                    syncColumnPickerPanel($select);
                    $select.trigger('change');
                    return;
                }

                if (method === 'deselectAll' || method === 'clearSelection') {
                    if ($select.data('mcColumnPickerReady')) {
                        $select.find('option').prop('selected', false);
                        $select.val([]);
                        syncColumnPickerPanel($select);
                        saveMcColumnPickerSelection($select, getMcColumnPickerOptions($select));
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
