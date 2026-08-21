@extends('layouts.app')

@section('styles')
    <!-- Data Table Css -->
    @include('partials.list-pagination-footer-styles')

    <style>
        /* Users list: stocks-like full-height shell */
        body.users-list-page {
            overflow: hidden !important;
            height: 100vh;
        }
        body.users-list-page .pcoded-content {
            overflow: hidden !important;
        }
        body.users-list-page .pcoded-inner-content,
        body.users-list-page .main-body,
        body.users-list-page .page-wrapper,
        body.users-list-page .page-body {
            height: 100%;
            overflow: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .users-list-card {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 64px);
            margin: 0 !important;
            border-radius: 0 !important;
            border-left: none !important;
            border-right: none !important;
            overflow: hidden;
        }
        .users-list-card > .card-block {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            overflow: hidden;
            padding: 8px 12px 8px !important;
        }
        .users-list-card .list-page-header {
            flex-shrink: 0;
            margin-bottom: 8px;
        }
        .users-toolbar {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }
        .users-search-input {
            height: var(--mc-control-height, 34px);
            min-width: 220px;
            max-width: 320px;
            width: 100%;
            border: 1px solid #d6e3ee;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 13px;
            color: #0e1d4a;
            background: #fff;
        }
        .users-search-input:focus {
            outline: none;
            border-color: #00aeef;
            box-shadow: 0 0 0 3px rgba(0, 174, 239, 0.15);
        }
        .users-table-area {
            flex: 1;
            min-height: 0;
            overflow: auto;
            -webkit-overflow-scrolling: touch;
        }
        #users-pagination.pagination-sticky-footer {
            flex-shrink: 0;
        }
        .btn-teal {
            background-color: #008080;
            border-color: #008080;
            color: white;
        }
        .btn-teal:hover {
            background-color: #006666;
            border-color: #006666;
            color: #fff;
        }
        .btn-outline-teal {
            color: #008080;
            border-color: #008080;
            background-color: transparent;
        }
        .btn-outline-teal:hover {
            background-color: #008080;
            color: white;
        }

        #addUserModal .modal-footer,
        #editUserModal .modal-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 14px;
        }
        #addUserModal .btn-modal-cancel,
        #editUserModal .btn-modal-cancel {
            color: #008080;
            font-size: 13px;
            font-weight: 700;
            background: transparent;
            border: none;
            padding: 8px 4px;
            box-shadow: none;
        }
        #addUserModal .btn-modal-cancel:hover,
        #editUserModal .btn-modal-cancel:hover {
            color: #0e1d4a;
            text-decoration: underline;
            background: transparent;
        }
        #addUserModal .btn-modal-save,
        #editUserModal .btn-modal-save {
            background: linear-gradient(135deg, #00aeef 0%, #008080 100%);
            color: #fff;
            border: none;
            padding: 10px 28px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.01em;
            box-shadow: 0 4px 12px rgba(0, 128, 128, 0.28);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        #addUserModal .btn-modal-save:hover,
        #editUserModal .btn-modal-save:hover {
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 128, 128, 0.34);
        }
        .filter-label {
            font-size: 11px;
            color: #666;
            margin-bottom: 2px;
            display: block;
        }
        .filter-input {
            height: 32px;
            font-size: 13px;
            border-radius: 2px;
        }
        .clear-filters {
            font-size: 12px;
            color: #ff5252;
            text-decoration: none;
            cursor: pointer;
            margin-top: 25px;
            display: inline-block;
        }
        .card-header-actions .btn {
            font-size: 12px;
            padding: 6px 15px;
            border-radius: 2px;
        }
        .custom-row {
            margin-right: -10px;
            margin-left: -10px;
        }
        .custom-col {
            padding-right: 10px;
            padding-left: 10px;
            flex: 0 0 11.5%;
            max-width: 11.5%;
        }
        @media (max-width: 992px) {
            .custom-col {
                flex: 0 0 33.33%;
                max-width: 33.33%;
            }
        }
        @media (max-width: 768px) {
            .custom-col {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }
        .filter-input {
            height: 30px;
            font-size: 11px;
            border-radius: 2px;
        }
        
        /* Bootstrap Multiselect Custom Styling */
        .multiselect-native-select .btn-group {
            width: 100%;
        }
        .multiselect-native-select .multiselect {
            width: 100%;
            text-align: left;
            height: 30px;
            padding: 4px 10px;
            font-size: 11px;
            background-color: #fff;
            border: 1px solid #ced4da;
            color: #495057;
        }
        .multiselect-native-select .multiselect-container {
            width: 235px;
            font-size: 11px;
        }
        .multiselect-native-select .multiselect-container li a label {
            padding: 5px 10px 5px 0;
            display: block;
            margin: 0;
            cursor: pointer;
        }
        .multiselect-native-select .multiselect-selected .form-check-label {
            color: #008080;
            font-weight: bold;
        }
        .multiselect-item.multiselect-all label {
            font-weight: bold;
            color: #333;
        }
        input.form-control.multiselect-search {
            font-size: 11px;
        }
        .multiselect-container .input-group {
            margin: 2px;
        }
        .input-group-addon {
            background-color: #01a9ac;
            color: #fff;
            max-height: 31px;
        }
        .multiselect-container>li {
            padding: 0px 5px;
        }
        .multiselect-item .input-group {
            width: 114%;
        }
        /* Select2 Custom Styling */
        .select2-container .select2-selection--single {
            height: 30px !important;
            font-size: 11px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.25 !important;
            padding: 2px 8px;
        }
        #addUserModal .select2-container--default .select2-selection--single,
        #addUserModal .select2-container--default.select2-container--focus .select2-selection--single,
        #addUserModal .select2-container--default.select2-container--open .select2-selection--single,
        #addUserModal .select2-container--default .select2-selection--single .select2-selection__rendered,
        #editUserModal .select2-container--default .select2-selection--single,
        #editUserModal .select2-container--default.select2-container--focus .select2-selection--single,
        #editUserModal .select2-container--default.select2-container--open .select2-selection--single,
        #editUserModal .select2-container--default .select2-selection--single .select2-selection__rendered {
            background-color: transparent !important;
            color: #495057 !important;
        }
        #addUserModal .select2-container--default .select2-selection--single .select2-selection__arrow b,
        #editUserModal .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #64748b transparent transparent transparent !important;
        }
        #addUserModal .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b,
        #editUserModal .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #64748b transparent !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #008080;
            border: 1px solid #006666;
            color: #fff;
            font-size: 10px;
            margin-top: 2px;
        }
        .select2-container--default .select2-selection--multiple {
            min-height: 30px;
            border: 1px solid #ced4da;
            border-radius: 2px;
        }
        /* Filter Toggle Button Styling */
        .btn-filter-toggle {
            height: 30px;
            padding: 4px 10px;
            font-size: 14px;
            color: #008080;
            border-color: #008080;
            background-color: transparent;
        }
        .btn-filter-toggle:hover, .btn-filter-toggle:focus, .btn-filter-toggle:active {
            background-color: #008080 !important;
            color: white !important;
            border-color: #008080 !important;
        }

        /* Reduce gap/margin between sidebar and content */
        .pcoded-inner-content {
            padding: 5px !important;
        }
        .main-body .page-wrapper {
            padding: 5px !important;
        }

        /* Premium Index Styles */
        .page-title-link {
            font-size: 13px;
            color: #3b82f6;
            text-decoration: none;
            background: #eef2ff;
            padding: 4px 12px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 15px;
            font-weight: 500;
        }
        .header-search-bar {
            background: #fff;
            padding: 10px 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .search-inner {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .search-text {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
        }
        .search-input-custom {
            border: 1px solid #f3f4f6;
            padding: 6px 12px;
            font-size: 13px;
            width: 200px;
            border-radius: 4px;
            color: #9ca3af;
        }
        .btn-add-office {
            border: 1px solid #3b82f6;
            color: #3b82f6;
            background: #fff;
            padding: 6px 15px;
            font-size: 13px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
        }
        .btn-add-office:hover {
            background: #3b82f6;
            color: #fff;
        }

        .office-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        .office-table th {
            text-align: left;
            padding: 12px 15px;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }
        .office-table td {
            padding: 12px 15px;
            font-size: 12px;
            color: #4b5563;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }
        .office-name-link {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
        }
        .office-name-link:hover {
            text-decoration: underline;
        }
        .badge-inactive {
            background: #e5e7eb;
            color: #6b7280;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
        }
        .badge-active {
            background: #ecfdf5;
            color: #065f46;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
            border: 1px solid #10b981;
        }
        .badge-blocked {
            background: #fef2f2;
            color: #b91c1c;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
            border: 1px solid #fca5a5;
            margin-left: 6px;
        }
        .btn-unblock-user {
            font-size: 11px;
            padding: 2px 8px;
            line-height: 1.4;
            margin-right: 8px;
        }
        .flag-icon {
            margin-right: 8px;
            vertical-align: middle;
            width: 18px;
        }
        .action-icon {
            color: #9ca3af;
            cursor: pointer;
            font-size: 14px;
        }
        .action-icon:hover {
            color: #4b5563;
        }
        .header-search-bar {
            margin-bottom: -5px;
            margin-top: 30px;
        }
        .password-strength-track {
            height: 5px;
            margin-top: 7px;
            overflow: hidden;
            border-radius: 4px;
            background: #e5e7eb;
        }
        .password-strength-bar {
            width: 0;
            height: 100%;
            transition: width 0.2s ease, background-color 0.2s ease;
        }
        .password-feedback {
            min-height: 18px;
            margin-top: 4px;
            font-size: 11px;
        }
        .password-feedback.is-weak {
            color: #dc2626;
        }
        .password-feedback.is-normal {
            color: #d97706;
        }
        .password-feedback.is-strong,
        .password-feedback.is-matching {
            color: #16a34a;
        }
        .password-feedback.is-mismatch {
            color: #dc2626;
        }

        input#edit-user-name {
            font-size: 12px;
        }
        input#edit-user-email {
            font-size: 12px;
        }
        input#edit-user-phone-number {
            font-size: 12px;
        }
        input#edit-user-role {
            font-size: 12px;
        }
        input#edit-user-password {
            font-size: 12px;
        }
        input#edit-user-password-confirmation {
            font-size: 12px;
        }

        #offices-table {
            min-width: 900px !important;
            width: 100% !important;
        }

        #offices-table th,
        #offices-table td {
            font-size: 13px !important;
            white-space: nowrap;
        }

        /* Hide DataTables scrollX cloned header */
        #offices-table_wrapper .dataTables_scrollBody > table > thead,
        #offices-table_wrapper .dataTables_scrollBody thead {
            height: 0 !important;
            line-height: 0 !important;
            visibility: collapse !important;
        }
        #offices-table_wrapper .dataTables_scrollBody thead tr,
        #offices-table_wrapper .dataTables_scrollBody thead th {
            height: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            border: none !important;
            line-height: 0 !important;
            font-size: 0 !important;
            overflow: hidden !important;
            background: transparent !important;
        }
        #offices-table_wrapper .dataTables_scrollBody thead th:before,
        #offices-table_wrapper .dataTables_scrollBody thead th:after {
            display: none !important;
            content: none !important;
        }

        /* Kill responsive "+" control if any leftover CSS loads */
        table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control:before,
        table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control:before {
            display: none !important;
        }

        @media (max-width: 991.98px) {
            .header-search-bar {
                flex-wrap: wrap;
                gap: 10px;
                padding: 10px 12px;
                margin-bottom: 12px;
            }

            .btn-add-office {
                width: 100%;
                text-align: center;
            }

            .users-table-wrap,
            .dataTables_wrapper,
            .dataTables_scroll,
            .dataTables_scrollBody {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }

            #offices-table th,
            #offices-table td {
                font-size: 11px !important;
                padding: 10px 12px !important;
            }
        }
    </style>
