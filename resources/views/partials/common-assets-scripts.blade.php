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
