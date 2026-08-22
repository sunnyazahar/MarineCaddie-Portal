/**
 * MarineCaddie shared motion — tabs, list AJAX refresh, filters/search feedback.
 */
function whenJqueryReady(callback) {
    if (typeof window.jQuery === 'function') {
        callback(window.jQuery);
        return;
    }

    var tries = 0;
    var timer = setInterval(function () {
        tries += 1;
        if (typeof window.jQuery === 'function') {
            clearInterval(timer);
            callback(window.jQuery);
            return;
        }
        if (tries >= 120) {
            clearInterval(timer);
        }
    }, 50);
}

function pulseMcClass(el, className) {
    if (!el) {
        return;
    }
    el.classList.remove(className);
    void el.offsetWidth;
    el.classList.add(className);
}

function pulseMcTabPane(el) {
    pulseMcClass(el, 'mc-tab-swap');
}

function pulseMcListRows(tableEl) {
    if (!tableEl) {
        return;
    }

    var rows = tableEl.querySelectorAll('tbody tr');
    rows.forEach(function (row, index) {
        if (row.querySelector('td[colspan]')) {
            return;
        }
        row.classList.remove('mc-list-row-in');
        void row.offsetWidth;
        row.classList.add('mc-list-row-in');
        row.style.animationDelay = Math.min(index * 0.04, 0.28) + 's';
    });
}

whenJqueryReady(function ($) {
    window.mcPulseTabPane = function (selectorOrEl) {
        var el = typeof selectorOrEl === 'string'
            ? document.querySelector(selectorOrEl)
            : selectorOrEl;
        pulseMcTabPane(el);
    };

    window.mcPulseListRows = function (selector) {
        document.querySelectorAll(selector || 'table.dataTable').forEach(function (tableEl) {
            pulseMcListRows(tableEl);
        });
    };

    window.mcPulseListCount = function () {
        document.querySelectorAll('.list-page-header-count strong').forEach(function (el) {
            pulseMcClass(el, 'mc-count-pulse');
        });
    };

    window.mcSetListLoading = function (selector, isLoading) {
        $(selector || 'table.dataTable').each(function () {
            var $wrap = $(this).closest('.list-ajax-table-wrapper, .table-responsive, .card-block, .card');
            if (!$wrap.length) {
                $wrap = $(this).parent();
            }
            $wrap.toggleClass('mc-list-loading', !!isLoading);
        });
    };

    function resolveTabPaneId($trigger) {
        var tabId = $trigger.data('tab');
        if (tabId) {
            return tabId;
        }

        var href = $trigger.attr('href') || $trigger.data('target') || $trigger.data('bs-target');
        if (href && href.charAt(0) === '#') {
            return href.slice(1);
        }

        return null;
    }

    function pulseTabFromTrigger($trigger) {
        var tabId = resolveTabPaneId($trigger);
        if (!tabId) {
            return;
        }
        window.setTimeout(function () {
            pulseMcTabPane(document.getElementById(tabId));
        }, 0);
    }

    $(document).on('click.mcMotion', '.tab-item[data-tab], .nav-tab-item[data-tab], .custom-tab[data-tab], .edit-office-tab[data-tab]', function () {
        pulseTabFromTrigger($(this));
    });

    $(document).on('shown.bs.tab.mcMotion', 'a[data-toggle="tab"], a[data-bs-toggle="tab"]', function (e) {
        pulseTabFromTrigger($(e.target));
    });

    $(document).on('click.mcMotion', '.list-filters-toggle', function () {
        var $card = $(this).closest('.card');
        window.setTimeout(function () {
            var $filters = $card.find('.filter-row, [class*="-filters-area"]').first();
            if ($filters.length) {
                pulseMcClass($filters.get(0), 'mc-filter-reveal');
            }
        }, 40);
    });

    $(document).on('input.mcMotion', '.filter-group .filter-input, .list-inline-toolbar-search input, .list-inline-toolbar input[type="search"], .list-inline-toolbar input[type="text"]', function () {
        var $group = $(this).closest('.filter-group, .list-inline-toolbar-search, .searchable-filter-wrapper');
        if (!$group.length) {
            return;
        }
        pulseMcClass($group.get(0), 'mc-search-active');
    });

    $(document).on('select2:select select2:unselect select2:clear.mcMotion', '.searchable-filter-wrapper select, .filter-row select.select2, [class*="-filters-area"] select.select2', function () {
        var $wrap = $(this).closest('.searchable-filter-wrapper, .filter-item, .filter-group');
        if (!$wrap.length) {
            return;
        }
        pulseMcClass($wrap.get(0), 'mc-filter-active');
    });

    $(document).on('click.mcMotion', '.btn-clear-filters, .clear-filters', function () {
        var $scope = $(this).closest('.card, .page-body');
        window.setTimeout(function () {
            $scope.find('.filter-item, .searchable-filter-wrapper, .filter-group').each(function (index, el) {
                el.classList.remove('mc-filter-active');
                pulseMcClass(el, 'mc-filter-reset');
                el.style.animationDelay = Math.min(index * 0.03, 0.18) + 's';
            });
        }, 30);
    });
});
