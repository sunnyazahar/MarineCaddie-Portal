var pendingReminderMail = null;
var reminderContactsUrl = @json(route('api.mail-contacts'));
var reminderComposeSender = @json([
    'name' => auth()->user()?->name,
    'email' => auth()->user()?->email,
]);
var reminderContactsSearchTimer = null;

@include('partials.compose-font-tools-script')
@include('partials.compose-color-tools-script')

function fillReminderComposeFrom(preview) {
    preview = preview || {};
    var fromName = preview.from_name || reminderComposeSender.name || '';
    var fromEmail = preview.from || reminderComposeSender.email || '';
    var fromDisplay = fromName && fromEmail
        ? fromName + ' <' + fromEmail + '>'
        : (fromEmail || fromName || '');
    $('#reminder-mail-from').val(fromDisplay);
}

function openReminderMailDraft(emlUrl) {
    if (!emlUrl) {
        alert('Mail draft is not available.');
        return;
    }

    if (window.MarineMailBusy) {
        window.MarineMailBusy.show('Opening mail app...');
        window.setTimeout(function () {
            window.MarineMailBusy.hide();
        }, 8000);
    }

    var mailWindow = window.open('', '_blank');
    if (mailWindow) {
        mailWindow.location.href = emlUrl;
    } else {
        window.open(emlUrl, '_blank');
    }
}

function closeReminderContactPickers() {
    $('#compose-reminder-modal .compose-contact-picker').removeClass('open');
    $('#compose-reminder-modal .compose-contact-btn').removeClass('active');
}

function appendReminderEmail(fieldId, email) {
    email = $.trim(email || '');
    if (!email) {
        return;
    }

    var $field = $('#' + fieldId);
    var current = $.trim($field.val() || '');
    var parts = current
        ? current.split(/[;,]+/).map(function(part) { return $.trim(part); }).filter(Boolean)
        : [];

    if (!parts.some(function(part) { return part.toLowerCase() === email.toLowerCase(); })) {
        parts.push(email);
    }
    $field.val(parts.join(', ')).trigger('change');
}

function renderReminderContacts($picker, contacts) {
    var $list = $picker.find('.compose-contact-list').empty();
    if (!contacts.length) {
        $list.append('<li class="compose-contact-empty">No contacts found</li>');
        return;
    }

    contacts.forEach(function(contact) {
        $list.append(
            $('<li class="compose-contact-item"></li>')
                .attr('data-email', contact.email || '')
                .append($('<p class="compose-contact-name"></p>').text(contact.name || contact.email || 'Contact'))
                .append($('<p class="compose-contact-email"></p>').text(contact.email || ''))
        );
    });
}

function loadReminderContacts($picker, query) {
    $picker.find('.compose-contact-list').html('<li class="compose-contact-empty">Loading...</li>');
    $.ajax({
        url: reminderContactsUrl,
        method: 'GET',
        dataType: 'json',
        data: { q: query || '' }
    })
        .done(function(response) {
            renderReminderContacts($picker, (response && response.results) || []);
        })
        .fail(function() {
            $picker.find('.compose-contact-list').html('<li class="compose-contact-empty">Could not load contacts</li>');
        });
}

$(document).on('click', '#compose-reminder-modal .compose-contact-btn', function(e) {
    e.preventDefault();
    e.stopPropagation();

    var $button = $(this);
    var fieldId = $button.data('target-field');
    var $picker = $('#compose-reminder-modal .compose-contact-picker[data-for="' + fieldId + '"]');
    var alreadyOpen = $picker.hasClass('open');

    closeReminderContactPickers();
    if (alreadyOpen) {
        return;
    }

    $button.addClass('active');
    $picker.addClass('open');
    $picker.find('.compose-contact-search').val('').focus();
    loadReminderContacts($picker, '');
});

