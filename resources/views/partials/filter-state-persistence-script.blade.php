<script>
    (function ($) {
        if (!$ || typeof $.fn === 'undefined') {
            return;
        }

        if (window.mcEnsureFilterPersistence) {
            return;
        }

        function getPersistedFilterUserId() {
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

        function buildPersistedFilterStorageKey(baseKey) {
            var normalizedBaseKey = $.trim(String(baseKey || ''));
            if (!normalizedBaseKey) {
                return '';
            }

            return 'mc.filters.' + getPersistedFilterUserId() + '.' + normalizedBaseKey;
        }

        function canUsePersistedFilterStorage(storageKey) {
            if (!storageKey || !window.localStorage) {
                return false;
            }

            try {
                var probeKey = storageKey + ':probe';
                window.localStorage.setItem(probeKey, '1');
                window.localStorage.removeItem(probeKey);
                return true;
            } catch (error) {
                return false;
            }
        }

        function slugifyPersistedFilterKey(value) {
            return $.trim(String(value || ''))
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '')
                || 'field';
        }

        function resolvePersistedFieldLabel($field) {
            var explicitKey = $field.attr('data-mc-filter-field-key');
            if (explicitKey) {
                return explicitKey;
            }

            if ($field.attr('id')) {
                return $field.attr('id');
            }

            if ($field.attr('name')) {
                return $field.attr('name');
            }

            var $column = $field.closest('[id]');
            if ($column.length && !$column.is('[data-mc-filter-persist-key]')) {
                return $column.attr('id');
            }

            var filterLabel = $.trim($field.closest('.filter-group').find('.filter-label').first().text() || '');
            if (filterLabel !== '') {
                return filterLabel;
            }

            var itemLabel = $.trim($field.closest('.filter-item').find('.filter-item-label').first().text() || '');
            if (itemLabel !== '') {
                return itemLabel;
            }

            var toggleLabel = $.trim($field.closest('.filter-checkbox-group, .checkbox-container, .dangerous-goods-box').find('label, span').first().text() || '');
            if (toggleLabel !== '') {
                return toggleLabel;
            }

            var radioLabel = $.trim($field.closest('label').text() || '');
            if (radioLabel !== '') {
                return radioLabel;
            }

            return 'field';
        }

        function isPersistableFilterField($field) {
            if (!$field || !$field.length) {
                return false;
            }

            if ($field.is('[type="hidden"], [type="submit"], [type="button"], [type="reset"], [type="file"], [type="password"]')) {
                return false;
            }

            if ($field.hasClass('select2-search__field')) {
                return false;
            }

            if (String($field.attr('id') || '') === 'filter-multiselect') {
                return false;
            }

            if ($field.is('[data-storage-key], .mc-column-picker-native')) {
                return false;
            }

            if ($field.closest('.mc-column-picker__panel').length) {
                return false;
            }

            if ($field.closest('table').length && !$field.closest('.filter-group, .filter-item-content, .filter-checkbox-group, .checkbox-container, .dangerous-goods-box, .type-segments').length) {
                return false;
            }

            return $field.hasClass('filter-input')
                || $field.hasClass('searchable-filter-multiselect')
                || $field.hasClass('change-log-select2')
                || $field.hasClass('select2')
                || $field.hasClass('select2-exports')
                || $field.hasClass('select2-exrates')
                || $field.hasClass('date-range-filter')
                || $field.hasClass('custom-checkbox-styled')
                || $field.closest('.filter-group, .filter-item-content, .filter-checkbox-group, .checkbox-container, .dangerous-goods-box, .type-segments').length > 0;
        }

        function normalizePersistedArray(value) {
            if (Array.isArray(value)) {
                return value
                    .map(function (item) {
                        return $.trim(String(item == null ? '' : item));
                    })
                    .filter(function (item) {
                        return item !== '';
                    });
            }

            if (value == null || value === '') {
                return [];
            }

            return [$.trim(String(value))].filter(function (item) {
                return item !== '';
            });
        }

        function resolvePersistedRadioValue($field) {
            var explicitValue = $field.attr('value');
            if (typeof explicitValue === 'string' && explicitValue !== '') {
                return explicitValue;
            }

            var labelText = $.trim($field.closest('label').text() || '');
            if (labelText !== '') {
                return labelText;
            }

            return String($field.index());
        }

        function syncPersistedSelectUi($field) {
            if (!$field.hasClass('select2-hidden-accessible')) {
                return;
            }

            $field.trigger('change.select2');

            if (typeof window.refreshSearchableFilterMultiselectSummary === 'function' && $field.hasClass('searchable-filter-multiselect')) {
                window.refreshSearchableFilterMultiselectSummary($field);
            }
        }

        function syncPersistedTypeSegments($root) {
            $root.find('.type-segments').each(function () {
                var $group = $(this);
                $group.find('.type-segment').removeClass('active');
                $group.find('.type-segment').has('input:checked').addClass('active');
            });
        }

        function hasMeaningfulPersistedValue(value) {
            if (Array.isArray(value)) {
                return value.length > 0;
            }

            if (value && typeof value === 'object') {
                return Object.keys(value).some(function (key) {
                    return hasMeaningfulPersistedValue(value[key]);
                });
            }

            if (typeof value === 'boolean') {
                return value;
            }

            return $.trim(String(value == null ? '' : value)) !== '';
        }

        function resolvePersistedFieldEntries($root) {
            var entries = [];
            var radioGroups = {};
            var keyCounts = {};

            $root.find('input, select, textarea').each(function (index) {
                var $field = $(this);
                if (!isPersistableFilterField($field)) {
                    return;
                }

                if ($field.is(':radio')) {
                    var radioBaseKey = 'radio-' + slugifyPersistedFilterKey($field.attr('name') || resolvePersistedFieldLabel($field) || ('field-' + index));
                    if (radioGroups[radioBaseKey]) {
                        return;
                    }

                    radioGroups[radioBaseKey] = true;

                    var radioName = $field.attr('name');
                    var $radioGroup = radioName
                        ? $root.find('input[type="radio"]').filter(function () {
                            return $(this).attr('name') === radioName;
                        })
                        : $field.closest('.type-segments').find('input[type="radio"]');

                    entries.push({
                        key: radioBaseKey,
                        type: 'radio',
                        $elements: $radioGroup
                    });

                    return;
                }

                var baseKey = slugifyPersistedFilterKey(resolvePersistedFieldLabel($field) || ('field-' + index));
                keyCounts[baseKey] = (keyCounts[baseKey] || 0) + 1;

                entries.push({
                    key: keyCounts[baseKey] > 1 ? (baseKey + '-' + keyCounts[baseKey]) : baseKey,
                    type: $field.is(':checkbox')
                        ? 'checkbox'
                        : ($field.is('select') && $field.prop('multiple') ? 'multi' : 'field'),
                    $element: $field
                });
            });

            return entries;
        }

        function readPersistedEntryValue(entry) {
            if (entry.type === 'radio') {
                var $checked = entry.$elements.filter(':checked').first();
                return $checked.length ? resolvePersistedRadioValue($checked) : '';
            }

            if (entry.type === 'checkbox') {
                return entry.$element.is(':checked');
            }

            if (entry.type === 'multi') {
                return normalizePersistedArray(entry.$element.val());
            }

            return $.trim(String(entry.$element.val() == null ? '' : entry.$element.val()));
        }

        function applyPersistedEntryValue(entry, value) {
            if (entry.type === 'radio') {
                var expectedValue = $.trim(String(value == null ? '' : value));
                entry.$elements.prop('checked', false);

                if (expectedValue === '') {
                    return;
                }

                entry.$elements.each(function () {
                    var $option = $(this);
                    if (resolvePersistedRadioValue($option) === expectedValue) {
                        $option.prop('checked', true);
                        return false;
                    }
                });

                return;
            }

            if (entry.type === 'checkbox') {
                entry.$element.prop('checked', !!value);
                return;
            }

            if (entry.type === 'multi') {
                entry.$element.val(normalizePersistedArray(value));
                syncPersistedSelectUi(entry.$element);
                return;
            }

            var normalizedValue = $.trim(String(value == null ? '' : value));
            entry.$element.val(normalizedValue);

            if (entry.$element.is('select')) {
                syncPersistedSelectUi(entry.$element);
            }

            if (entry.$element.hasClass('hasDatepicker') && normalizedValue === '') {
                entry.$element.datepicker('setDate', null);
            }
        }

        function createFilterPersistenceStore(options) {
            var $root = $(options.root || options.rootSelector || options.scopeSelector).first();
            if (!$root.length) {
                return null;
            }

            var baseStorageKey = options.storageKey || $root.attr('data-mc-filter-persist-key') || '';
            var storageKey = buildPersistedFilterStorageKey(baseStorageKey);
            var clearSelector = options.clearSelector || $root.attr('data-mc-filter-clear-selector') || '.btn-clear-filters, .clear-filters, .clear-filters-link';
            var namespace = '.mcFilterPersist' + slugifyPersistedFilterKey(baseStorageKey || 'root');
            var restoreAttempted = false;
            var restored = false;
            var bound = false;

            function collectState() {
                var state = { fields: {} };

                resolvePersistedFieldEntries($root).forEach(function (entry) {
                    state.fields[entry.key] = readPersistedEntryValue(entry);
                });

                if (typeof options.collectExtraState === 'function') {
                    state.extra = options.collectExtraState($root) || {};
                }

                return state;
            }

            function readState() {
                if (!canUsePersistedFilterStorage(storageKey)) {
                    return null;
                }

                var raw = window.localStorage.getItem(storageKey);
                if (!raw) {
                    return null;
                }

                try {
                    var parsed = JSON.parse(raw);
                    if (!parsed || typeof parsed !== 'object') {
                        return null;
                    }

                    if (Object.prototype.hasOwnProperty.call(parsed, 'fields')) {
                        return parsed;
                    }

                    return {
                        fields: parsed,
                        extra: {}
                    };
                } catch (error) {
                    window.localStorage.removeItem(storageKey);
                    return null;
                }
            }

            function bind() {
                if (bound) {
                    return;
                }

                $root.off(namespace);
                $root.on('input' + namespace + ' change' + namespace, 'input, select, textarea', function () {
                    if ($root.data('mcFilterPersistenceRestoring')) {
                        return;
                    }

                    if (!isPersistableFilterField($(this))) {
                        return;
                    }

                    syncPersistedTypeSegments($root);
                    store.save();
                });

                $root.on('click' + namespace, clearSelector, function () {
                    store.clear();
                });

                bound = true;
            }

            var store = {
                restore: function () {
                    bind();

                    if (restoreAttempted) {
                        return restored;
                    }

                    restoreAttempted = true;

                    var savedState = readState();
                    if (!savedState || (!hasMeaningfulPersistedValue(savedState.fields || {}) && !hasMeaningfulPersistedValue(savedState.extra || {}))) {
                        restored = false;
                        syncPersistedTypeSegments($root);
                        return restored;
                    }

                    $root.data('mcFilterPersistenceRestoring', true);

                    try {
                        var fields = savedState.fields || {};
                        resolvePersistedFieldEntries($root).forEach(function (entry) {
                            if (!Object.prototype.hasOwnProperty.call(fields, entry.key)) {
                                return;
                            }

                            applyPersistedEntryValue(entry, fields[entry.key]);
                        });

                        if (typeof options.applyExtraState === 'function') {
                            options.applyExtraState(savedState.extra || {}, $root);
                        }
                    } finally {
                        $root.removeData('mcFilterPersistenceRestoring');
                    }

                    syncPersistedTypeSegments($root);
                    restored = hasMeaningfulPersistedValue(savedState.fields || {}) || hasMeaningfulPersistedValue(savedState.extra || {});

                    return restored;
                },
                save: function () {
                    bind();

                    if (!canUsePersistedFilterStorage(storageKey)) {
                        return false;
                    }

                    var currentState = collectState();
                    if (!hasMeaningfulPersistedValue(currentState.fields || {}) && !hasMeaningfulPersistedValue(currentState.extra || {})) {
                        window.localStorage.removeItem(storageKey);
                        return false;
                    }

                    window.localStorage.setItem(storageKey, JSON.stringify(currentState));
                    return true;
                },
                clear: function () {
                    if (!canUsePersistedFilterStorage(storageKey)) {
                        return false;
                    }

                    window.localStorage.removeItem(storageKey);
                    restored = false;
                    return true;
                },
                wasRestored: function () {
                    return restored;
                },
                getRoot: function () {
                    return $root;
                },
                getStorageKey: function () {
                    return storageKey;
                },
                getState: function () {
                    return collectState();
                }
            };

            bind();

            return store;
        }

        window.mcEnsureFilterPersistence = function (options) {
            options = options || {};

            var $root = $(options.root || options.rootSelector || options.scopeSelector).first();
            if (!$root.length) {
                return null;
            }

            var existingStore = $root.data('mcFilterPersistenceStore');
            if (existingStore) {
                return existingStore;
            }

            var store = createFilterPersistenceStore(options);
            if (!store) {
                return null;
            }

            $root.data('mcFilterPersistenceStore', store);
            return store;
        };

        window.mcAutoInitFilterPersistence = function () {
            $('[data-mc-filter-persist-key]').each(function () {
                var store = window.mcEnsureFilterPersistence({
                    root: this
                });

                if (!store) {
                    return;
                }

                store.restore();
            });
        };

        if (!window.mcFilterPersistenceBooted) {
            window.mcFilterPersistenceBooted = true;

            $(function () {
                window.mcAutoInitFilterPersistence();
            });
        }
    })(window.jQuery);
</script>
