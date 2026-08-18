<script>
    window.initializeSearchableFilterMultiselect = function(selector, options) {
        var settings = $.extend({
            enableCaseInsensitiveFiltering: true,
            includeResetOption: true,
            resetText: 'Clear',
            filterPlaceholder: 'Type here',
            maxHeight: 420,
            buttonWidth: '100%',
            nonSelectedText: 'Click here',
            numberDisplayed: 1,
            nSelectedText: 'selected',
            buttonText: function(selectedOptions) {
                if (selectedOptions.length === 0) {
                    return 'Click here';
                }

                var firstSelection = $(selectedOptions[0]).text();
                return selectedOptions.length === 1 ? firstSelection : firstSelection + ', ...';
            },
            buttonTitle: function(selectedOptions) {
                var labels = [];

                selectedOptions.each(function() {
                    labels.push($(this).text());
                });

                return labels.join(', ');
            }
        }, options || {});

        $(selector).each(function() {
            $(this).multiselect(settings);
            $(this).closest('.multiselect-native-select').addClass('searchable-filter-wrapper');
        });
    };

    window.clearSearchableFilterMultiselect = function(selector, triggerChange) {
        $(selector).each(function() {
            var $select = $(this);
            $select.find('option').prop('selected', false);
            $select.val([]);
            $select.multiselect('clearSelection');
            $select.closest('.multiselect-native-select').find('.multiselect-search').val('');
            $select.closest('.multiselect-native-select').find('li.multiselect-filter-hidden')
                .removeClass('multiselect-filter-hidden')
                .show();

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

        $(document).on('click', (config.resetClickScope || '') + ' .multiselect-reset a', function () {
            if (!shouldLoad()) {
                return;
            }

            setTimeout(function () {
                load(1);
            }, 0);
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