$(document).on('input', '#compose-reminder-modal .compose-contact-search', function() {
    var $picker = $(this).closest('.compose-contact-picker');
    var query = $.trim($(this).val() || '');
    clearTimeout(reminderContactsSearchTimer);
    reminderContactsSearchTimer = setTimeout(function() {
        loadReminderContacts($picker, query);
    }, 200);
});

$(document).on('click', '#compose-reminder-modal .compose-contact-item', function(e) {
    e.preventDefault();
    e.stopPropagation();
    appendReminderEmail(
        $(this).closest('.compose-contact-picker').attr('data-for'),
        $(this).attr('data-email')
    );
    closeReminderContactPickers();
});

$(document).on('click', '#compose-reminder-modal', function(e) {
    if (!$(e.target).closest('.compose-contact-btn, .compose-contact-picker').length) {
        closeReminderContactPickers();
    }
});

$('#compose-reminder-modal').on('hidden.bs.modal', closeReminderContactPickers);

function plainTextToReminderHtml(text) {
    var parts = String(text || '').split(/(<table[\s\S]*?<\/table>|<font[\s\S]*?<\/font>|<span[\s\S]*?<\/span>|<mark[\s\S]*?<\/mark>)/i);
    return parts.map(function(part) {
        if (/^<(table|font|span|mark)\b/i.test(part)) {
            return part;
        }
        return String(part || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\n/g, '<br>');
    }).join('');
}

function reminderEditorToPlainText(html) {
    var $tmp = $('<div>').html(html || '');
    var blocks = [];
    $tmp.find('table, font[color], span[style], mark').each(function() {
        var style = String(this.getAttribute('style') || '').toLowerCase();
        var keep = this.tagName === 'TABLE'
            || this.tagName === 'FONT'
            || this.tagName === 'MARK'
            || style.indexOf('color') !== -1
            || style.indexOf('background') !== -1;
        if (!keep) {
            return;
        }
        blocks.push(this.outerHTML);
        $(this).replaceWith('%%MAIL_HTML_' + (blocks.length - 1) + '%%');
    });
    $tmp.find('br').replaceWith('\n');
    $tmp.find('div, p, li').each(function() {
        $(this).append('\n');
    });
    var text = $.trim($tmp.text().replace(/\n{3,}/g, '\n\n'));
    blocks.forEach(function(blockHtml, index) {
        text = text.replace('%%MAIL_HTML_' + index + '%%', '\n' + blockHtml + '\n');
    });
    return $.trim(text.replace(/\n{3,}/g, '\n\n'));
}

