@extends('layouts.app')

@section('styles')
    @include('customers.partials.customer-contact-form-styles')
@endsection

@section('content')
    <script>document.body.classList.add('customer-contact-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="customer-contact-page">
        <div class="customer-contact-hero">
            <div class="customer-contact-hero-main">
                <span class="customer-contact-hero-icon" aria-hidden="true">
                    <i class="ti-id-badge"></i>
                </span>
                <div>
                    <p class="customer-contact-kicker">Customer contact</p>
                    <h1 class="customer-contact-title">Add contact</h1>
                    <p class="customer-contact-sub">
                        Add a contact person for <strong>{{ $customer->customer_name }}</strong>.
                    </p>
                </div>
            </div>
            <a href="{{ route('customers.edit', $customer->id) }}#contacts" class="customer-contact-back">
                <i class="ti-arrow-left"></i> Back to customer
            </a>
        </div>

        <div class="customer-contact-meta">
            @if ($customer->customer_number)
                <span class="customer-contact-meta-pill">FM Number <strong>{{ $customer->customer_number }}</strong></span>
            @endif
            @if ($customer->primaryAddress?->city)
                <span class="customer-contact-meta-pill">
                    Location
                    <strong>{{ $customer->primaryAddress->city }}</strong>
                </span>
            @endif
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show customer-contact-form-alert" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif

        <div class="customer-contact-card">
            <form action="{{ route('contacts.store', $customer->id) }}" method="POST" id="customerContactForm" class="customer-contact-form">
                @csrf

                <div class="customer-contact-form-container">
                    <div class="customer-contact-pillar">
                        <div class="customer-contact-pillar__title">Contact details</div>

                        <div class="customer-contact-fields">
                            <div class="form-group-custom">
                                <label class="form-label-custom" for="customer_contact_name">Name <span class="text-danger">*</span></label>
                                <input type="text" id="customer_contact_name" name="name" class="form-control-custom"
                                    value="{{ old('name') }}" required autocomplete="name">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="customer_contact_email">Email <span class="text-danger">*</span></label>
                                <input type="email" id="customer_contact_email" name="email" class="form-control-custom"
                                    value="{{ old('email') }}" required autocomplete="email">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="customer_contact_phone">Phone number (with country code)</label>
                                <input type="text" id="customer_contact_phone" name="phone_number" class="form-control-custom"
                                    value="{{ old('phone_number') }}" autocomplete="tel">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="customer_contact_description">Description</label>
                                <textarea id="customer_contact_description" name="description" class="form-control-custom" rows="4">{{ old('description') }}</textarea>
                            </div>

                            <div class="customer-contact-checkbox">
                                <input type="checkbox" name="is_main_contact" id="is_main_contact" value="1"
                                    {{ old('is_main_contact') ? 'checked' : '' }}>
                                <label for="is_main_contact">Is main contact</label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="customer-contact-footer">
            <button type="submit" class="btn-save-custom" form="customerContactForm">Save contact</button>
            <a href="{{ route('customers.edit', $customer->id) }}#contacts" class="btn-cancel-custom">Cancel</a>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function () {
            $('body').addClass('customer-contact-page');

            $('#customerContactForm').validate({
                rules: {
                    name: { required: true, minlength: 2 },
                    email: { required: true, email: true }
                },
                messages: {
                    name: {
                        required: 'Please enter contact name',
                        minlength: 'Name must be at least 2 characters'
                    },
                    email: {
                        required: 'Please enter contact email',
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
        'formSelector' => '#customerContactForm',
        'fallbackUrl' => route('customers.edit', $customer->id) . '#contacts',
    ])
@endsection
