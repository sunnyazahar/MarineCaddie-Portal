@extends('layouts.app')

@section('styles')
    @include('hub.partials.hub-user-form-styles')
@endsection

@section('content')
    <script>document.body.classList.add('hub-user-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="hub-user-page">
        <div class="hub-user-hero">
            <div class="hub-user-hero-main">
                <span class="hub-user-hero-icon" aria-hidden="true">
                    <i class="ti-user"></i>
                </span>
                <div>
                    <p class="hub-user-kicker">Hub user</p>
                    <h1 class="hub-user-title">Edit hub user</h1>
                    <p class="hub-user-sub">
                        Update <strong>{{ $user->name }}</strong> for <strong>{{ $hub->hub_name }}</strong>.
                    </p>
                </div>
            </div>
            <a href="{{ route('hub.show', $hub->id) }}#hub-users" class="hub-user-back">
                <i class="ti-arrow-left"></i> Back to hub
            </a>
        </div>

        <div class="hub-user-meta">
            @if ($hub->code)
                <span class="hub-user-meta-pill">Code <strong>{{ $hub->code }}</strong></span>
            @endif
            @if ($hub->city || $hub->country)
                <span class="hub-user-meta-pill">
                    Location
                    <strong>{{ trim(($hub->city ? $hub->city . ', ' : '') . ($hub->country ?? '')) }}</strong>
                </span>
            @endif
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show hub-user-form-alert" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif

        <div class="hub-user-card">
            <form action="{{ route('hub.users.update', [$hub->id, $user->id]) }}" method="POST" id="hubUserForm" class="hub-user-form">
                @csrf
                @method('PUT')

                <div class="hub-user-form-container">
                    <div class="hub-user-pillar">
                        <div class="hub-user-pillar__title">User details</div>

                        <div class="hub-user-fields">
                            <div class="form-group-custom">
                                <label class="form-label-custom" for="hub_user_name">Name <span class="text-danger">*</span></label>
                                <input type="text" id="hub_user_name" name="name" class="form-control-custom"
                                    value="{{ old('name', $user->name) }}" required autocomplete="name">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="hub_user_email">Email</label>
                                <input type="email" id="hub_user_email" name="email" class="form-control-custom"
                                    value="{{ old('email', $user->email) }}" autocomplete="email">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="hub_user_phone">Phone number</label>
                                <input type="text" id="hub_user_phone" name="phone_number" class="form-control-custom"
                                    value="{{ old('phone_number', $user->phone_number) }}" autocomplete="tel">
                            </div>

                            <div class="hub-user-checkbox">
                                <input type="checkbox" name="show_in_scan_gun" id="show_in_scan_gun" value="1"
                                    {{ old('show_in_scan_gun', $user->show_in_scan_gun) ? 'checked' : '' }}>
                                <label for="show_in_scan_gun">Show user in scan gun user list</label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="hub-user-footer">
            <button type="submit" class="btn-save-custom" form="hubUserForm">Save changes</button>
            <a href="{{ route('hub.show', $hub->id) }}#hub-users" class="btn-cancel-custom">Cancel</a>
            <div class="audit-info">
                @include('partials.audit-info', ['record' => $user])
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function () {
            $('body').addClass('hub-user-page');

            $('#hubUserForm').validate({
                rules: {
                    name: { required: true, minlength: 2 },
                    email: { email: true }
                },
                errorElement: 'div',
                errorClass: 'error-message',
                highlight: function (element) {
                    $(element).addClass('error');
                },
                unhighlight: function (element) {
                    $(element).removeClass('error');
                }
            });
        });
    </script>

    @include('partials.unsaved-changes-guard', [
        'formSelector' => '#hubUserForm',
        'fallbackUrl' => route('hub.show', $hub->id) . '#hub-users',
    ])
@endsection
