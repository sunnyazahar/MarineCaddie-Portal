{{--
  Unsaved changes leave guard (SweetAlert).
  Usage:
    @include('partials.unsaved-changes-guard', [
        'formSelector' => '#officeEditForm',
        'fallbackUrl' => route('offices.index'),
        'saveButtonSelector' => '#btn-save', // optional
    ])
--}}
@php
    $formSelector = $formSelector ?? null;
    $fallbackUrl = $fallbackUrl ?? url('/');
    $saveButtonSelector = $saveButtonSelector ?? null;
    $includeSweetAlert = $includeSweetAlert ?? true;
@endphp

{{-- SweetAlert loaded via layouts/app common assets --}}


@if($formSelector)
<script>
(function($) {
    function initUnsavedChangesGuard() {
        var $form = $('{{ $formSelector }}');
        if (!$form.length) {
            return;
        }

        var unsavedLeaveMessage = 'There are unsaved changes in the form. Are you sure you want to leave without saving?';
        var initialSnapshot = '';
        var fileDirty = false;
        var allowLeave = false;
        var fallbackUrl = @json($fallbackUrl);
        var saveButtonSelector = @json($saveButtonSelector);
        var $saveBtn = saveButtonSelector ? $(saveButtonSelector) : $();

        function formSnapshot() {
            return $form.serialize();
        }

        function isDirty() {
            return fileDirty || formSnapshot() !== initialSnapshot;
        }

        function hasUnsavedChanges() {
            return !allowLeave && isDirty();
        }

        function resetBaseline() {
            initialSnapshot = formSnapshot();
            fileDirty = false;
            syncSaveButtonState();
        }

        function syncSaveButtonState() {
            if (!$saveBtn.length) {
                return;
            }
            var dirty = isDirty();
            if (dirty) {
                $saveBtn.prop('disabled', false);
                if (!$saveBtn.data('saved-label')) {
                    $saveBtn.data('saved-label', $.trim($saveBtn.text()) || 'All changes saved');
                }
                if (!$saveBtn.data('dirty-label')) {
                    $saveBtn.data('dirty-label', 'Save changes');
                }
                $saveBtn.text($saveBtn.data('dirty-label'));
                $saveBtn.css({ background: '#1b5e6f', color: '#fff', cursor: 'pointer' });
            } else {
                $saveBtn.prop('disabled', true);
                $saveBtn.text($saveBtn.data('saved-label') || 'All changes saved');
                $saveBtn.css({ background: '#e9ecef', color: '#a0aec0', cursor: 'default' });
            }
        }

        function markAllowLeave() {
            allowLeave = true;
        }

        function isLeavingPage(href) {
            try {
                var target = new URL(href, window.location.origin);
                if (target.origin !== window.location.origin) {
                    return true;
                }
                return target.pathname !== window.location.pathname
                    || target.search !== window.location.search
                    || target.hash !== window.location.hash;
            } catch (error) {
                return false;
            }
        }

        function shouldInterceptLink($link, href) {
            if (!href || href === '#' || href === '#!' || href.indexOf('javascript:') === 0) {
                return false;
            }
            if ($link.attr('target') === '_blank' || $link.data('skipUnsavedGuard')) {
                return false;
            }
            if ($link.attr('data-toggle') === 'tab'
                || $link.attr('data-toggle') === 'dropdown'
                || $link.hasClass('custom-tab')
                || $link.hasClass('tab-item')
                || $link.data('tab')) {
                return false;
            }
            return isLeavingPage(href);
        }

        function confirmLeaveWithoutSaving(onConfirm) {
            if (typeof swal !== 'function') {
                if (window.confirm(unsavedLeaveMessage)) {
                    onConfirm();
                }
                return;
            }

            swal({
                title: 'Are you sure?',
                text: unsavedLeaveMessage,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#01a9ac',
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm) {
                if (isConfirm) {
                    onConfirm();
                }
            });
        }

        function leaveToPreviousPage() {
            var referrer = document.referrer;
            if (referrer && isLeavingPage(referrer)) {
                window.location.href = referrer;
            } else {
                window.location.href = fallbackUrl;
            }
        }

        function proceedWithNavigation(href) {
            markAllowLeave();
            var $logoutForm = $('#logout-form');
            if ($logoutForm.length && String($logoutForm.attr('action')) === String(href)) {
                $logoutForm.get(0).submit();
                return;
            }
            window.location.href = href;
        }

        function handlePotentialNavigation(linkEl) {
            if (!linkEl || !hasUnsavedChanges()) {
                return false;
            }

            var $link = $(linkEl);
            var href = linkEl.getAttribute('href');
            if (!shouldInterceptLink($link, href)) {
                return false;
            }

            confirmLeaveWithoutSaving(function() {
                proceedWithNavigation(href);
            });

            return true;
        }

        $form.on('submit', function() {
            markAllowLeave();
        });

        $form.on('input change', ':input', function() {
            if ($(this).is(':file')) {
                fileDirty = true;
            }
            syncSaveButtonState();
        });

        $(document).on('select2:select select2:unselect select2:clear', '{{ $formSelector }} .select2, {{ $formSelector }} select', function() {
            setTimeout(syncSaveButtonState, 0);
        });

        document.addEventListener('click', function(e) {
            var linkEl = e.target.closest('a[href]');
            if (!linkEl) {
                return;
            }
            if (!handlePotentialNavigation(linkEl)) {
                return;
            }
            e.preventDefault();
            e.stopImmediatePropagation();
        }, true);

        $(document).on('submit', 'form', function(e) {
            var $submitForm = $(this);
            if ($submitForm.is($form) || !hasUnsavedChanges()) {
                return;
            }

            e.preventDefault();
            var formEl = this;
            confirmLeaveWithoutSaving(function() {
                markAllowLeave();
                HTMLFormElement.prototype.submit.call(formEl);
            });
        });

        $(window).on('keydown', function(e) {
            if (!hasUnsavedChanges()) {
                return;
            }

            var isRefresh = e.key === 'F5'
                || ((e.ctrlKey || e.metaKey) && (e.key === 'r' || e.key === 'R'));

            if (!isRefresh) {
                return;
            }

            e.preventDefault();
            confirmLeaveWithoutSaving(function() {
                markAllowLeave();
                window.location.reload();
            });
        });

        $(window).on('beforeunload', function(e) {
            if (!hasUnsavedChanges()) {
                return;
            }
            e.preventDefault();
            e.returnValue = unsavedLeaveMessage;
            return unsavedLeaveMessage;
        });

        if (window.history && history.pushState) {
            history.pushState({ unsavedChangesGuard: true }, document.title, window.location.href);
            $(window).on('popstate', function() {
                if (!hasUnsavedChanges()) {
                    return;
                }

                history.pushState({ unsavedChangesGuard: true }, document.title, window.location.href);
                confirmLeaveWithoutSaving(function() {
                    markAllowLeave();
                    leaveToPreviousPage();
                });
            });
        }

        // Delay baseline so select2 / late widgets settle
        setTimeout(resetBaseline, 50);
        window.unsavedChangesGuardReset = resetBaseline;
        window.unsavedChangesGuardAllowLeave = markAllowLeave;
    }

    if (window.jQuery) {
        $(function() {
            initUnsavedChangesGuard();
        });
    } else {
        document.addEventListener('DOMContentLoaded', initUnsavedChangesGuard);
    }
})(window.jQuery);
</script>
@endif