function formatReminderFileSize(bytes) {
    if (bytes < 1024) {
        return bytes + ' B';
    }
    if (bytes < 1024 * 1024) {
        return (bytes / 1024).toFixed(1) + ' KB';
    }
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

function clearReminderAttachments() {
    if (pendingReminderMail && pendingReminderMail.attachments) {
        pendingReminderMail.attachments.forEach(function(item) {
            if (item.previewUrl) {
                URL.revokeObjectURL(item.previewUrl);
            }
        });
    }
    $('#reminder-attach-previews').empty();
    $('#reminder-attachment-input').val('');
}

function renderReminderAttachments() {
    var $previews = $('#reminder-attach-previews').empty();
    var attachments = pendingReminderMail ? pendingReminderMail.attachments : [];

    (attachments || []).forEach(function(item) {
        var isPdf = item.file.type === 'application/pdf' || /\.pdf$/i.test(item.file.name);
        var isImage = /^image\//.test(item.file.type) || /\.(png|jpe?g|gif|webp)$/i.test(item.file.name);
        var canPreview = !!item.previewUrl;
        var typeLabel = isPdf ? 'PDF' : (isImage ? 'Image' : 'File');
        var $card = $('<div class="compose-attach-card"></div>')
            .attr('data-preview-url', item.previewUrl || '')
            .attr('title', canPreview ? ('Preview ' + item.file.name) : item.file.name)
            .css('cursor', canPreview ? 'pointer' : 'default');
        var $remove = $('<button type="button" class="compose-attach-remove" title="Remove attachment">&times;</button>')
            .attr('data-key', item.key);
        var $thumb = $('<div class="compose-attach-thumb"></div>');

        if (item.previewUrl && isPdf) {
            $thumb.append(
                $('<iframe></iframe>')
                    .attr('src', item.previewUrl)
                    .attr('title', item.file.name)
                    .attr('loading', 'lazy')
            );
        } else if (item.previewUrl && isImage) {
            $thumb.append($('<img alt="">').attr('src', item.previewUrl));
        } else {
            $thumb.append('<i class="ti-file attach-icon"></i>');
        }

        var $meta = $('<div class="compose-attach-meta"></div>')
            .append($('<p class="compose-attach-name"></p>').text(item.file.name))
            .append(
                $('<p class="compose-attach-type"></p>').text(
                    typeLabel + ' · ' + formatReminderFileSize(item.file.size)
                    + (canPreview ? ' · Click to preview' : '')
                )
            );

        $previews.append($card.append($remove, $thumb, $meta));
    });
}

$(document).on('click', '#compose-reminder-modal .compose-tool-btn', function(e) {
    e.preventDefault();
    document.execCommand($(this).data('cmd'), false, $(this).data('value') || null);
    $('#reminder-mail-body').focus();
});

$(document).on('click', '#reminder-attachment-btn', function() {
    $('#reminder-attachment-input').trigger('click');
});

$(document).on('change', '#reminder-attachment-input', function() {
    if (!pendingReminderMail) {
        return;
    }

    Array.prototype.forEach.call(this.files || [], function(file, index) {
        if (file.size > 20 * 1024 * 1024) {
            alert(file.name + ' is larger than the 20MB limit.');
            return;
        }

        var canPreview = file.type === 'application/pdf'
            || /^image\//.test(file.type)
            || /\.(pdf|png|jpe?g|gif|webp)$/i.test(file.name);
        pendingReminderMail.attachments.push({
            key: 'reminder-local-' + Date.now() + '-' + index,
            file: file,
            previewUrl: canPreview ? URL.createObjectURL(file) : null
        });
    });

    renderReminderAttachments();
    this.value = '';
});

$(document).on('click', '#reminder-attach-previews .compose-attach-remove', function() {
    if (!pendingReminderMail) {
        return;
    }
    var key = String($(this).data('key'));
    pendingReminderMail.attachments = pendingReminderMail.attachments.filter(function(item) {
        if (String(item.key) !== key) {
            return true;
        }
        if (item.previewUrl) {
            URL.revokeObjectURL(item.previewUrl);
        }
        return false;
    });
    renderReminderAttachments();
});

$(document).on('click', '#reminder-attach-previews .compose-attach-card', function(e) {
    if ($(e.target).closest('.compose-attach-remove').length) {
        return;
    }
    var previewUrl = $(this).attr('data-preview-url');
    if (previewUrl) {
        window.open(previewUrl, '_blank');
    }
});

$(document).on('click', '.send-reminder-btn', function(e) {
    e.preventDefault();
    var $button = $(this);
    var originalText = $button.text();

    $button.prop('disabled', true).text('Preparing...');
    $.ajax({
        url: $button.data('preview-url'),
        method: 'GET',
        dataType: 'json',
        headers: { Accept: 'application/json' }
    })
        .done(function(response) {
            if (!response || !response.success || !response.preview) {
                alert((response && response.message) || 'Could not prepare reminder email.');
                return;
            }

            pendingReminderMail = {
                shipmentId: $button.data('shipment-id'),
                sendUrl: $button.data('send-url') || null,
                emlUrl: $button.data('eml-url') || null,
                emlFilename: $button.data('eml-filename') || null,
                attachments: []
            };
            $('#reminder-mail-to').val(response.preview.to || '');
            $('#reminder-mail-cc').val(response.preview.cc || '');
            $('#reminder-mail-bcc').val(response.preview.bcc || '');
            $('#reminder-mail-subject').val(response.preview.subject || '');
            $('#reminder-mail-body').html(plainTextToReminderHtml(response.preview.body || ''));
            fillReminderComposeFrom(response.preview);
            renderReminderAttachments();
            $('#compose-reminder-modal').modal('show');
        })
        .fail(function(xhr) {
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'Could not prepare reminder email.');
        })
        .always(function() {
            $button.prop('disabled', false).text(originalText);
        });
});

