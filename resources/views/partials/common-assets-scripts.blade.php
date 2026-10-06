{{-- Shared vendor JS for authenticated app pages (Tailwind v2). jQuery is loaded in layouts/app head. --}}
<script type="text/javascript" src="{{ asset('files/bower_components/jquery-ui/jquery-ui.min.js') }}"></script>

<script>
    (function ($) {
        window.mcCsrfToken = function () {
            return $('meta[name="csrf-token"]').attr('content') || '';
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': window.mcCsrfToken()
            }
        });

        /**
         * Administration deletes: real HTTP DELETE + CSRF header.
         * Do NOT use POST+_method spoofing here — nested admin routes only register PUT/DELETE,
         * so an unspoofed POST returns "POST method is not supported".
         * Hostinger may drop DELETE bodies, so CSRF must live in the header (not only _token body).
         */
        window.mcAjaxDelete = function (options) {
            var token = window.mcCsrfToken();
            var settings = $.extend(true, {}, options || {});

            settings.type = 'DELETE';
            settings.method = 'DELETE';
            settings.headers = $.extend({}, settings.headers || {}, {
                'X-CSRF-TOKEN': token
            });

            // Keep _token in data when possible; header is the reliable source on Hostinger.
            if (settings.data == null || typeof settings.data === 'object') {
                settings.data = $.extend({}, settings.data || {}, {
                    _token: token
                });
            }

            return $.ajax(settings);
        };

        /**
         * Every message from a failed AJAX call: all Laravel validation errors
         * (not just the "(and N more errors)" summary), then message/error, then fallback.
         */
        window.mcAjaxErrorMessages = function (xhr, fallbackMessage) {
            var json = (xhr && xhr.responseJSON) || null;
            var messages = [];

            if (json && json.errors && typeof json.errors === 'object') {
                $.each(json.errors, function (field, fieldMessages) {
                    $.each([].concat(fieldMessages), function (i, message) {
                        if (typeof message === 'string' && message !== '') {
                            messages.push(message);
                        }
                    });
                });
            }

            if (!messages.length && json) {
                var single = json.message || json.error;
                if (typeof single === 'string' && single !== '') {
                    messages.push(single);
                }
            }

            if (!messages.length && xhr) {
                if (xhr.status === 413) {
                    messages.push('The file is too large to upload.');
                } else if (xhr.status === 419) {
                    messages.push('Your session has expired. Please refresh the page and try again.');
                }
            }

            if (!messages.length) {
                messages.push(fallbackMessage || 'Something went wrong. Please try again.');
            }

            return $.grep(messages, function (message, index) {
                return $.inArray(message, messages) === index;
            });
        };

        var mcOpenErrorMessages = null;

        /**
         * Error SweetAlert (text is escaped by SweetAlert; one message per line).
         * Errors raised while one is still open (e.g. several files rejected at once)
         * are added to it instead of replacing it.
         */
        window.mcShowErrors = function (title, messages) {
            messages = $.grep([].concat(messages || []), function (message) {
                return typeof message === 'string' && message !== '';
            });
            if (!messages.length) {
                return;
            }

            if (typeof swal !== 'function') {
                alert(messages.join('\n'));
                return;
            }

            var stillOpen = mcOpenErrorMessages !== null && $('.sweet-alert').hasClass('visible');
            var combined = (stillOpen ? mcOpenErrorMessages : []).concat(messages);
            mcOpenErrorMessages = $.grep(combined, function (message, index) {
                return $.inArray(message, combined) === index;
            });

            swal({
                title: title || 'Error',
                text: mcOpenErrorMessages.join('\n'),
                type: 'error'
            }, function () {
                mcOpenErrorMessages = null;
            });
        };
    })(jQuery);
</script>

<script src="{{ asset('files/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('files/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('files/assets/pages/data-table/js/jszip.min.js') }}"></script>
<script src="{{ asset('files/assets/pages/data-table/js/pdfmake.min.js') }}"></script>
<script src="{{ asset('files/assets/pages/data-table/js/vfs_fonts.js') }}"></script>
<script src="{{ asset('files/bower_components/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('files/bower_components/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('files/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('files/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
@include('partials.searchable-filter-multiselect-script')
@include('partials.filter-state-persistence-script')
@include('partials.multiselect-select2-shim')

<script type="text/javascript" src="{{ asset('files/bower_components/i18next/i18next.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('files/bower_components/i18next-xhr-backend/i18nextXHRBackend.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('files/bower_components/i18next-browser-languagedetector/i18nextBrowserLanguageDetector.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('files/bower_components/jquery-i18next/jquery-i18next.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('files/assets/js/sweetalert.js') }}"></script>

<script type="text/javascript" src="{{ asset('files/bower_components/moment/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('files/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script type="text/javascript" src="{{ asset('files/assets/pages/advance-elements/bootstrap-datetimepicker.min.js') }}"></script>
