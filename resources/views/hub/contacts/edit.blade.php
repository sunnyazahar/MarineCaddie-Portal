@extends('layouts.app')

@section('styles')
    @include('hub.partials.hub-contact-form-styles')
@endsection

@section('content')
    <script>document.body.classList.add('hub-contact-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="hub-contact-page">
        <div class="hub-contact-hero">
            <div class="hub-contact-hero-main">
                <span class="hub-contact-hero-icon" aria-hidden="true">
                    <i class="ti-id-badge"></i>
                </span>
                <div>
                    <p class="hub-contact-kicker">Hub contact</p>
                    <h1 class="hub-contact-title">Edit contact</h1>
                    <p class="hub-contact-sub">
                        Update <strong>{{ $contact->name }}</strong> for <strong>{{ $hub->hub_name }}</strong>.
                    </p>
                </div>
            </div>
            <a href="{{ route('hub.show', $hub->id) }}#contacts" class="hub-contact-back">
                <i class="ti-arrow-left"></i> Back to hub
            </a>
        </div>

        <div class="hub-contact-meta">
            @if ($hub->code)
                <span class="hub-contact-meta-pill">Code <strong>{{ $hub->code }}</strong></span>
            @endif
            @if ($hub->city || $hub->country)
                <span class="hub-contact-meta-pill">
                    Location
                    <strong>{{ trim(($hub->city ? $hub->city . ', ' : '') . ($hub->country ?? '')) }}</strong>
                </span>
            @endif
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show hub-contact-form-alert" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif

        <div class="hub-contact-card">
            <form action="{{ route('hub.contacts.update', [$hub->id, $contact->id]) }}" method="POST" id="hubContactForm" class="hub-contact-form">
                @csrf
                @method('PUT')

                <div class="hub-contact-form-container">
                    <div class="hub-contact-pillar">
                        <div class="hub-contact-pillar__title">Contact details</div>

                        <div class="hub-contact-fields">
                            <div class="form-group-custom">
                                <label class="form-label-custom" for="hub_contact_name">Name <span class="text-danger">*</span></label>
                                <input type="text" id="hub_contact_name" name="name" class="form-control-custom"
                                    value="{{ old('name', $contact->name) }}" required autocomplete="name">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="hub_contact_email">Email</label>
                                <input type="email" id="hub_contact_email" name="email" class="form-control-custom"
                                    value="{{ old('email', $contact->email) }}" autocomplete="email">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="hub_contact_phone">Phone number (with country code)</label>
                                <input type="text" id="hub_contact_phone" name="phone_number" class="form-control-custom"
                                    value="{{ old('phone_number', $contact->phone_number) }}" autocomplete="tel">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="hub_contact_description">Description</label>
                                <textarea id="hub_contact_description" name="description" class="form-control-custom" rows="4">{{ old('description', $contact->description) }}</textarea>
                            </div>

                            <div class="hub-contact-checkbox">
                                <input type="checkbox" name="is_main_contact" id="is_main_contact" value="1"
                                    {{ old('is_main_contact', $contact->is_main_contact) ? 'checked' : '' }}>
                                <label for="is_main_contact">Is main contact</label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="hub-contact-footer">
            <button type="submit" class="btn-save-custom" form="hubContactForm">Save changes</button>
            <a href="{{ route('hub.show', $hub->id) }}#contacts" class="btn-cancel-custom">Cancel</a>
            <div class="audit-info">
                @include('partials.audit-info', ['record' => $contact])
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function () {
            $('body').addClass('hub-contact-page');

            $('#hubContactForm').validate({
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
        'formSelector' => '#hubContactForm',
        'fallbackUrl' => route('hub.show', $hub->id) . '#contacts',
    ])
@endsection
