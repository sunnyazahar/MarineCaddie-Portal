@extends('layouts.app')

@section('styles')
    @include('offices.partials.office-user-form-styles')
@endsection

@section('content')
    <script>document.body.classList.add('office-user-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="office-user-page">
        <div class="office-user-hero">
            <div class="office-user-hero-main">
                <span class="office-user-hero-icon" aria-hidden="true">
                    <i class="ti-user"></i>
                </span>
                <div>
                    <p class="office-user-kicker">Manager user</p>
                    <h1 class="office-user-title">{{ $contact->name }}</h1>
                    <p class="office-user-sub">
                        Edit manager contact for <strong>{{ $office->office_name }}</strong>.
                    </p>
                </div>
            </div>
            <a href="{{ route('offices.edit', $office->id) }}#manager-users" class="office-user-back">
                <i class="ti-arrow-left"></i> Back to office
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show office-user-form-alert" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif

        <div class="office-user-card is-tabbed">
            <div class="office-user-tabs" role="tablist">
                <button type="button" class="office-user-tab active" data-tab="user-details" role="tab">User details</button>
                <button type="button" class="office-user-tab" data-tab="vessels" role="tab">Vessels</button>
                <button type="button" class="office-user-tab" data-tab="user-access" role="tab">User access</button>
            </div>

            <form
                action="{{ route('offices.manager_users.update', ['office' => $office->id, 'contact' => $contact->id]) }}"
                method="POST"
                id="managerUserEditForm"
                class="office-user-form"
            >
                @csrf
                @method('PUT')

                <div id="user-details" class="office-user-tab-pane active">
                    <div class="office-user-form-container">
                        <div class="office-user-pillar">
                            <div class="office-user-pillar__title">User details</div>

                            <div class="office-user-fields">
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="manager_user_name">Name</label>
                                    <input type="text" id="manager_user_name" name="name" class="form-control-custom"
                                        value="{{ old('name', $contact->name) }}" required autocomplete="name">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="manager_user_email">Email</label>
                                    <input type="email" id="manager_user_email" name="email" class="form-control-custom"
                                        value="{{ old('email', $contact->email) }}" required autocomplete="email">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="manager_user_phone">Phone number (with country code)</label>
                                    <input type="text" id="manager_user_phone" name="phone_number" class="form-control-custom"
                                        value="{{ old('phone_number', $contact->phone_number) }}" autocomplete="tel">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="manager_user_reply_to">Reply to on emails</label>
                                    <input type="email" id="manager_user_reply_to" name="reply_to_email" class="form-control-custom"
                                        value="{{ old('reply_to_email', $contact->reply_to_email) }}" autocomplete="email">
                                </div>

                                <div class="office-user-checkbox">
                                    <input type="checkbox" name="is_cc_enabled" id="is_cc_enabled" value="1"
                                        {{ old('is_cc_enabled', $contact->is_cc_enabled) ? 'checked' : '' }}>
                                    <label for="is_cc_enabled">Add as CC on emails</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>

            <div id="vessels" class="office-user-tab-pane">
                <div class="office-user-form-container">
                    <div class="office-user-vessels-toolbar">
                        <label for="vessels-search">Search</label>
                        <input type="search" id="vessels-search" class="office-user-vessels-search" placeholder="Filter vessels…">
                    </div>
                    <div class="office-user-table-wrap">
                        <table class="office-user-vessels-table">
                            <thead>
                                <tr>
                                    <th style="width: 44px;"><input type="checkbox" class="vessel-checkbox" id="vessels-check-all" aria-label="Select all vessels"></th>
                                    <th>Vessel</th>
                                    <th>Customer</th>
                                    <th>IMO</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $vessels = [
                                        ['ALICE OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9942720'],
                                        ['BRITTA OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9853046'],
                                        ['CARL OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9484704'],
                                        ['CEDRIC OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9591571'],
                                        ['CLIVIA OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9599195'],
                                        ['CORA OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9622916'],
                                        ['CHIARA OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9599171'],
                                        ['CHRISTINE OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9537898'],
                                        ['EDGAR OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9794484'],
                                        ['EDWARD OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9702613'],
                                        ['EIKE OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9794472'],
                                        ['ELSA OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9702625'],
                                        ['ERNST OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9702637'],
                                        ['ERNA OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9717870'],
                                        ['EVA OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9707754'],
                                        ['GEBE OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9727596'],
                                        ['General Stock Oldendorff (for BGL SIN)', 'Oldendorff (BGL Singapore)', ''],
                                        ['GINA OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9942732'],
                                        ['HAUKE OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9871115'],
                                        ['HILLE OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9731573'],
                                        ['HEDWIG OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9742728'],
                                        ['HEIDE OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9871103'],
                                        ['HELGA OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9713040'],
                                        ['HENRY OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9871086'],
                                        ['HERMINE OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9718375'],
                                        ['JENS OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9852028'],
                                        ['KENDRA OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9849813'],
                                        ['KIM OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9848998'],
                                        ['KIRA OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9867566'],
                                        ['KLARA OLDENDORFF (for BGL SIN)', 'Oldendorff (BGL Singapore)', '9849007'],
                                    ];
                                @endphp
                                @foreach ($vessels as $vessel)
                                    <tr>
                                        <td><input type="checkbox" class="vessel-checkbox"></td>
                                        <td>{{ $vessel[0] }}</td>
                                        <td>{{ $vessel[1] }}</td>
                                        <td>{{ $vessel[2] }}</td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="user-access" class="office-user-tab-pane">
                <div class="office-user-empty-pane">User access settings coming soon.</div>
            </div>
        </div>

        <div class="office-user-footer" id="manager-user-footer">
            <button type="submit" class="btn-save-custom" form="managerUserEditForm">Save user</button>
            <a href="{{ route('offices.edit', $office->id) }}#manager-users" class="btn-cancel-custom">Cancel</a>
            <div class="audit-info">
                @include('partials.audit-info', ['record' => $contact, 'bold' => true])
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')

    <script>
        $(document).ready(function () {
            $('body').addClass('office-user-page');

            function activateOfficeUserTab(tabId) {
                if (!tabId || !$('#' + tabId).length) {
                    return;
                }

                $('.office-user-tab').removeClass('active');
                $('.office-user-tab[data-tab="' + tabId + '"]').addClass('active');
                $('.office-user-tab-pane').removeClass('active');
                $('#' + tabId).addClass('active');

                if (tabId === 'user-details') {
                    $('#manager-user-footer').show();
                } else {
                    $('#manager-user-footer').hide();
                }
            }

            $('.office-user-tab').on('click', function () {
                activateOfficeUserTab($(this).data('tab'));
            });

            $('.office-user-vessels-search').on('input', function () {
                var value = $(this).val().toLowerCase();
                $('.office-user-vessels-table tbody tr').each(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            $('#vessels-check-all').on('change', function () {
                var isChecked = $(this).prop('checked');
                $('#vessels .office-user-vessels-table tbody .vessel-checkbox').prop('checked', isChecked);
            });
        });
    </script>

    @include('partials.unsaved-changes-guard', [
        'formSelector' => '#managerUserEditForm',
        'fallbackUrl' => route('offices.edit', $office->id) . '#manager-users',
    ])
@endsection
