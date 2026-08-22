/**
 * Global form save button state — disabled until the form is dirty.
 * Pairs: form[id] + button.btn-save-custom[type=submit] (inside form or form="id").
 */
const registry = new Map();

const DEFAULT_SNAPSHOT_EXCLUDE = ['active_tab'];

function whenJqueryReady(callback) {
    if (typeof window.jQuery === 'function') {
        callback(window.jQuery);
        return;
    }

    let tries = 0;
    const timer = setInterval(() => {
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

function resolveSaveButton($, $form, saveButtonSelector) {
    if (saveButtonSelector) {
        const $explicit = $(saveButtonSelector).first();
        if ($explicit.length) {
            return $explicit;
        }
    }

    const formId = $form.attr('id');
    let $btn = $();

    if (formId) {
        $btn = $(`button[type="submit"].btn-save-custom[form="${formId}"]`).first();
    }

    if (!$btn.length) {
        $btn = $form.find('button[type="submit"].btn-save-custom').first();
    }

    if (!$btn.length) {
        $btn = $form.closest('.page-body, .edit-hub-page, .edit-agent-page, .edit-office-page, .create-hub-page, .create-agent-page, .create-office-page, .hub-user-page, .agent-user-page, .office-user-page, .hub-contact-page, .agent-contact-page, body')
            .find('button[type="submit"].btn-save-custom')
            .filter(function () {
                const linkedForm = this.getAttribute('form');
                return !linkedForm || linkedForm === formId;
            })
            .first();
    }

    return $btn;
}

function bindFormSaveState($, options) {
    const formSelector = options.formSelector;
    const $form = $(formSelector);

    if (!$form.length) {
        return null;
    }

    if (registry.has(formSelector)) {
        return registry.get(formSelector);
    }

    const snapshotExcludeNames = options.snapshotExcludeNames || DEFAULT_SNAPSHOT_EXCLUDE;
    const $saveBtn = resolveSaveButton($, $form, options.saveButtonSelector || null);

    if (!$saveBtn.length) {
        return null;
    }

    let initialSnapshot = '';
    let fileDirty = false;
    let allowLeave = false;

    if (!$saveBtn.data('mc-save-label')) {
        $saveBtn.data('mc-save-label', $.trim($saveBtn.text()) || 'Save');
    }

    if (options.legacyLabelSwap) {
        if (!$saveBtn.data('mc-save-idle-label')) {
            $saveBtn.data('mc-save-idle-label', $.trim($saveBtn.text()) || 'All changes saved');
        }
        if (!$saveBtn.data('mc-save-dirty-label')) {
            $saveBtn.data('mc-save-dirty-label', 'Save changes');
        }
    }

    $saveBtn.addClass('mc-save-btn');

    function formSnapshot() {
        if (!snapshotExcludeNames.length) {
            return $form.serialize();
        }

        return $form.find(':input').filter(function () {
            const name = this.name;
            return name && snapshotExcludeNames.indexOf(name) === -1;
        }).serialize();
    }

    function isDirty() {
        return !allowLeave && (fileDirty || formSnapshot() !== initialSnapshot);
    }

    function syncSaveButtonState() {
        const dirty = fileDirty || formSnapshot() !== initialSnapshot;

        $saveBtn.prop('disabled', !dirty);
        $saveBtn.toggleClass('mc-save-btn--dirty', dirty);
        $saveBtn.toggleClass('mc-save-btn--idle', !dirty);

        if (options.legacyLabelSwap && !dirty) {
            $saveBtn.text($saveBtn.data('mc-save-idle-label') || 'All changes saved');
        } else if (options.legacyLabelSwap && dirty) {
            $saveBtn.text($saveBtn.data('mc-save-dirty-label') || 'Save changes');
        } else {
            $saveBtn.text($saveBtn.data('mc-save-label'));
        }
    }

    function resetBaseline() {
        initialSnapshot = formSnapshot();
        fileDirty = false;
        allowLeave = false;
        syncSaveButtonState();
    }

    function markAllowLeave() {
        allowLeave = true;
        syncSaveButtonState();
    }

    $form.on('submit.mcSaveState', () => {
        markAllowLeave();
    });

    $form.on('input.mcSaveState change.mcSaveState', ':input', function () {
        if ($(this).is(':file')) {
            fileDirty = true;
        }
        syncSaveButtonState();
    });

    $(document).on(
        'select2:select.mcSaveState select2:unselect.mcSaveState select2:clear.mcSaveState',
        `${formSelector} select, ${formSelector} .select2`,
        () => {
            window.setTimeout(syncSaveButtonState, 0);
        }
    );

    $saveBtn.prop('disabled', true).addClass('mc-save-btn--idle');
    syncSaveButtonState();

    window.setTimeout(resetBaseline, 300);
    $(window).on('load.mcSaveState', resetBaseline);

    const api = {
        formSelector,
        isDirty,
        resetBaseline,
        markAllowLeave,
        syncSaveButtonState,
        getSaveButton() {
            return $saveBtn;
        },
    };

    registry.set(formSelector, api);
    $form.data('mcSaveStateBound', true);

    return api;
}

function autoBindFormSaveStates($) {
    $('form[id]').each(function () {
        const formId = this.id;
        const $form = $(this);

        if ($form.data('mcSaveStateBound') || $form.is('[data-mc-skip-save-state]')) {
            return;
        }

        if ($form.find('[data-mc-manual-save-state]').length) {
            return;
        }

        if (document.querySelector(`button[type="submit"][data-mc-manual-save-state][form="${formId}"]`)) {
            return;
        }

        if (!resolveSaveButton($, $form, null).length) {
            return;
        }

        bindFormSaveState($, {
            formSelector: `#${formId}`,
        });
    });
}

whenJqueryReady(($) => {
    window.McFormSaveState = {
        bind(options) {
            return bindFormSaveState($, options || {});
        },
        get(formSelector) {
            return registry.get(formSelector) || null;
        },
        autoBind() {
            autoBindFormSaveStates($);
        },
    };

    $(function () {
        window.McFormSaveState.autoBind();
    });

    $(window).on('load.mcSaveStateAutoBind', () => {
        window.McFormSaveState.autoBind();
    });
});
