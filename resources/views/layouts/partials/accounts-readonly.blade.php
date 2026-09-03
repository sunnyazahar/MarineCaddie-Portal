@php
    $accountsReadOnly = auth()->user()?->isAccounts()
        && ! request()->routeIs('billing.*');
@endphp
@if($accountsReadOnly)
<style>
    html.accounts-readonly a[href*="/create"],
    html.accounts-readonly a[href*="create-crr"],
    html.accounts-readonly a[href*="create-shipment"],
    html.accounts-readonly a[href*="create-pre-alert"],
    html.accounts-readonly .btn-add-office,
    html.accounts-readonly .hub-add-desktop,
    html.accounts-readonly .agents-add-desktop,
    html.accounts-readonly .agents-add-mobile,
    html.accounts-readonly .customers-add-mobile,
    html.accounts-readonly .btn-pane-action,
    html.accounts-readonly .btn-vessel-add,
    html.accounts-readonly .btn-add-row,
    html.accounts-readonly .btn-add-account,
    html.accounts-readonly .remove-account-btn,
    html.accounts-readonly .delete-hub,
    html.accounts-readonly .delete-agent,
    html.accounts-readonly .delete-contact,
    html.accounts-readonly .remove-file,
    html.accounts-readonly .drag-drop-area,
    html.accounts-readonly .upload-area,
    html.accounts-readonly button[type="submit"],
    html.accounts-readonly .btn-save-custom,
    html.accounts-readonly .btn-saved-custom,
    html.accounts-readonly .btn-update-customer,
    html.accounts-readonly .btn-save-customer,
    html.accounts-readonly .btn-save-contact,
    html.accounts-readonly .ti-trash,
    html.accounts-readonly [data-action="delete"],
    html.accounts-readonly [data-action="create"],
    html.accounts-readonly .create-btn,
    html.accounts-readonly .btn-create,
    html.accounts-readonly .add-new {
        display: none !important;
    }
    html.accounts-readonly .hub-status-toggle,
    html.accounts-readonly .agent-status-toggle {
        pointer-events: none;
        cursor: default;
    }
    html.accounts-readonly form input:not([type="hidden"]):not([type="search"]),
    html.accounts-readonly form select,
    html.accounts-readonly form textarea {
        pointer-events: none;
        background-color: #f8fafc;
    }
    html.accounts-readonly .header-search-bar input,
    html.accounts-readonly .hub-filters-bar input,
    html.accounts-readonly .hub-filters-bar select,
    html.accounts-readonly .filter-row input,
    html.accounts-readonly .filter-row select,
    html.accounts-readonly .customers-filters-panel input,
    html.accounts-readonly .customers-filters-panel select,
    html.accounts-readonly .vessels-search-container input,
    html.accounts-readonly .searchable-filter-wrapper,
    html.accounts-readonly .select2-container,
    html.accounts-readonly .dash-period__btn {
        pointer-events: auto;
        background-color: #fff;
    }
</style>
<script>
    document.documentElement.classList.add('accounts-readonly');
    document.addEventListener('DOMContentLoaded', function () {
        document.body.classList.add('accounts-readonly');
        document.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                event.stopPropagation();
            }, true);
        });
        document.addEventListener('click', function (event) {
            if (event.target.closest('.delete-hub, .delete-agent, .delete-contact, .remove-file, .hub-status-toggle, .agent-status-toggle, .btn-add-account, .btn-add-row, .remove-account-btn, [data-action="delete"], [data-action="create"]')) {
                event.preventDefault();
                event.stopImmediatePropagation();
            }
        }, true);
        setTimeout(function () {
            if (!window.jQuery) {
                return;
            }
            window.jQuery('form select').not('.filter-row select, .hub-filters-bar select, .customers-filters-panel select').prop('disabled', true);
        }, 800);
    });
</script>
@endif
