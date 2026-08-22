@extends('layouts.app')

@section('styles')
    @include('Other Companies.partials.oc-contact-form-styles')
@endsection

@section('content')
    <script>document.body.classList.add('oc-contact-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="oc-contact-page">
        <div class="oc-contact-hero">
            <div class="oc-contact-hero-main">
                <span class="oc-contact-hero-icon" aria-hidden="true">
                    <i class="ti-id-badge"></i>
                </span>
                <div>
                    <p class="oc-contact-kicker">Other company contact</p>
                    <h1 class="oc-contact-title">Edit contact</h1>
                    <p class="oc-contact-sub">
                        Update <strong>{{ $contact->name }}</strong> for <strong>{{ $otherCompany->company_name }}</strong>.
                    </p>
                </div>
            </div>
            <a href="{{ route('other-companies.edit', $otherCompany->id) }}#contacts" class="oc-contact-back">
                <i class="ti-arrow-left"></i> Back to company
            </a>
        </div>

        <div class="oc-contact-meta">
            @if ($otherCompany->code)
                <span class="oc-contact-meta-pill">Code <strong>{{ $otherCompany->code }}</strong></span>
            @endif
            @if ($otherCompany->city || $otherCompany->country)
                <span class="oc-contact-meta-pill">
                    Location
                    <strong>{{ trim(($otherCompany->city ? $otherCompany->city . ', ' : '') . ($otherCompany->country?->name ?? '')) }}</strong>
                </span>
            @endif
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show oc-contact-form-alert" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif

        <div class="oc-contact-card">
            <form action="{{ route('other-companies.contacts.update', [$otherCompany->id, $contact->id]) }}" method="POST" id="ocContactForm" class="oc-contact-form">
                @csrf
                @method('PUT')

                <div class="oc-contact-form-container">
                    <div class="oc-contact-pillar">
                        <div class="oc-contact-pillar__title">Contact details</div>

                        <div class="oc-contact-fields">
                            <div class="form-group-custom">
                                <label class="form-label-custom" for="oc_contact_name">Name <span class="text-danger">*</span></label>
                                <input type="text" id="oc_contact_name" name="name" class="form-control-custom"
                                    value="{{ old('name', $contact->name) }}" required autocomplete="name">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="oc_contact_email">Email</label>
                                <input type="email" id="oc_contact_email" name="email" class="form-control-custom"
                                    value="{{ old('email', $contact->email) }}" autocomplete="email">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="oc_contact_phone">Phone number (with country code)</label>
                                <input type="text" id="oc_contact_phone" name="phone_number" class="form-control-custom"
                                    value="{{ old('phone_number', $contact->phone_number) }}" autocomplete="tel">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="oc_contact_description">Description</label>
                                <textarea id="oc_contact_description" name="description" class="form-control-custom" rows="4">{{ old('description', $contact->description) }}</textarea>
                            </div>

                            <div class="oc-contact-checkbox">
                                <input type="checkbox" name="is_main_contact" id="is_main_contact" value="1"
                                    {{ old('is_main_contact', $contact->is_main_contact) ? 'checked' : '' }}>
                                <label for="is_main_contact">Is main contact</label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="oc-contact-footer">
            <button type="submit" class="btn-save-custom" form="ocContactForm">Save changes</button>
            <a href="{{ route('other-companies.edit', $otherCompany->id) }}#contacts" class="btn-cancel-custom">Cancel</a>
            <div class="audit-info">
                @include('partials.audit-info', ['record' => $contact])
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function () {
            $('body').addClass('oc-contact-page');

            $('#ocContactForm').validate({
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
        'formSelector' => '#ocContactForm',
        'fallbackUrl' => route('other-companies.edit', $otherCompany->id) . '#contacts',
    ])
@endsection
