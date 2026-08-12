<style>
    #marine-mail-busy-overlay {
        position: fixed;
        inset: 0;
        z-index: 20050;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(2px);
    }

    #marine-mail-busy-overlay.is-visible {
        display: flex;
    }

  #marine-mail-busy-overlay .marine-mail-busy-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 14px;
        min-width: 220px;
        max-width: 360px;
        padding: 28px 32px;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 18px 48px rgba(15, 23, 42, 0.22);
        text-align: center;
    }

    #marine-mail-busy-overlay .marine-mail-busy-spinner {
        width: 42px;
        height: 42px;
        border: 3px solid #e2e8f0;
        border-top-color: #14b8a6;
        border-radius: 50%;
        animation: marine-mail-busy-spin 0.75s linear infinite;
    }

    #marine-mail-busy-overlay .marine-mail-busy-text {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
        line-height: 1.45;
    }

    #marine-mail-busy-overlay .marine-mail-busy-hint {
        margin: 0;
        font-size: 12px;
        color: #64748b;
        line-height: 1.4;
    }

    @keyframes marine-mail-busy-spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>

<div id="marine-mail-busy-overlay" aria-live="polite" aria-busy="false" aria-hidden="true">
    <div class="marine-mail-busy-card">
        <div class="marine-mail-busy-spinner" role="status" aria-label="Loading"></div>
        <p class="marine-mail-busy-text">Processing email...</p>
        <p class="marine-mail-busy-hint">This may take a moment while we prepare or send your message.</p>
    </div>
</div>

<script>
(function () {
    var overlay = document.getElementById('marine-mail-busy-overlay');
    var messageEl = overlay ? overlay.querySelector('.marine-mail-busy-text') : null;
    var activeCount = 0;

    var MAIL_URL_PATTERN = /\/(manifest-mail|pre-alert-mail|pre-alert-reminder-mail|delivery-status-reminder-mail|invoice-request-mail)(\/|$)/i;

    function resolveMessage(settings) {
        if (settings && settings.marineMailBusyMessage) {
            return settings.marineMailBusyMessage;
        }

        var url = String(settings && settings.url || '');
        if (/\/send(\/|$)/i.test(url) || /\/dispatch(\/|$)/i.test(url)) {
            return 'Sending email...';
        }
        if (/\/prepare(\/|$)/i.test(url)) {
            return 'Preparing email...';
        }
        if (/\/preview(\/|$)/i.test(url)) {
            return 'Preparing email preview...';
        }

        return 'Processing email...';
    }

    function shouldTrackMailBusy(settings) {
        if (!settings) {
            return false;
        }
        if (settings.marineMailBusy === false) {
            return false;
        }
        if (settings.marineMailBusy) {
            return true;
        }

        return MAIL_URL_PATTERN.test(String(settings.url || ''));
    }

    function show(message) {
        if (!overlay) {
            return;
        }

        activeCount += 1;
        if (messageEl) {
            messageEl.textContent = message || 'Processing email...';
        }
        overlay.classList.add('is-visible');
        overlay.setAttribute('aria-busy', 'true');
        overlay.setAttribute('aria-hidden', 'false');
    }

    function hide() {
        if (!overlay) {
            return;
        }

        activeCount = Math.max(0, activeCount - 1);
        if (activeCount === 0) {
            overlay.classList.remove('is-visible');
            overlay.setAttribute('aria-busy', 'false');
            overlay.setAttribute('aria-hidden', 'true');
        }
    }

    window.MarineMailBusy = {
        show: show,
        hide: hide,
        shouldTrack: shouldTrackMailBusy,
        resolveMessage: resolveMessage
    };

    function registerJqueryHooks() {
        if (typeof jQuery === 'undefined' || window.__marineMailAjaxHooks) {
            return;
        }

        window.__marineMailAjaxHooks = true;
        var $ = jQuery;

        $(document).ajaxSend(function (_event, jqXHR, settings) {
            if (!shouldTrackMailBusy(settings)) {
                return;
            }

            jqXHR._marineMailBusy = true;
            show(resolveMessage(settings));
        });

        $(document).ajaxComplete(function (_event, jqXHR) {
            if (jqXHR._marineMailBusy) {
                jqXHR._marineMailBusy = false;
                hide();
            }
        });

        $(document).on('click', '[data-marine-mail-busy]', function () {
            var $btn = $(this);
            if ($btn.prop('disabled') || $btn.hasClass('disabled')) {
                return;
            }

            show($btn.attr('data-marine-mail-busy-message') || 'Opening mail app...');
            window.setTimeout(function () {
                hide();
            }, 8000);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', registerJqueryHooks);
    } else {
        registerJqueryHooks();
    }

    window.addEventListener('load', registerJqueryHooks);
})();
</script>