$(document).on('click', '#reminder-mail-discard', function() {
    clearReminderAttachments();
    pendingReminderMail = null;
    $('#compose-reminder-modal').modal('hide');
});

$(document).on('click', '#reminder-mail-draft', function() {
    if (!pendingReminderMail || !pendingReminderMail.emlUrl) {
        alert('Mail draft is not available.');
        return;
    }
    openReminderMailDraft(pendingReminderMail.emlUrl);
    $('#compose-reminder-modal').modal('hide');
});

$(document).on('click', '#reminder-mail-send', function() {
    if (!pendingReminderMail || !pendingReminderMail.sendUrl) {
        alert('Email is not available.');
        return;
    }

    var $button = $(this);
    var to = $.trim($('#reminder-mail-to').val() || '');
    var subject = $.trim($('#reminder-mail-subject').val() || '');
    if (!to) {
        alert('Please enter at least one recipient in To.');
        return;
    }
    if (!subject) {
        alert('Please enter a subject.');
        return;
    }

    var formData = new FormData();
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    formData.append('to', to);
    formData.append('cc', $.trim($('#reminder-mail-cc').val() || ''));
    formData.append('bcc', $.trim($('#reminder-mail-bcc').val() || ''));
    formData.append('subject', subject);
    formData.append('body', $('#reminder-mail-body').html() || '');
    (pendingReminderMail.attachments || []).forEach(function(item) {
        formData.append('files[]', item.file, item.file.name);
    });

    var $modal = $('#compose-reminder-modal');
    var originalHtml = $button.html();
    $modal.addClass('compose-sending');
    $button.prop('disabled', true).html('<i class="ti-reload"></i> Sending...');
    $('#reminder-mail-discard, #reminder-mail-draft, #reminder-attachment-btn').prop('disabled', true);

    $.ajax({
        url: pendingReminderMail.sendUrl,
        method: 'POST',
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false
    })
        .done(function(response) {
            if (!response || !response.success) {
                alert((response && response.message) || 'Could not send email.');
                return;
            }

            var shipmentId = pendingReminderMail.shipmentId;
            clearReminderAttachments();
            pendingReminderMail = null;
            $modal.modal('hide');
            if (response.reminder_sent_count !== undefined) {
                $('.reminder-sent-count[data-shipment-id="' + shipmentId + '"]')
                    .text(response.reminder_sent_count);
            }
            var $reminderSentDate = $('.reminder-sent-date[data-shipment-id="' + shipmentId + '"]');
            if ($reminderSentDate.length) {
                var today = new Date();
                var formatted = ('0' + today.getDate()).slice(-2) + '.' +
                    ('0' + (today.getMonth() + 1)).slice(-2) + '.' +
                    today.getFullYear();
                $reminderSentDate.text(formatted);
            }

            if (typeof swal === 'function') {
                swal({
                    title: 'Email sent',
                    text: response.message || 'Reminder email sent successfully.',
                    type: 'success',
                    timer: 4000
                });
            } else {
                alert(response.message || 'Reminder email sent successfully.');
            }
        })
        .fail(function(xhr) {
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'Could not send email. Please try again.');
        })
        .always(function() {
            $modal.removeClass('compose-sending');
            $button.prop('disabled', false).html(originalHtml);
            $('#reminder-mail-discard, #reminder-mail-draft, #reminder-attachment-btn').prop('disabled', false);
        });
});
