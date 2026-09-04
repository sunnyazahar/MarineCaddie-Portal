{{--
  Unsaved changes leave guard (SweetAlert) + save button state (via form-save-state.js).
  Usage:
    @include('partials.unsaved-changes-guard', [
        'formSelector' => '#officeEditForm',
        'fallbackUrl' => route('offices.index'),
        'saveButtonSelector' => '#btn-save', // optional — auto-resolved when omitted
        'legacySaveLabelSwap' => true,     // optional — idle/dirty label text (legacy pages)
    ])
--}}
@php
    $formSelector = $formSelector ?? null;
    $fallbackUrl = $fallbackUrl ?? url('/');
    $saveButtonSelector = $saveButtonSelector ?? null;
    $includeSweetAlert = $includeSweetAlert ?? true;
    $legacySaveLabelSwap = $legacySaveLabelSwap ?? false;
    $snapshotExcludeNames = $snapshotExcludeNames ?? ['active_tab'];
@endphp

{{-- SweetAlert loaded via layouts/app common assets --}}


@if($formSelector)
<script>
(function($) {
    function initUnsavedChangesGuard() {
        var formSelector = @json($formSelector);
        var $form = $(formSelector);
        if (!$form.length) {
            return;
        }

        var unsavedLeaveMessage = 'There are unsaved changes in the form. Are you sure you want to leave without saving?';
        var fallbackUrl = @json($fallbackUrl);
        var saveButtonSelector = @json($saveButtonSelector);
        var snapshotExcludeNames = @json($snapshotExcludeNames);
        var legacySaveLabelSwap = @json($legacySaveLabelSwap);

        var saveState = null;
        var initialSnapshot = '';
        var fileDirty = false;
        var allowLeave = false;

        if (window.McFormSaveState) {
            saveState = window.McFormSaveState.get(formSelector);
            if (!saveState) {
                saveState = window.McFormSaveState.bind({
                    formSelector: formSelector,
                    saveButtonSelector: saveButtonSelector,
                    snapshotExcludeNames: snapshotExcludeNames,
                    legacyLabelSwap: legacySaveLabelSwap
                });
            }
        }

        function formSnapshot() {
            if (!snapshotExcludeNames.length) {
                return $form.serialize();
            }

            return $form.find(':input').filter(function() {
                var name = this.name;
                return name && snapshotExcludeNames.indexOf(name) === -1;
            }).serialize();
        }

        function isDirtyFallback() {
            return fileDirty || formSnapshot() !== initialSnapshot;
        }

        function hasUnsavedChanges() {
            if (saveState) {
                return saveState.isDirty();
            }
            return !allowLeave && isDirtyFallback();
        }

        function markAllowLeave() {
            if (saveState) {
                saveState.markAllowLeave();
                return;
            }
            allowLeave = true;
        }

        function clearAllowLeave() {
            if (saveState && typeof saveState.clearAllowLeave === 'function') {
                saveState.clearAllowLeave();
                return;
            }
            allowLeave = false;
        }

        function armSaveSubmit() {
            markAllowLeave();
        }

        function rearmGuardIfSaveBlocked() {
            window.setTimeout(function() {
                if (document.hidden) {
                    return;
                }

                var validator = $form.data('validator');
                if (validator && typeof validator.checkForm === 'function' && !validator.checkForm()) {
                    clearAllowLeave();
                    return;
                }

                if ($form[0] && typeof $form[0].checkValidity === 'function' && !$form[0].checkValidity()) {
                    clearAllowLeave();
                }
            }, 0);
        }

        function resetBaselineFallback() {
            initialSnapshot = formSnapshot();
            fileDirty = false;
            allowLeave = false;
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
                || $link.hasClass('edit-office-tab')
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

        function buttonSubmitsGuardedForm(btn) {
            if (!btn) {
                return false;
            }
            var formId = $form.attr('id');
            if (formId && btn.getAttribute('form') === formId) {
                return true;
            }
            return $form[0] && $.contains($form[0], btn);
        }

        $form.on('submit', function() {
            armSaveSubmit();
        });

        // Arm before unload: external form= submit buttons can race beforeunload
        // ahead of the form submit handler in some browsers.
        document.addEventListener('click', function(e) {
            var btn = e.target && e.target.closest
                ? e.target.closest('button[type="submit"], input[type="submit"]')
                : null;
            if (!buttonSubmitsGuardedForm(btn)) {
                return;
            }
            armSaveSubmit();
            rearmGuardIfSaveBlocked();
        }, true);

        if (saveButtonSelector) {
            $(saveButtonSelector).on('mousedown.unsavedGuard', function() {
                armSaveSubmit();
            });
        }

        if (!saveState) {
            $form.on('input change', ':input', function() {
                if ($(this).is(':file')) {
                    fileDirty = true;
                }
                allowLeave = false;
            });

            setTimeout(resetBaselineFallback, 300);
            $(window).on('load', resetBaselineFallback);
        }

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

        window.unsavedChangesGuardReset = function() {
            if (saveState) {
                saveState.resetBaseline();
                return;
            }
            resetBaselineFallback();
        };
        window.unsavedChangesGuardAllowLeave = markAllowLeave;
        window.unsavedChangesGuardClearAllowLeave = clearAllowLeave;
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