@endsection

@section('content')
<!-- Pre-loader start -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class='contain'>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pre-loader end -->
    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])
                                   @if (session('success'))
                                       <div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size: 12px; margin: 8px 12px 0;">
                                           {{ session('success') }}
                                           <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                       </div>
                                   @endif

                                   <div class="card users-list-card">
                                       <div class="card-block">
                                           <x-lists.page-header
                                               title="Users"
                                               subtitle="Manage portal accounts, roles, and OTP unlocks"
                                               icon="ti-user"
                                               :count="$users->total()"
                                               countLabel="users"
                                           >
                                               <x-slot:actions>
                                                   <button type="button" id="btn-add-user" class="btn btn-teal btn-sm d-none d-lg-inline-flex" data-toggle="modal" data-target="#addUserModal">
                                                       Add user
                                                   </button>
                                               </x-slot:actions>
                                           </x-lists.page-header>

                                           <div class="users-toolbar">
                                               <input type="text" class="users-search-input search-input-custom" placeholder="Search users…" aria-label="Search users">
                                               <button type="button" id="btn-add-user-mobile" class="btn btn-teal btn-sm d-lg-none" data-toggle="modal" data-target="#addUserModal">
                                                   Add user
                                               </button>
                                           </div>

                                           <div class="users-table-area">
                                   <table id="offices-table" class="office-table">
                                       <thead>
                                           <tr>
                                               <th>First name</th>
                                               <th>Last name</th>
                                               <th>Username</th>
                                               <th>Email</th>
                                               <th>Phone</th>
                                               <th>Role</th>
                                               <th>Status</th>
                                               <th>Action</th>
                                           </tr>
                                       </thead>
                                       <tbody>
                                           @foreach ($users as $user)
                                               @php
                                                   $nameParts = preg_split('/\s+/', trim($user->name), 2);
                                                   $firstName = $nameParts[0] ?? '';
                                                   $lastName = $nameParts[1] ?? '';
                                                   $isOtpBlocked = filled($user->otp_blocked_until) && now()->lessThan($user->otp_blocked_until);
                                               @endphp
                                               <tr>
                                                   <td>{{ $firstName }}</td>
                                                   <td>{{ $lastName ?: '—' }}</td>
                                                   <td>{{ \Illuminate\Support\Str::before($user->email, '@') }}</td>
                                                   <td>{{ $user->email }}</td>
                                                   <td>{{ $user->phone_number ?: '—' }}</td>
                                                   <td>{{ $user->role ?: '—' }}</td>
                                                   <td>
                                                       <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">
                                                           {{ $user->is_active ? 'Active' : 'Inactive' }}
                                                       </span>
                                                       @if ($isOtpBlocked)
                                                           <span class="badge-blocked">OTP Blocked</span>
                                                       @endif
                                                   </td>
                                                   <td>
                                                       @if ($isOtpBlocked)
                                                           <form method="POST" action="{{ route('users.unblock', $user) }}" class="d-inline">
                                                               @csrf
                                                               <button type="submit"
                                                                   class="btn btn-sm btn-outline-success btn-unblock-user"
                                                                   onclick="return confirm('Unblock this user and clear the OTP lock?')">
                                                                   Unblock
                                                               </button>
                                                           </form>
                                                       @endif
                                                       <button type="button"
                                                           class="btn btn-link p-0 action-icon edit-user-btn"
                                                           title="Edit user"
                                                           data-user-id="{{ $user->id }}"
                                                           data-name="{{ $user->name }}"
                                                           data-email="{{ $user->email }}"
                                                           data-phone-number="{{ $user->phone_number }}"
                                                           data-role="{{ $user->role }}"
                                                           data-is-active="{{ $user->is_active ? '1' : '0' }}"
                                                           data-office-ids='@json($user->offices->modelKeys())'
                                                           data-hub-ids='@json($user->hubs->modelKeys())'
                                                           data-agent-ids='@json($user->agents->modelKeys())'
                                                           data-supplier-ids='@json($user->suppliers->modelKeys())'>
                                                           <i class="ti-pencil"></i>
                                                       </button>
                                                   </td>
                                               </tr>
                                           @endforeach
                                       </tbody>
                                   </table>
                                           </div>

                                           <div id="users-pagination" class="pagination-sticky-footer">
                                               @include('partials.list-pagination-footer-inner', ['paginator' => $users])
                                           </div>
                                       </div>
                                   </div>

                                   <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
                                       <div class="modal-dialog modal-dialog-centered" role="document">
                                           <div class="modal-content">
                                               <form id="add-user-form" method="POST" action="{{ route('users.store') }}" autocomplete="off">
                                                   @csrf
                                                   <div class="modal-header">
                                                       <h5 class="modal-title" id="addUserModalLabel">Create new user</h5>
                                                       <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                           <span aria-hidden="true">&times;</span>
                                                       </button>
                                                   </div>
                                                   <div class="modal-body">
                                                       <div class="form-group">
                                                           <label for="user-name">Name</label>
                                                           <input type="text" id="user-name" name="name" value="{{ old('name') }}"
                                                               class="form-control @error('name') is-invalid @enderror" autocomplete="off" required>
                                                           @error('name')
                                                               <div class="invalid-feedback">{{ $message }}</div>
                                                           @enderror
                                                       </div>
                                                       <div class="form-group">
                                                           <label for="user-email">Email</label>
                                                           <input type="email" id="user-email" name="email" value="{{ old('email') }}"
                                                               class="form-control @error('email') is-invalid @enderror" autocomplete="off" required>
                                                           @error('email')
                                                               <div class="invalid-feedback">{{ $message }}</div>
                                                           @enderror
                                                       </div>
                                                       <div class="form-group">
                                                           <label for="user-phone-number">Phone number</label>
                                                           <input type="text" id="user-phone-number" name="phone_number" value="{{ old('phone_number') }}"
                                                               class="form-control @error('phone_number') is-invalid @enderror" autocomplete="off">
                                                           @error('phone_number')
                                                               <div class="invalid-feedback">{{ $message }}</div>
                                                           @enderror
                                                       </div>
                                                       <div class="form-group">
                                                           <label for="user-role">Role</label>
                                                           <select id="user-role" name="role"
                                                               class="form-control select2-user-role @error('role') is-invalid @enderror" required>
                                                               <option value=""></option>
                                                               @foreach (['Admin', 'Operations', 'Agents', 'Accounts', 'Supplier'] as $role)
                                                                   <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>{{ $role }}</option>
                                                               @endforeach
                                                           </select>
                                                           @error('role')
                                                               <div class="invalid-feedback d-block">{{ $message }}</div>
                                                           @enderror
                                                       </div>
                                                       <div class="form-group">
                                                           <label for="user-password">Password</label>
                                                           <input type="password" id="user-password" name="password"
                                                               class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" required>
                                                           @error('password')
                                                               <div class="invalid-feedback">{{ $message }}</div>
                                                           @enderror
                                                           <div class="password-strength-track">
                                                               <div id="password-strength-bar" class="password-strength-bar"></div>
                                                           </div>
                                                           <div id="password-strength-text" class="password-feedback">
                                                               Use at least 8 characters with letters and numbers.
                                                           </div>
                                                       </div>
                                                       <div class="form-group mb-0">
                                                           <label for="user-password-confirmation">Confirm password</label>
                                                           <input type="password" id="user-password-confirmation" name="password_confirmation"
                                                               class="form-control" autocomplete="new-password" required>
                                                           <div id="password-confirmation-text" class="password-feedback"></div>
                                                       </div>
                                                   </div>
                                                   <div class="modal-footer">
                                                       <button type="button" class="btn-modal-cancel" data-dismiss="modal">Cancel</button>
                                                       <button type="submit" class="btn-modal-save">Create user</button>
                                                   </div>
                                               </form>
                                           </div>
                                       </div>
                                   </div>

                                   <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
                                       <div class="modal-dialog modal-dialog-centered" role="document">
                                           <div class="modal-content">
                                               <form id="edit-user-form" method="POST"
                                                   action="{{ old('editing_user_id') ? route('users.update', old('editing_user_id')) : '#' }}"
                                                   autocomplete="off">
                                                   @csrf
                                                   @method('PUT')
                                                   <input type="hidden" id="editing-user-id" name="editing_user_id" value="{{ old('editing_user_id') }}">
                                                   <div class="modal-header">
                                                       <h5 class="modal-title" id="editUserModalLabel">Edit user</h5>
                                                       <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                           <span aria-hidden="true">&times;</span>
                                                       </button>
                                                   </div>
                                                   <div class="modal-body">
                                                       <div class="form-group">
                                                           <label for="edit-user-name">Name</label>
                                                           <input type="text" id="edit-user-name" name="name" value="{{ old('name') }}"
                                                               class="form-control @error('name', 'editUser') is-invalid @enderror" autocomplete="off" required>
                                                           @error('name', 'editUser')
                                                               <div class="invalid-feedback">{{ $message }}</div>
                                                           @enderror
                                                       </div>
                                                       <div class="form-group">
                                                           <label for="edit-user-email">Email</label>
                                                           <input type="email" id="edit-user-email" name="email" value="{{ old('email') }}"
                                                               class="form-control @error('email', 'editUser') is-invalid @enderror" autocomplete="off" required>
                                                           @error('email', 'editUser')
                                                               <div class="invalid-feedback">{{ $message }}</div>
                                                           @enderror
                                                       </div>
                                                       <div class="form-group">
                                                           <label for="edit-user-phone-number">Phone number</label>
                                                           <input type="text" id="edit-user-phone-number" name="phone_number" value="{{ old('phone_number') }}"
                                                               class="form-control @error('phone_number', 'editUser') is-invalid @enderror" autocomplete="off">
                                                           @error('phone_number', 'editUser')
                                                               <div class="invalid-feedback">{{ $message }}</div>
                                                           @enderror
                                                       </div>
                                                       <div class="form-group">
                                                           <label for="edit-user-role">Role</label>
                                                           <select id="edit-user-role" name="role"
                                                               class="form-control select2-edit-user-role @error('role', 'editUser') is-invalid @enderror" required>
                                                               <option value=""></option>
                                                               @foreach (['Admin', 'Operations', 'Agents', 'Accounts', 'Supplier'] as $role)
                                                                   <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>{{ $role }}</option>
                                                               @endforeach
                                                           </select>
                                                           @error('role', 'editUser')
                                                               <div class="invalid-feedback d-block">{{ $message }}</div>
                                                           @enderror
                                                       </div>
                                                       <div id="office-hub-assignments" class="user-assignment-fields" style="display:none;">
                                                           <div class="form-group">
                                                               <label for="edit-user-offices">Assigned offices</label>
                                                               <select id="edit-user-offices" name="office_ids[]" class="form-control assignment-select" multiple>
                                                                   @foreach($assignmentOffices as $office)
                                                                       <option value="{{ $office->id }}" {{ in_array($office->id, old('office_ids', [])) ? 'selected' : '' }}>{{ $office->office_name }}</option>
                                                                   @endforeach
                                                               </select>
                                                           </div>
                                                           <div class="form-group">
                                                               <label for="edit-user-hubs">Assigned hubs</label>
                                                               <select id="edit-user-hubs" name="hub_ids[]" class="form-control assignment-select" multiple>
                                                                   @foreach($assignmentHubs as $hub)
                                                                       <option value="{{ $hub->id }}" {{ in_array($hub->id, old('hub_ids', [])) ? 'selected' : '' }}>{{ $hub->code ? $hub->code . ' - ' : '' }}{{ $hub->hub_name }}</option>
                                                                   @endforeach
                                                               </select>
                                                           </div>
                                                       </div>
                                                       <div id="agent-assignments" class="user-assignment-fields form-group" style="display:none;">
                                                           <label for="edit-user-agents">Assigned agents</label>
                                                           <select id="edit-user-agents" name="agent_ids[]" class="form-control assignment-select" multiple>
                                                               @foreach($assignmentAgents as $agent)
                                                                   <option value="{{ $agent->id }}" {{ in_array($agent->id, old('agent_ids', [])) ? 'selected' : '' }}>{{ $agent->code ? $agent->code . ' - ' : '' }}{{ $agent->agent_name }}</option>
                                                               @endforeach
                                                           </select>
                                                       </div>
                                                       <div id="supplier-assignments" class="user-assignment-fields form-group" style="display:none;">
                                                           <label for="edit-user-suppliers">Assigned suppliers</label>
                                                           <select id="edit-user-suppliers" name="supplier_ids[]" class="form-control assignment-select" multiple>
                                                               @foreach($assignmentSuppliers as $supplier)
                                                                   <option value="{{ $supplier->id }}" {{ in_array($supplier->id, old('supplier_ids', [])) ? 'selected' : '' }}>{{ $supplier->supplier_name }}</option>
                                                               @endforeach
                                                           </select>
                                                       </div>
                                                       <div class="form-group mb-0">
                                                           <label for="edit-user-status">Status</label>
                                                           <select id="edit-user-status" name="is_active"
                                                               class="form-control select2-edit-user-status @error('is_active', 'editUser') is-invalid @enderror" required>
                                                               <option value="1" {{ old('is_active') === '1' ? 'selected' : '' }}>Active</option>
                                                               <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                                                           </select>
                                                           @error('is_active', 'editUser')
                                                               <div class="invalid-feedback d-block">{{ $message }}</div>
                                                           @enderror
                                                       </div>
                                                   </div>
                                                   <div class="modal-footer">
                                                       <button type="button" class="btn-modal-cancel" data-dismiss="modal">Cancel</button>
                                                       <button type="submit" class="btn-modal-save">Save changes</button>
                                                   </div>
                                               </form>
                                           </div>
                                       </div>
                                   </div>
    @include('layouts.partials.pcoded-shell-end')

    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-validation/dist/jquery.validate.js') }}"></script>


    <script>
        $(document).ready(function() {
            $('body').addClass('users-list-page');

            // Keep modals above list chrome / Select2
            $('#addUserModal, #editUserModal').appendTo('body');

            $(document).on('click', '#btn-add-user, #btn-add-user-mobile', function (e) {
                e.preventDefault();
                $('#addUserModal').modal('show');
            });

             // Initialize Select2 for standard filters
            $('.select2').select2({
                placeholder: "Click here",
                allowClear: true
            });
            $('.select2-user-role').select2({
                placeholder: 'Select role',
                allowClear: false,
                width: '100%',
                dropdownParent: $('#addUserModal')
            });
            $('.select2-edit-user-role').select2({
                placeholder: 'Select role',
                allowClear: false,
                width: '100%',
                dropdownParent: $('#editUserModal')
            });
            $('.select2-edit-user-status').select2({
                minimumResultsForSearch: Infinity,
                width: '100%',
                dropdownParent: $('#editUserModal')
            });
            $('.assignment-select').select2({
                placeholder: 'Select assignments',
                width: '100%',
                dropdownParent: $('#editUserModal')
            });

            function assignmentIds($button, attributeName) {
                try {
                    return JSON.parse($button.attr(attributeName) || '[]').map(String);
                } catch (error) {
                    return [];
                }
            }

            function toggleAssignmentFields() {
                var role = $('#edit-user-role').val();
                $('.user-assignment-fields').hide();
                $('#edit-user-offices, #edit-user-hubs, #edit-user-agents, #edit-user-suppliers').prop('disabled', true);

                if (role === 'Operations' || role === 'Accounts') {
                    $('#office-hub-assignments').show();
                    $('#edit-user-offices, #edit-user-hubs').prop('disabled', false);
                } else if (role === 'Agents') {
                    $('#agent-assignments').show();
                    $('#edit-user-agents').prop('disabled', false);
                } else if (role === 'Supplier') {
                    $('#supplier-assignments').show();
                    $('#edit-user-suppliers').prop('disabled', false);
                }
            }

            var userUpdateUrlTemplate = @json(route('users.update', '__USER__'));

            $(document).on('click', '.edit-user-btn', function() {
                var $button = $(this);
                var userId = $button.data('user-id');

                $('#edit-user-form').attr('action', userUpdateUrlTemplate.replace('__USER__', userId));
                $('#editing-user-id').val(userId);
                $('#edit-user-name').val($button.attr('data-name') || '');
                $('#edit-user-email').val($button.attr('data-email') || '');
                $('#edit-user-phone-number').val($button.attr('data-phone-number') || '');
                $('#edit-user-role').val($button.attr('data-role') || '').trigger('change');
                $('#edit-user-status').val($button.attr('data-is-active')).trigger('change');
                $('#edit-user-offices').val(assignmentIds($button, 'data-office-ids')).trigger('change');
                $('#edit-user-hubs').val(assignmentIds($button, 'data-hub-ids')).trigger('change');
                $('#edit-user-agents').val(assignmentIds($button, 'data-agent-ids')).trigger('change');
                $('#edit-user-suppliers').val(assignmentIds($button, 'data-supplier-ids')).trigger('change');
                toggleAssignmentFields();
                $('#edit-user-form').validate().resetForm();
                $('#edit-user-form .is-invalid').removeClass('is-invalid');
                $('#editUserModal').modal('show');
            });

            function getPasswordStrength(password) {
                var hasLetter = /[A-Za-z]/.test(password);
                var hasNumber = /\d/.test(password);
                var hasUpper = /[A-Z]/.test(password);
                var hasLower = /[a-z]/.test(password);
                var hasSpecial = /[^A-Za-z0-9]/.test(password);

                if (password.length < 8 || !hasLetter || !hasNumber) {
                    return 'weak';
                }

                if (password.length >= 12 && hasUpper && hasLower && hasNumber && hasSpecial) {
                    return 'strong';
                }

                return 'normal';
            }

            function updatePasswordStrength() {
                var password = $('#user-password').val();
                var strength = getPasswordStrength(password);
                var $bar = $('#password-strength-bar');
                var $text = $('#password-strength-text');

                $text.removeClass('is-weak is-normal is-strong');

                if (!password) {
                    $bar.css({ width: '0', backgroundColor: 'transparent' });
                    $text.text('Use at least 8 characters with letters and numbers.');
                    return strength;
                }

                if (strength === 'weak') {
                    $bar.css({ width: '33%', backgroundColor: '#dc2626' });
                    $text.addClass('is-weak').text('Weak password — add letters, numbers, and at least 8 characters.');
                } else if (strength === 'normal') {
                    $bar.css({ width: '66%', backgroundColor: '#d97706' });
                    $text.addClass('is-normal').text('Normal password');
                } else {
                    $bar.css({ width: '100%', backgroundColor: '#16a34a' });
                    $text.addClass('is-strong').text('Strong password');
                }

                return strength;
            }

            function updatePasswordConfirmation() {
                var password = $('#user-password').val();
                var confirmation = $('#user-password-confirmation').val();
                var $text = $('#password-confirmation-text');

                $text.removeClass('is-matching is-mismatch');

                if (!confirmation) {
                    $text.text('');
                    return false;
                }

                if (password === confirmation) {
                    $text.addClass('is-matching').text('Passwords match');
                    return true;
                }

                $text.text('');
                return false;
            }

            $('#user-password').on('input', function() {
                updatePasswordStrength();
                updatePasswordConfirmation();
                if ($('#user-password-confirmation').val()) {
                    $('#user-password-confirmation').valid();
                }
            });

            $('#user-password-confirmation').on('input', updatePasswordConfirmation);

            $.validator.addMethod('passwordStrength', function(value, element) {
                return this.optional(element) || getPasswordStrength(value) !== 'weak';
            }, 'Use at least 8 characters with letters and numbers.');

            $('#add-user-form').validate({
                ignore: [],
                rules: {
                    name: {
                        required: true,
                        maxlength: 255
                    },
                    email: {
                        required: true,
                        email: true,
                        maxlength: 255
                    },
                    phone_number: {
                        maxlength: 50
                    },
                    role: {
                        required: true
                    },
                    password: {
                        required: true,
                        minlength: 8,
                        passwordStrength: true
                    },
                    password_confirmation: {
                        required: true,
                        equalTo: '#user-password'
                    }
                },
                messages: {
                    name: {
                        required: 'Please enter the user name.'
                    },
                    email: {
                        required: 'Please enter an email address.',
                        email: 'Please enter a valid email address.'
                    },
                    role: {
                        required: 'Please select a role.'
                    },
                    password: {
                        required: 'Please enter a password.',
                        minlength: 'The password must contain at least 8 characters.'
                    },
                    password_confirmation: {
                        required: 'Please confirm the password.',
                        equalTo: 'Passwords do not match.'
                    }
                },
                errorElement: 'div',
                errorClass: 'invalid-feedback d-block',
                errorPlacement: function(error, element) {
                    if (element.hasClass('select2-hidden-accessible')) {
                        error.insertAfter(element.next('.select2'));
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                },
                onkeyup: function(element) {
                    $(element).valid();
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });

            $('#user-role').on('change', function() {
                $(this).valid();
            });

            $('#edit-user-form').validate({
                ignore: [],
                rules: {
                    name: {
                        required: true,
                        maxlength: 255
                    },
                    email: {
                        required: true,
                        email: true,
                        maxlength: 255
                    },
                    phone_number: {
                        maxlength: 50
                    },
                    role: {
                        required: true
                    },
                    is_active: {
                        required: true
                    }
                },
                messages: {
                    name: 'Please enter the user name.',
                    email: {
                        required: 'Please enter an email address.',
                        email: 'Please enter a valid email address.'
                    },
                    role: 'Please select a role.',
                    is_active: 'Please select a status.'
                },
                errorElement: 'div',
                errorClass: 'invalid-feedback d-block',
                errorPlacement: function(error, element) {
                    if (element.hasClass('select2-hidden-accessible')) {
                        error.insertAfter(element.next('.select2'));
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                },
                onkeyup: function(element) {
                    $(element).valid();
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });

            $('#edit-user-role').on('change', function() {
                $(this).valid();
                toggleAssignmentFields();
            });
            $('#edit-user-status').on('change', function() {
                $(this).valid();
            });

            // Initialize Bootstrap Multiselect for special filter toggle
            $('#filter-multiselect').multiselect({
                includeSelectAllOption: true,
                enableFiltering: true,
                buttonWidth: '100%',
                maxHeight: 200,
                nonSelectedText: '',
                allSelectedText: '',
                nSelectedText: '',
                numberDisplayed: 0,
                buttonClass: 'btn btn-outline-teal btn-filter-toggle',
                templates: {
                    button: '<button type="button" class="multiselect dropdown-toggle" data-toggle="dropdown"><i class="ti-filter"></i></button>'
                },
                onChange: function(option, checked) {
                    toggleFilterVisibility();
                },
                onSelectAll: function() {
                    toggleFilterVisibility();
                },
                onDeselectAll: function() {
                    toggleFilterVisibility();
                }
            });

            function toggleFilterVisibility() {
                var selectedOptions = $('#filter-multiselect option:selected');
                var selectedValues = [];
                selectedOptions.each(function() {
                    selectedValues.push($(this).val());
                });

                var allFilters = [
                    {val: 'Office Name', id: 'col-Office-Name'},
                    {val: 'Short Name', id: 'col-Short-Name'},
                    {val: 'City', id: 'col-City'},
                    {val: 'Country', id: 'col-Country'},
                    {val: 'Phone', id: 'col-Phone'},
                    {val: 'Email', id: 'col-Email'}
                ];

                allFilters.forEach(function(filter) {
                    if (selectedValues.includes(filter.val)) {
                        $('#' + filter.id).show();
                    } else {
                        $('#' + filter.id).hide();
                    }
                });
            }
            
            // Initial call to set visibility state
            toggleFilterVisibility();

            var table = $('#offices-table').DataTable({
                "dom": 't',
                "paging": false,
                "info": false,
                "ordering": true,
                "order": [],
                "autoWidth": false,
                "responsive": false,
                "scrollX": true
            });

            // Link custom search input to DataTable
            $('.search-input-custom').on('keyup', function() {
                table.search(this.value).draw();
            });

            @if ($errors->getBag('default')->any())
                $('#addUserModal').modal('show');
            @endif
            @if ($errors->editUser->any())
                toggleAssignmentFields();
                $('#editUserModal').modal('show');
            @endif
        });
    </script>
 
@endsection
