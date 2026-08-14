@php
    $administrationReadOnly = auth()->user()?->isOperations()
        && request()->routeIs('offices.*', 'hub.*', 'agents.*', 'customers.*', 'contacts.*');
@endphp
@if($administrationReadOnly)
<style>
    html.ops-admin-readonly a[href*="/create"],
    html.ops-admin-readonly .btn-add-office,
    html.ops-admin-readonly .hub-add-desktop,
    html.ops-admin-readonly .agents-add-desktop,
    html.ops-admin-readonly .agents-add-mobile,
    html.ops-admin-readonly .customers-add-mobile,
    html.ops-admin-readonly .btn-pane-action,
    html.ops-admin-readonly .btn-vessel-add,
    html.ops-admin-readonly .btn-add-row,
    html.ops-admin-readonly .btn-add-account,
    html.ops-admin-readonly .remove-account-btn,
    html.ops-admin-readonly .delete-hub,
    html.ops-admin-readonly .delete-agent,
    html.ops-admin-readonly .delete-contact,
    html.ops-admin-readonly .remove-file,
    html.ops-admin-readonly .drag-drop-area,
    html.ops-admin-readonly .upload-area,
    html.ops-admin-readonly button[type="submit"],
    html.ops-admin-readonly .btn-save-custom,
    html.ops-admin-readonly .btn-saved-custom,
    html.ops-admin-readonly .btn-update-customer,
    html.ops-admin-readonly .btn-save-customer,
    html.ops-admin-readonly .btn-save-contact,
    html.ops-admin-readonly .ti-trash {
        display: none !important;
    }
    html.ops-admin-readonly .hub-status-toggle,
    html.ops-admin-readonly .agent-status-toggle {
        pointer-events: none;
        cursor: default;
    }
    html.ops-admin-readonly form input:not([type="hidden"]):not([type="search"]),
    html.ops-admin-readonly form select,
    html.ops-admin-readonly form textarea {
        pointer-events: none;
        background-color: #f8fafc;
    }
    html.ops-admin-readonly .header-search-bar input,
    html.ops-admin-readonly .hub-filters-bar input,
    html.ops-admin-readonly .hub-filters-bar select,
    html.ops-admin-readonly .filter-row input,
    html.ops-admin-readonly .filter-row select,
    html.ops-admin-readonly .customers-filters-panel input,
    html.ops-admin-readonly .customers-filters-panel select,
    html.ops-admin-readonly .vessels-search-container input {
        pointer-events: auto;
        background-color: #fff;
    }
</style>
<script>
    document.documentElement.classList.add('ops-admin-readonly');
        document.addEventListener('DOMContentLoaded', function () {
        document.body.classList.add('ops-admin-readonly');
        document.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                event.stopPropagation();
            }, true);
        });
        document.addEventListener('click', function (event) {
            if (event.target.closest('.delete-hub, .delete-agent, .delete-contact, .remove-file, .hub-status-toggle, .agent-status-toggle, .btn-add-account, .btn-add-row, .remove-account-btn')) {
                event.preventDefault();
                event.stopImmediatePropagation();
            }
        }, true);
        setTimeout(function () {
            if (!window.jQuery) {
                return;
            }
            window.jQuery('form select').prop('disabled', true);
        }, 800);
    });
</script>
@endif
