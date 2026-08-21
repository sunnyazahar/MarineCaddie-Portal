<script>
    window.initializeSearchableFilterMultiselect = function(selector, options) {
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

        var incoming = options || {};
        var onChange = incoming.onChange;
        var onSelectAll = incoming.onSelectAll;
        var onDeselectAll = incoming.onDeselectAll;
        var placeholderText = incoming.nonSelectedText || 'Click here';
        var numberDisplayed = typeof incoming.numberDisplayed === 'number' ? incoming.numberDisplayed : 1;

        function escapeHtml(text) {
            return $('<div>').text(text == null ? '' : String(text)).html();
        }

        function summaryLabel($select) {
            var $selected = $select.find('option:selected').filter(function () {
                return $(this).val() !== '';
            });

            if (!$selected.length) {
                return { text: placeholderText, placeholder: true };
            }

            var first = $.trim($selected.first().text());
            if ($selected.length === 1 || numberDisplayed <= 1) {
                return {
                    text: $selected.length === 1 ? first : (first + ', ...'),
                    placeholder: false
                };
            }

            var labels = [];
            $selected.slice(0, numberDisplayed).each(function () {
                labels.push($.trim($(this).text()));
            });
            if ($selected.length > numberDisplayed) {
                labels.push('...');
            }
            return { text: labels.join(', '), placeholder: false };
        }

        function refreshSummary($select) {
            var $rendered = $select.next('.select2-container').find('.select2-selection__rendered');
            if (!$rendered.length) {
                return;
            }

            var summary = summaryLabel($select);
            var $label = $rendered.find('.mc-filter-summary');
            if (!$label.length) {
                $label = $('<span class="mc-filter-summary"></span>');
                $rendered.prepend($label);
            }
            $label.text(summary.text);
            $label.toggleClass('is-placeholder', !!summary.placeholder);
        }

        var settings = $.extend({
            placeholder: placeholderText,
            allowClear: false,
            width: incoming.buttonWidth || '100%',
            closeOnSelect: false,
            minimumResultsForSearch: 0,
            dropdownCssClass: 'mc-filter-select2-dropdown',
            escapeMarkup: function (markup) { return markup; },
            templateResult: function (state) {
                if (!state.id) {
                    return state.text;
                }
                return (
                    '<span class="mc-filter-option">' +
                        '<span class="mc-filter-option__check" aria-hidden="true"></span>' +
                        '<span class="mc-filter-option__label">' + escapeHtml(state.text) + '</span>' +
                    '</span>'
                );
            },
            templateSelection: function () {
                return '';
            }
        }, incoming);

        Object.keys(legacyKeys).forEach(function(key) {
            delete settings[key];
        });

        $(selector).each(function() {
            var $select = $(this);

            if ($select.hasClass('select2-hidden-accessible')) {
                $select.off('.searchableFilter');
                $select.select2('destroy');
            }

            if (!$select.parent().hasClass('searchable-filter-wrapper')) {
                $select.wrap('<div class="searchable-filter-wrapper"></div>');
            } else {
                $select.parent().addClass('searchable-filter-wrapper');
            }

            $select.select2(settings);
            refreshSummary($select);

            $select.off('change.searchableFilter select2:open.searchableFilter select2:close.searchableFilter');

            $select.on('select2:open.searchableFilter', function () {
                window.setTimeout(function () {
                    var $dropdown = $('.select2-container--open .select2-dropdown').last();
                    if (!$dropdown.length) {
                        return;
                    }

                    $dropdown.addClass('mc-filter-select2-dropdown');

                    var $tools = $dropdown.find('.mc-filter-dropdown-tools');
                    if (!$tools.length) {
                        $tools = $(
                            '<div class="mc-filter-dropdown-tools">' +
                                '<input type="search" class="mc-filter-dropdown-search" placeholder="Type here" autocomplete="off" />' +
                                '<button type="button" class="mc-filter-clear">Clear</button>' +
                            '</div>'
                        );
                        $dropdown.prepend($tools);

                        $tools.on('mousedown', function (e) {
                            // Keep dropdown open while interacting with tools
                            e.stopPropagation();
                        });

                        $tools.find('.mc-filter-dropdown-search').on('keyup input', function () {
                            var term = $(this).val();
                            var $inline = $select.next('.select2-container').find('.select2-search__field');
                            if ($inline.length) {
                                $inline.val(term).trigger('input');
                            }
                        });

                        $tools.find('.mc-filter-clear').on('click mousedown', function (e) {
                            e.preventDefault();
                            e.stopPropagation();

                            window.clearSearchableFilterMultiselect($select, true);
                            refreshSummary($select);

                            $tools.find('.mc-filter-dropdown-search').val('');
                            var $inline = $select.next('.select2-container').find('.select2-search__field');
                            if ($inline.length) {
                                $inline.val('').trigger('input');
                            }
                        });
                    }

                    $tools.find('.mc-filter-dropdown-search').val('').trigger('focus');
                }, 0);
            });

            $select.on('change.searchableFilter', function() {
                refreshSummary($select);

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

    window.clearSearchableFilterMultiselect = function(selector, triggerChange) {
        $(selector).each(function() {
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


    window.bindAjaxListFilters = function(config) {
        var tableSelector = config.tableSelector;
        var paginationSelector = config.paginationSelector;
        var indexUrl = config.indexUrl;
        var searchTimer = null;
        var filtersReady = false;
        var requestToken = 0;
        var suppressFilterLoad = false;
        var table = config.existingTable || null;
        var dataTableOptions = $.extend({
            "dom": 'rt',
            "paging": false,
            "info": false,
            "searching": false,
            "ordering": true,
            "order": [],
            "autoWidth": false
        }, config.dataTableOptions || {});

        function shouldLoad() {
            return filtersReady && !suppressFilterLoad;
        }

        function initTable() {
            if ($.fn.DataTable.isDataTable(tableSelector)) {
                return $(tableSelector).DataTable();
            }

            return $(tableSelector).DataTable(dataTableOptions);
        }

        if (!table) {
            table = initTable();
        }

        function replaceRows(html, paginationHtml) {
            table = initTable();
            table.clear();

            var $rows = $('<table><tbody>' + html + '</tbody></table>').find('tr').filter(function () {
                return $(this).find('td[colspan]').length === 0;
            });

            if ($rows.length) {
                table.rows.add($rows);
            }

            table.draw(false);
            if (paginationSelector) {
                $(paginationSelector).html(paginationHtml || '');
            }
            if (typeof config.afterDraw === 'function') {
                config.afterDraw(table);
            }
        }

        function load(page) {
            var params = config.getParams(page || 1);
            var token = ++requestToken;

            $.ajax({
                url: indexUrl,
                method: 'GET',
                data: params,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).done(function (response) {
                if (token !== requestToken) {
                    return;
                }

                replaceRows(response.html, response.pagination);
            });
        }

        if (config.multiselectSelector) {
            initializeSearchableFilterMultiselect(config.multiselectSelector, {
                onChange: function () {
                    if (shouldLoad()) {
                        load(1);
                    }
                },
                onSelectAll: function () {
                    if (shouldLoad()) {
                        load(1);
                    }
                },
                onDeselectAll: function () {
                    if (shouldLoad()) {
                        load(1);
                    }
                }
            });
        }

        if (config.textSelectors) {
            $(config.textSelectors).on('input keyup', function (e) {
                if (e.type === 'keyup' && e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(searchTimer);
                    load(1);
                    return;
                }

                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    load(1);
                }, 200);
            });
        }

        if (config.changeSelectors) {
            $(config.changeSelectors).on('change', function () {
                if (shouldLoad()) {
                    load(1);
                }
            });
        }

        if (paginationSelector) {
            $(paginationSelector).on('click', 'a', function (e) {
                var href = $(this).attr('href');
                if (!href || href === '#') {
                    return;
                }

                e.preventDefault();
                var page = new URL(href, window.location.origin).searchParams.get('page') || 1;
                load(page);
            });
        }

        $(document).on('click', config.clearSelector || '.clear-filters', function (e) {
            e.preventDefault();
            e.stopPropagation();
            clearTimeout(searchTimer);
            suppressFilterLoad = true;
            config.resetFields();
            suppressFilterLoad = false;
            load(1);
            return false;
        });

        setTimeout(function () {
            filtersReady = true;
        }, 200);

        return {
            load: load,
            getTable: function () {
                return table;
            }
        };
    };
</script>
