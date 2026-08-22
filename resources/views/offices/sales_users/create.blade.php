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
                    <p class="office-user-kicker">Sales user</p>
                    <h1 class="office-user-title">Add sales user</h1>
                    <p class="office-user-sub">
                        Create a sales contact for <strong>{{ $office->office_name }}</strong>.
                    </p>
                </div>
            </div>
            <a href="{{ route('offices.edit', $office->id) }}#sales-users" class="office-user-back">
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

        <div class="office-user-card">
            <form action="{{ route('offices.sales_users.store', $office->id) }}" method="POST" id="salesUserForm" class="office-user-form">
                @csrf

                <div class="office-user-form-container">
                    <div class="office-user-pillar">
                        <div class="office-user-pillar__title">User details</div>

                        <div class="office-user-fields">
                            <div class="form-group-custom">
                                <label class="form-label-custom" for="sales_user_name">Name</label>
                                <input type="text" id="sales_user_name" name="name" class="form-control-custom"
                                    value="{{ old('name') }}" required autocomplete="name">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="sales_user_email">Email</label>
                                <input type="email" id="sales_user_email" name="email" class="form-control-custom"
                                    value="{{ old('email') }}" required autocomplete="email">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="sales_user_phone">Phone number (with country code)</label>
                                <input type="text" id="sales_user_phone" name="phone_number" class="form-control-custom"
                                    value="{{ old('phone_number') }}" autocomplete="tel">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="sales_user_reply_to">Reply to on emails</label>
                                <input type="email" id="sales_user_reply_to" name="reply_to_email" class="form-control-custom"
                                    value="{{ old('reply_to_email') }}" autocomplete="email">
                            </div>

                            <div class="office-user-checkbox">
                                <input type="checkbox" name="is_cc_enabled" id="is_cc_enabled" value="1" {{ old('is_cc_enabled') ? 'checked' : '' }}>
                                <label for="is_cc_enabled">Add as CC on emails</label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="office-user-footer">
            <button type="submit" class="btn-save-custom" form="salesUserForm">Save user</button>
            <a href="{{ route('offices.edit', $office->id) }}#sales-users" class="btn-cancel-custom">Cancel</a>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')

    <script>
        $(document).ready(function () {
            $('body').addClass('office-user-page');
        });
    </script>
@endsection
