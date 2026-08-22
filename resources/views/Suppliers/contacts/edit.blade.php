@extends('layouts.app')

@section('styles')
    @include('Suppliers.partials.supplier-contact-form-styles')
@endsection

@section('content')
    <script>document.body.classList.add('supplier-contact-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="supplier-contact-page">
        <div class="supplier-contact-hero">
            <div class="supplier-contact-hero-main">
                <span class="supplier-contact-hero-icon" aria-hidden="true">
                    <i class="ti-id-badge"></i>
                </span>
                <div>
                    <p class="supplier-contact-kicker">Supplier contact</p>
                    <h1 class="supplier-contact-title">Edit contact</h1>
                    <p class="supplier-contact-sub">
                        Update <strong>{{ $contact->name }}</strong> for <strong>{{ $supplier->supplier_name }}</strong>.
                    </p>
                </div>
            </div>
            <a href="{{ route('suppliers.edit', $supplier->id) }}#contacts" class="supplier-contact-back">
                <i class="ti-arrow-left"></i> Back to supplier
            </a>
        </div>

        <div class="supplier-contact-meta">
            @if ($supplier->un_locode)
                <span class="supplier-contact-meta-pill">UN/LOCODE <strong>{{ $supplier->un_locode }}</strong></span>
            @endif
            @if ($supplier->city || $supplier->country)
                <span class="supplier-contact-meta-pill">
                    Location
                    <strong>{{ trim(($supplier->city ? $supplier->city . ', ' : '') . ($supplier->country?->name ?? '')) }}</strong>
                </span>
            @endif
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show supplier-contact-form-alert" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif

        <div class="supplier-contact-card">
            <form action="{{ route('suppliers.contacts.update', [$supplier->id, $contact->id]) }}" method="POST" id="supplierContactForm" class="supplier-contact-form">
                @csrf
                @method('PUT')

                <div class="supplier-contact-form-container">
                    <div class="supplier-contact-pillar">
                        <div class="supplier-contact-pillar__title">Contact details</div>

                        <div class="supplier-contact-fields">
                            <div class="form-group-custom">
                                <label class="form-label-custom" for="supplier_contact_name">Name <span class="text-danger">*</span></label>
                                <input type="text" id="supplier_contact_name" name="name" class="form-control-custom"
                                    value="{{ old('name', $contact->name) }}" required autocomplete="name">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="supplier_contact_email">Email</label>
                                <input type="email" id="supplier_contact_email" name="email" class="form-control-custom"
                                    value="{{ old('email', $contact->email) }}" autocomplete="email">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="supplier_contact_phone">Phone number (with country code)</label>
                                <input type="text" id="supplier_contact_phone" name="phone_number" class="form-control-custom"
                                    value="{{ old('phone_number', $contact->phone_number) }}" autocomplete="tel">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="supplier_contact_description">Description</label>
                                <textarea id="supplier_contact_description" name="description" class="form-control-custom" rows="4">{{ old('description', $contact->description) }}</textarea>
                            </div>

                            <div class="supplier-contact-checkbox">
                                <input type="checkbox" name="is_main_contact" id="is_main_contact" value="1"
                                    {{ old('is_main_contact', $contact->is_main_contact) ? 'checked' : '' }}>
                                <label for="is_main_contact">Is main contact</label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="supplier-contact-footer">
            <button type="submit" class="btn-save-custom" form="supplierContactForm">Save changes</button>
            <a href="{{ route('suppliers.edit', $supplier->id) }}#contacts" class="btn-cancel-custom">Cancel</a>
            <div class="audit-info">
                @include('partials.audit-info', ['record' => $contact])
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function () {
            $('body').addClass('supplier-contact-page');

            $('#supplierContactForm').validate({
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
        'formSelector' => '#supplierContactForm',
        'fallbackUrl' => route('suppliers.edit', $supplier->id) . '#contacts',
    ])
@endsection
