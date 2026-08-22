@extends('layouts.app')

@section('styles')
    @include('Agents.partials.agent-contact-form-styles')
@endsection

@section('content')
    <script>document.body.classList.add('agent-contact-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="agent-contact-page">
        <div class="agent-contact-hero">
            <div class="agent-contact-hero-main">
                <span class="agent-contact-hero-icon" aria-hidden="true">
                    <i class="ti-id-badge"></i>
                </span>
                <div>
                    <p class="agent-contact-kicker">Agent contact</p>
                    <h1 class="agent-contact-title">Add contact</h1>
                    <p class="agent-contact-sub">
                        Add a contact person for <strong>{{ $agent->agent_name }}</strong>.
                    </p>
                </div>
            </div>
            <a href="{{ route('agents.edit', $agent->id) }}#contacts" class="agent-contact-back">
                <i class="ti-arrow-left"></i> Back to agent
            </a>
        </div>

        <div class="agent-contact-meta">
            @if ($agent->code)
                <span class="agent-contact-meta-pill">Code <strong>{{ $agent->code }}</strong></span>
            @endif
            @if ($agent->city || $agent->country)
                <span class="agent-contact-meta-pill">
                    Location
                    <strong>{{ trim(($agent->city ? $agent->city . ', ' : '') . ($agent->country?->name ?? '')) }}</strong>
                </span>
            @endif
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show agent-contact-form-alert" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif

        <div class="agent-contact-card">
            <form action="{{ route('agents.contacts.store', $agent->id) }}" method="POST" id="agentContactForm" class="agent-contact-form">
                @csrf

                <div class="agent-contact-form-container">
                    <div class="agent-contact-pillar">
                        <div class="agent-contact-pillar__title">Contact details</div>

                        <div class="agent-contact-fields">
                            <div class="form-group-custom">
                                <label class="form-label-custom" for="agent_contact_name">Name <span class="text-danger">*</span></label>
                                <input type="text" id="agent_contact_name" name="name" class="form-control-custom"
                                    value="{{ old('name') }}" required autocomplete="name">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="agent_contact_email">Email <span class="text-danger">*</span></label>
                                <input type="email" id="agent_contact_email" name="email" class="form-control-custom"
                                    value="{{ old('email') }}" required autocomplete="email">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="agent_contact_phone">Phone number (with country code)</label>
                                <input type="text" id="agent_contact_phone" name="phone_number" class="form-control-custom"
                                    value="{{ old('phone_number') }}" autocomplete="tel">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="agent_contact_description">Description</label>
                                <textarea id="agent_contact_description" name="description" class="form-control-custom" rows="4">{{ old('description') }}</textarea>
                            </div>

                            <div class="agent-contact-checkbox">
                                <input type="checkbox" name="is_main_contact" id="is_main_contact" value="1"
                                    {{ old('is_main_contact') ? 'checked' : '' }}>
                                <label for="is_main_contact">Is main contact</label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="agent-contact-footer">
            <button type="submit" class="btn-save-custom" form="agentContactForm">Save contact</button>
            <a href="{{ route('agents.edit', $agent->id) }}#contacts" class="btn-cancel-custom">Cancel</a>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function () {
            $('body').addClass('agent-contact-page');

            $('#agentContactForm').validate({
                rules: {
                    name: { required: true, minlength: 2 },
                    email: { required: true, email: true }
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
        'formSelector' => '#agentContactForm',
        'fallbackUrl' => route('agents.edit', $agent->id) . '#contacts',
    ])
@endsection
