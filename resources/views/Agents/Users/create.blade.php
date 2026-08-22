@extends('layouts.app')

@section('styles')
    @include('Agents.partials.agent-user-form-styles')
@endsection

@section('content')
    <script>document.body.classList.add('agent-user-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="agent-user-page">
        <div class="agent-user-hero">
            <div class="agent-user-hero-main">
                <span class="agent-user-hero-icon" aria-hidden="true">
                    <i class="ti-user"></i>
                </span>
                <div>
                    <p class="agent-user-kicker">Agent user</p>
                    <h1 class="agent-user-title">Add agent user</h1>
                    <p class="agent-user-sub">
                        Create a user for <strong>{{ $agent->agent_name }}</strong>.
                    </p>
                </div>
            </div>
            <a href="{{ route('agents.edit', $agent->id) }}#agent-users" class="agent-user-back">
                <i class="ti-arrow-left"></i> Back to agent
            </a>
        </div>

        <div class="agent-user-meta">
            @if ($agent->code)
                <span class="agent-user-meta-pill">Code <strong>{{ $agent->code }}</strong></span>
            @endif
            @if ($agent->city || $agent->country)
                <span class="agent-user-meta-pill">
                    Location
                    <strong>{{ trim(($agent->city ? $agent->city . ', ' : '') . ($agent->country?->name ?? '')) }}</strong>
                </span>
            @endif
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show agent-user-form-alert" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif

        <div class="agent-user-card">
            <form action="{{ route('agents.users.store', $agent->id) }}" method="POST" id="agentUserForm" class="agent-user-form">
                @csrf

                <div class="agent-user-form-container">
                    <div class="agent-user-pillar">
                        <div class="agent-user-pillar__title">User details</div>

                        <div class="agent-user-fields">
                            <div class="form-group-custom">
                                <label class="form-label-custom" for="agent_user_name">Name <span class="text-danger">*</span></label>
                                <input type="text" id="agent_user_name" name="name" class="form-control-custom"
                                    value="{{ old('name') }}" required autocomplete="name">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="agent_user_email">Email <span class="text-danger">*</span></label>
                                <input type="email" id="agent_user_email" name="email" class="form-control-custom"
                                    value="{{ old('email') }}" required autocomplete="email">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="agent_user_phone">Phone number (with country code)</label>
                                <input type="text" id="agent_user_phone" name="phone_number" class="form-control-custom"
                                    value="{{ old('phone_number') }}" autocomplete="tel">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="agent_user_description">Description</label>
                                <textarea id="agent_user_description" name="description" class="form-control-custom" rows="4">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="agent-user-footer">
            <button type="submit" class="btn-save-custom" form="agentUserForm">Save user</button>
            <a href="{{ route('agents.edit', $agent->id) }}#agent-users" class="btn-cancel-custom">Cancel</a>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function () {
            $('body').addClass('agent-user-page');

            $('#agentUserForm').validate({
                rules: {
                    name: { required: true, minlength: 2 },
                    email: { required: true, email: true }
                },
                messages: {
                    name: {
                        required: 'Please enter the user name',
                        minlength: 'Name must be at least 2 characters'
                    },
                    email: {
                        required: 'Please enter the user email',
                        email: 'Please enter a valid email address'
                    }
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
        'formSelector' => '#agentUserForm',
        'fallbackUrl' => route('agents.edit', $agent->id) . '#agent-users',
    ])
@endsection
