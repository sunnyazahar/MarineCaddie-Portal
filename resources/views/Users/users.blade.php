@extends('layouts.app')

@section('styles')
    <!-- Data Table Css -->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/pages/data-table/css/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}">
    <!-- Bootstrap Multiselect css -->
    <link rel="stylesheet" href="{{ asset('files/bower_components/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}" />
    <!-- Select 2 css -->
    <link rel="stylesheet" href="{{ asset('files/bower_components/select2/dist/css/select2.min.css') }}" />
    <style>
        #offices-table tbody td {
            padding: 4px 5px !important;
            vertical-align: middle !important;
            font-size: 12px;
            white-space: normal !important;
        }
        .btn-teal {
            background-color: #008080;
            border-color: #008080;
            color: white;
        }
        .btn-teal:hover {
            background-color: #006666;
            border-color: #006666;
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
            line-height: 28px !important;
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
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">

          @include('layouts.top-menu')
                @include('layouts.left-menu')
                     <!-- Page-body start -->
                      <br>
                      <div class="pcoded-content">
                        <div class="pcoded-inner-content">
                        <!-- Main-body start -->
                            <div class="main-body">
                                <div class="page-wrapper">                                   
                                   @if (session('success'))
                                       <div class="alert alert-success">{{ session('success') }}</div>
                                   @endif
                                   <div class="header-search-bar">
                                       <div class="search-inner">
                                          
                                       </div>
                                       <button type="button" class="btn-add-office" data-toggle="modal" data-target="#addUserModal">Add User</button>
                                   </div>

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
                                                   </td>
                                                   <td>
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

                                   <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
                                       <div class="modal-dialog modal-dialog-centered" role="document">
                                           <div class="modal-content">
                                               <form id="add-user-form" method="POST" action="{{ route('users.store') }}">
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
                                                               class="form-control @error('name') is-invalid @enderror" required>
                                                           @error('name')
                                                               <div class="invalid-feedback">{{ $message }}</div>
                                                           @enderror
                                                       </div>
                                                       <div class="form-group">
                                                           <label for="user-email">Email</label>
                                                           <input type="email" id="user-email" name="email" value="{{ old('email') }}"
                                                               class="form-control @error('email') is-invalid @enderror" required>
                                                           @error('email')
                                                               <div class="invalid-feedback">{{ $message }}</div>
                                                           @enderror
                                                       </div>
                                                       <div class="form-group">
                                                           <label for="user-phone-number">Phone number</label>
                                                           <input type="text" id="user-phone-number" name="phone_number" value="{{ old('phone_number') }}"
                                                               class="form-control @error('phone_number') is-invalid @enderror">
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
                                                               class="form-control @error('password') is-invalid @enderror" required>
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
                                                               class="form-control" required>
                                                           <div id="password-confirmation-text" class="password-feedback"></div>
                                                       </div>
                                                   </div>
                                                   <div class="modal-footer">
                                                       <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                                       <button type="submit" class="btn btn-primary">Create user</button>
                                                   </div>
                                               </form>
                                           </div>
                                       </div>
                                   </div>

                                   <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
                                       <div class="modal-dialog modal-dialog-centered" role="document">
                                           <div class="modal-content">
                                               <form id="edit-user-form" method="POST"
                                                   action="{{ old('editing_user_id') ? route('users.update', old('editing_user_id')) : '#' }}">
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
                                                               class="form-control @error('name', 'editUser') is-invalid @enderror" required>
                                                           @error('name', 'editUser')
                                                               <div class="invalid-feedback">{{ $message }}</div>
                                                           @enderror
                                                       </div>
                                                       <div class="form-group">
                                                           <label for="edit-user-email">Email</label>
                                                           <input type="email" id="edit-user-email" name="email" value="{{ old('email') }}"
                                                               class="form-control @error('email', 'editUser') is-invalid @enderror" required>
                                                           @error('email', 'editUser')
                                                               <div class="invalid-feedback">{{ $message }}</div>
                                                           @enderror
                                                       </div>
                                                       <div class="form-group">
                                                           <label for="edit-user-phone-number">Phone number</label>
                                                           <input type="text" id="edit-user-phone-number" name="phone_number" value="{{ old('phone_number') }}"
                                                               class="form-control @error('phone_number', 'editUser') is-invalid @enderror">
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
                                                       <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                                       <button type="submit" class="btn btn-primary">Save changes</button>
                                                   </div>
                                               </form>
                                           </div>
                                       </div>
                                   </div>
                                </div>
                            </div>
                            <div id="styleSelector">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
     <!-- Required Jquery -->
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-validation/dist/jquery.validate.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-ui/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/popper.js/dist/umd/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-slimscroll/jquery.slimscroll.js') }}"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/modernizr/modernizr.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/modernizr/feature-detects/css-scrollbars.js') }}"></script>

    <!-- data-table js -->
    <script src="{{ asset('files/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('files/assets/pages/data-table/js/jszip.min.js') }}"></script>
    <script src="{{ asset('files/assets/pages/data-table/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('files/assets/pages/data-table/js/vfs_fonts.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('files/assets/pages/data-table/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <!-- i18next.min.js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/i18next/i18next.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-xhr-backend/i18nextXHRBackend.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-browser-languagedetector/i18nextBrowserLanguageDetector.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-i18next/jquery-i18next.min.js') }}"></script>
    <!-- Custom js -->
    {{-- <script src="{{ asset('files/assets/pages/data-table/js/data-table-custom.js') }}"></script> --}}
    <!-- Bootstrap Multiselect js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}"></script>
    <script src="{{ asset('files/assets/js/pcoded.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/vartical-layout.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/js/script.js') }}"></script>
    <!-- Select 2 js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
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
                "dom": 't', // Only show the table itself
                "pageLength": 100,
                "ordering": true,
                "autoWidth": false,
                "responsive": true
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
