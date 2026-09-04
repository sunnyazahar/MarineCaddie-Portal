<div class="modal fade" id="compose-reminder-modal" tabindex="-1" role="dialog"
    aria-labelledby="composeReminderLabel" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="compose-send-loader" aria-live="polite" aria-busy="true">
                <div class="compose-send-spinner" role="status"></div>
                <p class="compose-send-loader-text">Sending email...</p>
            </div>
            <div class="compose-header">
                <h5 class="compose-title" id="composeReminderLabel">Compose New Message</h5>
            </div>
            <div class="compose-body">
                <div class="compose-field">
                    <input type="text" id="reminder-mail-from" class="compose-input compose-input-readonly" readonly placeholder="From:">
                </div>
                <p class="compose-from-hint">Reminder emails are sent from your login email address.</p>
                <div class="compose-field">
                    <input type="text" id="reminder-mail-to" class="compose-input" placeholder="To:">
                </div>
                <div class="compose-field compose-field-contact">
                    <div class="compose-field-with-icon">
                        <input type="text" id="reminder-mail-cc" class="compose-input" placeholder="Cc:">
                        <button type="button" class="compose-contact-btn" data-target-field="reminder-mail-cc" title="Contact directory">
                            <i class="icofont icofont-contacts"></i>
                        </button>
                    </div>
                    <div class="compose-contact-picker" data-for="reminder-mail-cc">
                        <input type="text" class="compose-contact-search" placeholder="Search contacts...">
                        <ul class="compose-contact-list"></ul>
                    </div>
                </div>
                <div class="compose-field compose-field-contact">
                    <div class="compose-field-with-icon">
                        <input type="text" id="reminder-mail-bcc" class="compose-input" placeholder="Bcc:">
                        <button type="button" class="compose-contact-btn" data-target-field="reminder-mail-bcc" title="Contact directory">
                            <i class="icofont icofont-contacts"></i>
                        </button>
                    </div>
                    <div class="compose-contact-picker" data-for="reminder-mail-bcc">
                        <input type="text" class="compose-contact-search" placeholder="Search contacts...">
                        <ul class="compose-contact-list"></ul>
                    </div>
                </div>
                <div class="compose-field">
                    <input type="text" id="reminder-mail-subject" class="compose-input" placeholder="Subject:">
                </div>
                <div class="compose-editor-wrap">
                    <div class="compose-toolbar">
                        <select id="reminder-font-size" title="Text style">
                            <option value="3">A Normal text</option>
                            <option value="2">Small</option>
                            <option value="4">Large</option>
                            <option value="5">Heading</option>
                        </select>
                        <button type="button" class="compose-tool-btn" data-cmd="bold" title="Bold"><strong>B</strong></button>
                        <button type="button" class="compose-tool-btn" data-cmd="italic" title="Italic"><em>I</em></button>
                        <button type="button" class="compose-tool-btn" data-cmd="underline" title="Underline"><u>U</u></button>
                        @include('partials.compose-color-tools')
                        <button type="button" class="compose-tool-btn" data-cmd="fontSize" data-value="2" title="Small">Small</button>
                        <button type="button" class="compose-tool-btn" data-cmd="formatBlock" data-value="blockquote" title="Quote"><i class="ti-quote-left"></i></button>
                        <button type="button" class="compose-tool-btn" data-cmd="insertUnorderedList" title="Bulleted list"><i class="ti-list"></i></button>
                        <button type="button" class="compose-tool-btn" data-cmd="insertOrderedList" title="Numbered list"><i class="ti-list-ol"></i></button>
                        <button type="button" class="compose-tool-btn" data-cmd="outdent" title="Outdent"><i class="ti-shift-left-alt"></i></button>
                        <button type="button" class="compose-tool-btn" data-cmd="indent" title="Indent"><i class="ti-shift-right-alt"></i></button>
                    </div>
                    <div id="reminder-mail-body" class="compose-editor" contenteditable="true" data-placeholder="Your Message Here...."></div>
                </div>
                <div class="compose-attach-row">
                    <button type="button" class="btn-compose-attach" id="reminder-attachment-btn">
                        <i class="ti-clip"></i> Attachment
                    </button>
                    <p class="compose-attach-hint">Maximum 20MB per file</p>
                    <div class="compose-attach-previews" id="reminder-attach-previews"></div>
                    <input type="file" id="reminder-attachment-input" multiple style="display:none;"
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.zip">
                </div>
            </div>
            <div class="compose-footer">
                <button type="button" class="btn-compose-discard" id="reminder-mail-discard">
                    <i class="ti-close"></i> Discard
                </button>
                <div class="compose-footer-right">
                    <button type="button" class="btn-compose-draft" id="reminder-mail-draft" title="Open in your mail app from your email address">
                        <i class="ti-share"></i> Open in mail app
                    </button>
                    <button type="button" class="btn-compose-send" id="reminder-mail-send">
                        <i class="ti-email"></i> Send from server
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
