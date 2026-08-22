@extends('layouts.app')

@section('styles')
    @include('hub.partials.show-page-styles')
@endsection

@section('content')
    <script>document.body.classList.add('edit-hub-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="edit-hub-page">
        <div class="edit-hub-hero">
            <div class="edit-hub-hero-main">
                <span class="edit-hub-hero-icon" aria-hidden="true">
                    <i class="ti-location-pin"></i>
                </span>
                <div>
                    <p class="edit-hub-kicker">Administration</p>
                    <h1 class="edit-hub-title">{{ $hub->hub_name }}</h1>
                    <p class="edit-hub-sub">Edit hub details, billing, documents, users, and scan gun settings.</p>
                </div>
            </div>
            <a href="{{ route('hub.index') }}" class="edit-hub-back">
                <i class="ti-arrow-left"></i> Back to hubs
            </a>
        </div>

        <div class="edit-hub-meta">
            <span class="edit-hub-meta-pill">Hub ID <strong>{{ $hub->id }}</strong></span>
            @if ($hub->code)
                <span class="edit-hub-meta-pill">Code <strong>{{ $hub->code }}</strong></span>
            @endif
            @if ($hub->city || $hub->country)
                <span class="edit-hub-meta-pill">
                    Location
                    <strong>{{ trim(($hub->city ? $hub->city . ', ' : '') . ($hub->country ?? '')) }}</strong>
                </span>
            @endif
            @if ($hub->hide_in_portal)
                <span class="edit-hub-meta-pill is-hidden">Portal <strong>Hidden</strong></span>
            @else
                <span class="edit-hub-meta-pill is-active">Portal <strong>Visible</strong></span>
            @endif
        </div>

        <div class="tabs-container">
            <a class="tab-item active" data-tab="hub-details"><i class="ti-info-alt"></i> Hub Details</a>
            <a class="tab-item" data-tab="billing-details"><i class="ti-receipt"></i> Billing Details</a>
            <a class="tab-item" data-tab="sop"><i class="ti-files"></i> SOP</a>
            <a class="tab-item" data-tab="pricing"><i class="ti-money"></i> Pricing</a>
            <a class="tab-item" data-tab="hub-users"><i class="ti-user"></i> Hub Users</a>
            <a class="tab-item" data-tab="contacts"><i class="ti-id-badge"></i> Contacts</a>
            <a class="tab-item" data-tab="email-settings"><i class="ti-email"></i> Email Settings</a>
            <a class="tab-item" data-tab="scan-gun"><i class="ti-hand-point-right"></i> Scan Gun</a>
        </div>

        <div class="edit-hub-card">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show edit-hub-alert" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show edit-hub-alert" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif

                                                            <div class="tab-content-container">
                                                            <form id="hubEditForm" action="{{ route('hub.update', $hub->id) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="active_tab" id="active_tab" value="{{ old('active_tab', 'hub-details') }}">
                                                                 <!-- Hub Details Tab -->
                                                                 <div id="hub-details" class="tab-content-custom active">
                                                                    <div class="form-pillar-container">
                                                                        <!-- Pillar 1: Hub information -->
                                                                        <div class="form-pillar">
                                                                            <div class="form-section-header">Hub information</div>
                                                                            
                                                                            <div class="form-group-custom">
                                                                                <label class="form-label-custom">Hub name</label>
                                                                                <div class="input-group-custom">
                                                                                    <input type="text" name="hub_name" class="form-input-custom" value="{{ $hub->hub_name }}">
                                                                                    <button type="button" class="btn-input-append"><i class="ti-more-alt"></i></button>
                                                                                </div>
                                                                            </div>

                                                                            <div class="form-group-custom d-none">
                                                                                <label class="form-label-custom">Company id</label>
                                                                                <input type="text" name="company_id" class="form-input-custom form-input-readonly" value="{{ $hub->company_id }}" readonly>
                                                                            </div>

                                                                            <div class="form-group-custom d-none">
                                                                                <label class="form-label-custom">Customer number from FM</label>
                                                                                <input type="text" name="customer_number_fm" class="form-input-custom" value="{{ $hub->customer_number_fm }}">
                                                                            </div>

                                                                            <div class="input-row">
                                                                                <div class="form-group-custom">
                                                                                    <label class="form-label-custom">Code</label>
                                                                                    <input type="text" name="code" class="form-input-custom" value="{{ $hub->code }}">
                                                                                </div>
                                                                                <div class="form-group-custom">
                                                                                    <label class="form-label-custom">Code description</label>
                                                                                    <input type="text" name="code_description" class="form-input-custom" value="{{ $hub->code_description }}">
                                                                                </div>
                                                                            </div>

                                                                            <div class="form-group-custom">
                                                                                <label class="form-label-custom">Phone number (with country code)</label>
                                                                                <input type="text" name="phone_number" class="form-input-custom" value="{{ $hub->phone_number }}">
                                                                            </div>

                                                                            <div class="form-group-custom">
                                                                                <label class="form-label-custom">Email</label>
                                                                                <input type="text" name="email" class="form-input-custom" value="{{ $hub->email }}"
                                                                                    placeholder="email@example.com; email2@example.com">
                                                                            </div>

                                                                            <div class="form-group-custom d-none" style="flex-direction: row; gap: 8px; align-items: center; margin-top: 5px;">
                                                                                <input type="checkbox" name="is_gts_company" id="is_gts_company" value="1" {{ $hub->is_gts_company ? 'checked' : '' }}>
                                                                                <label class="form-label-custom" for="is_gts_company">This hub is part of GTS company</label>
                                                                            </div>

                                                                            <div class="form-group-custom">
                                                                                <label class="form-label-custom">Remarks</label>
                                                                                <textarea name="remarks" class="form-textarea-custom" rows="3">{{ $hub->remarks }}</textarea>
                                                                            </div>

                                                                            <div class="form-group-custom">
                                                                                <label class="form-label-custom">Special considerations for destination</label>
                                                                                <textarea name="special_considerations" class="form-textarea-custom" rows="3">{{ $hub->special_considerations }}</textarea>
                                                                            </div>

                                                                            <div class="checkbox-group">
                                                                                <input type="checkbox" class="checkbox-custom" name="show_pre_alert" id="show_pre_alert" value="1" {{ $hub->show_pre_alert ? 'checked' : '' }}>
                                                                                <label class="checkbox-label" for="show_pre_alert">Show pre-alert warning when items in shipment are not scanned</label>
                                                                            </div>
                                                                            <div class="form-group-custom">
                                                                                <label class="form-label-custom">Contact Person <span class="text-danger">*</span></label>
                                                                                <input type="text" name="contact_person" class="form-input-custom" value="{{ old('contact_person', $hub->contact_person) }}" required>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Pillar 2: Hub address -->
                                                                        <div class="form-pillar">
                                                                            <div class="form-section-header">Hub address</div>
                                                                            
                                                                            <div class="form-group-custom">
                                                                                <label class="form-label-custom">Hub address</label>
                                                                                <textarea name="hub_address" class="form-textarea-custom" rows="3">{{ $hub->hub_address }}</textarea>
                                                                            </div>

                                                                            <div class="input-row">
                                                                                <div class="form-group-custom" style="flex: 2;">
                                                                                    <label class="form-label-custom">City</label>
                                                                                    <input type="text" name="city" class="form-input-custom" value="{{ $hub->city }}">
                                                                                </div>
                                                                                <div class="form-group-custom">
                                                                                    <label class="form-label-custom">District/state</label>
                                                                                    <input type="text" name="district_state" class="form-input-custom" value="{{ $hub->district_state }}">
                                                                                </div>
                                                                                <div class="form-group-custom">
                                                                                    <label class="form-label-custom">Zip code</label>
                                                                                    <input type="text" name="zip_code" class="form-input-custom" value="{{ $hub->zip_code }}">
                                                                                </div>
                                                                            </div>

                                                                            <x-forms.country-select
                                                                                name="country"
                                                                                label="Country"
                                                                                :countries="$countries"
                                                                                valueKey="name"
                                                                                :value="$hub->country"
                                                                                class="form-select-custom select2-flag"
                                                                                :allowClear="true"
                                                                            />

                                                                            <x-forms.port-select
                                                                                name="port_code"
                                                                                label="Port code"
                                                                                :value="old('port_code', $hub->port_code)"
                                                                            />

                                                                            <div class="form-section-header" style="margin-top: 15px;">Office address (Optional)</div>

                                                                            <div class="form-group-custom">
                                                                                <label class="form-label-custom">Office address</label>
                                                                                <textarea name="office_address" class="form-textarea-custom" rows="3">{{ $hub->office_address }}</textarea>
                                                                            </div>

                                                                            <div class="input-row">
                                                                                <div class="form-group-custom" style="flex: 2;">
                                                                                    <label class="form-label-custom">City</label>
                                                                                    <input type="text" name="office_city" class="form-input-custom" value="{{ $hub->office_city }}">
                                                                                </div>
                                                                                <div class="form-group-custom">
                                                                                    <label class="form-label-custom">District/state</label>
                                                                                    <input type="text" name="office_district_state" class="form-input-custom" value="{{ $hub->office_district_state }}">
                                                                                </div>
                                                                                <div class="form-group-custom">
                                                                                    <label class="form-label-custom">Zip code</label>
                                                                                    <input type="text" name="office_zip_code" class="form-input-custom" value="{{ $hub->office_zip_code }}">
                                                                                </div>
                                                                            </div>

                                                                            <x-forms.country-select
                                                                                name="office_country"
                                                                                label="Country"
                                                                                :countries="$countries"
                                                                                valueKey="name"
                                                                                :value="$hub->office_country"
                                                                                class="form-select-custom select2-flag"
                                                                                :allowClear="true"
                                                                            />
                                                                        </div>

                                                                        <!-- Pillar 3: Hub details & portal -->
                                                                        <div class="form-pillar">
                                                                            <div class="form-section-header">Hub details</div>
                                                                            
                                                                            <div class="form-group-custom">
                                                                                <label class="form-label-custom">EORI number</label>
                                                                                <input type="text" name="eori_number" class="form-input-custom" value="{{ $hub->eori_number }}">
                                                                            </div>

                                                                            <div class="form-group-custom">
                                                                                <label class="form-label-custom">UN/LOCODE</label>
                                                                                <input type="text" name="un_locode" class="form-input-custom" style="width: 50%;" value="{{ $hub->un_locode }}">
                                                                            </div>

                                                                            <div class="form-section-header" style="margin-top: 25px;">Customer portal</div>

                                                                            <div class="checkbox-group">
                                                                                <input type="checkbox" class="checkbox-custom" name="hide_in_portal" id="hide_in_portal" value="1" {{ $hub->hide_in_portal ? 'checked' : '' }}>
                                                                                <label class="checkbox-label" for="hide_in_portal">Do not show this hub in Customer portal</label>
                                                                            </div>

                                                                            <div class="form-group-custom">
                                                                                <label class="form-label-custom">Remarks for the customer portal</label>
                                                                                <textarea name="portal_remarks" class="form-textarea-custom" rows="3">{{ $hub->portal_remarks }}</textarea>
                                                                            </div>

                                                                            <div class="form-group-custom">
                                                                                <label class="form-label-custom">Email for Customer Portal</label>
                                                                                <input type="text" name="portal_email" class="form-input-custom" value="{{ $hub->portal_email }}"
                                                                                    placeholder="email@example.com; email2@example.com">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                 </div>

                                                                 <!-- Billing Details Tab -->
                                                                 <div id="billing-details" class="tab-content-custom">
                                                                     <div class="form-pillar-container" style="grid-template-columns: 1.2fr 1.2fr; gap: 60px;">
                                                                         <!-- Column 1: Invoicing details -->
                                                                         <div class="form-pillar">
                                                                             <div class="form-section-header">Invoicing details</div>
                                                                             
                                                                             <div class="form-group-custom">
                                                                                 <label class="form-label-custom">Invoicing name</label>
                                                                                 <input type="text" name="invoicing_name" class="form-input-custom" value="{{ $hub->invoicing_name }}">
                                                                             </div>

                                                                             <div class="form-group-custom">
                                                                                 <label class="form-label-custom">Address</label>
                                                                                 <textarea name="invoicing_address" class="form-textarea-custom" rows="3">{{ $hub->invoicing_address }}</textarea>
                                                                             </div>

                                                                             <div class="input-row">
                                                                                 <div class="form-group-custom" style="flex: 2;">
                                                                                     <label class="form-label-custom">City</label>
                                                                                     <div class="input-group-custom">
                                                                                         <input type="text" name="invoicing_city" class="form-input-custom has-append" value="{{ $hub->invoicing_city }}">
                                                                                         <button type="button" class="btn-input-append"><i class="ti-more-alt"></i></button>
                                                                                     </div>
                                                                                 </div>
                                                                                 <div class="form-group-custom">
                                                                                     <label class="form-label-custom">District</label>
                                                                                     <input type="text" name="invoicing_district" class="form-input-custom" value="{{ $hub->invoicing_district }}">
                                                                                 </div>
                                                                                 <div class="form-group-custom">
                                                                                     <label class="form-label-custom">Zip</label>
                                                                                     <input type="text" name="invoicing_zip" class="form-input-custom" value="{{ $hub->invoicing_zip }}">
                                                                                 </div>
                                                                             </div>

                                                                             <x-forms.country-select
                                                                                 name="billing_country"
                                                                                 label="Country"
                                                                                 :countries="$countries"
                                                                                 valueKey="name"
                                                                                 :value="$hub->billing_country"
                                                                                 class="form-select-custom select2-flag"
                                                                                 :allowClear="true"
                                                                             />

                                                                             <div class="form-group-custom">
                                                                                 <label class="form-label-custom">E-mails for invoicing</label>
                                                                                 <input type="text" name="emails_for_invoicing" class="form-input-custom" value="{{ $hub->emails_for_invoicing }}">
                                                                             </div>

                                                                             <div class="form-group-custom">
                                                                                 <label class="form-label-custom">E-mails for invoicing (CC)</label>
                                                                                 <input type="text" name="emails_for_invoicing_cc" class="form-input-custom" value="{{ $hub->emails_for_invoicing_cc }}">
                                                                             </div>

                                                                             <div class="input-row">
                                                                                 <div class="form-group-custom">
                                                                                     <label class="form-label-custom">VAT number</label>
                                                                                     <input type="text" name="vat_number" class="form-input-custom" value="{{ $hub->vat_number }}">
                                                                                 </div>
                                                                                 <div class="form-group-custom">
                                                                                     <label class="form-label-custom">Invoicing frequency</label>
                                                                                     <select name="invoicing_frequency" class="form-select-custom select2-single">
                                                                                         <option value="Daily" {{ $hub->invoicing_frequency == 'Daily' ? 'selected' : '' }}>Daily</option>
                                                                                         <option value="Weekly" {{ ($hub->invoicing_frequency ?? 'Weekly') == 'Weekly' ? 'selected' : '' }}>Weekly</option>
                                                                                         <option value="Bi-weekly" {{ $hub->invoicing_frequency == 'Bi-weekly' ? 'selected' : '' }}>Bi-weekly</option>
                                                                                         <option value="Monthly" {{ $hub->invoicing_frequency == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                                                                         <option value="Per Shipment" {{ $hub->invoicing_frequency == 'Per Shipment' ? 'selected' : '' }}>Per Shipment</option>
                                                                                     </select>
                                                                                 </div>
                                                                             </div>

                                                                             <div class="input-row rebate-row">
                                                                                 <div class="checkbox-group">
                                                                                     <input type="checkbox" class="checkbox-custom" id="applies_to_rebate" {{ $hub->rebate_percentage > 0 ? 'checked' : '' }}>
                                                                                     <label class="checkbox-label" for="applies_to_rebate">Applies to rebate</label>
                                                                                 </div>
                                                                                 <div class="form-group-custom">
                                                                                     <label class="form-label-custom">Rebate percentage</label>
                                                                                     <input type="text" name="rebate_percentage" class="form-input-custom" value="{{ $hub->rebate_percentage }}">
                                                                                 </div>
                                                                             </div>
                                                                         </div>

                                                                         <!-- Column 2: Other billing sections -->
                                                                         <div class="form-pillar">
                                                                             <div class="form-section-header">Outgoing invoices to hub</div>
                                                                             <div class="input-row">
                                                                                 <div class="form-group-custom">
                                                                                     <label class="form-label-custom">Currency</label>
                                                                                     <select name="billing_currency_outgoing" class="form-select-custom select2-single">
                                                                                         @foreach(['USD', 'SGD', 'EUR', 'GBP', 'AUD', 'CNY'] as $curr)
                                                                                             <option value="{{ $curr }}" {{ ($hub->billing_currency_outgoing ?? 'SGD') == $curr ? 'selected' : '' }}>{{ $curr }}</option>
                                                                                         @endforeach
                                                                                     </select>
                                                                                 </div>
                                                                                 <div class="form-group-custom">
                                                                                     <label class="form-label-custom">Payment terms</label>
                                                                                     <input type="text" name="payment_terms_outgoing" class="form-input-custom" value="{{ $hub->payment_terms_outgoing }}">
                                                                                 </div>
                                                                             </div>

                                                                             <div class="form-section-header" style="margin-top: 25px;">Incoming invoices from hub</div>
                                                                             <div class="input-row">
                                                                                 <div class="form-group-custom">
                                                                                     <label class="form-label-custom">Currency</label>
                                                                                     <select name="billing_currency_incoming" class="form-select-custom select2-single">
                                                                                         @foreach(['USD', 'SGD', 'EUR', 'GBP', 'AUD', 'CNY'] as $curr)
                                                                                             <option value="{{ $curr }}" {{ ($hub->billing_currency_incoming ?? 'SGD') == $curr ? 'selected' : '' }}>{{ $curr }}</option>
                                                                                         @endforeach
                                                                                     </select>
                                                                                 </div>
                                                                                 <div class="form-group-custom">
                                                                                     <label class="form-label-custom">Payment terms</label>
                                                                                     <input type="text" name="payment_terms_incoming" class="form-input-custom" value="{{ $hub->payment_terms_incoming ?? '60' }}">
                                                                                 </div>
                                                                             </div>

                                                                             
                                                                         </div>
                                                                     </div>
                                                                 </div>

                                                                 <!-- SOP Tab -->
                                                                 <div id="sop" class="tab-content-custom">
                                                                     <div class="form-pillar-container" style="grid-template-columns: 1fr 1.5fr; gap: 60px;">
                                                                         <!-- Pillar 1: SOP details -->
                                                                         <div class="form-pillar">
                                                                             <div class="form-section-header">SOP details</div>
                                                                             
                                                                             <div class="hub-checkbox-stack">
                                                                                 <div class="checkbox-group">
                                                                                     <input type="checkbox" class="checkbox-custom" name="coc_signed" id="coc_signed" value="1" {{ $hub->coc_signed ? 'checked' : '' }}>
                                                                                     <label class="checkbox-label" for="coc_signed">Code of Conduct signed</label>
                                                                                 </div>
                                                                                 <div class="checkbox-group">
                                                                                     <input type="checkbox" class="checkbox-custom" name="sop_implemented" id="sop_implemented" value="1" {{ $hub->sop_implemented ? 'checked' : '' }}>
                                                                                     <label class="checkbox-label" for="sop_implemented">SOP implemented</label>
                                                                                 </div>
                                                                             </div>

                                                                             <div class="form-group-custom">
                                                                                 <label class="form-label-custom">Code of Conduct signed date</label>
                                                                                 <div class="input-group-custom">
                                                                                     <input type="text" name="coc_signed_date" id="coc_signed_date" class="form-input-custom datepicker-input" value="{{ $hub->coc_signed_date ? \Carbon\Carbon::parse($hub->coc_signed_date)->format('d.m.Y') : '' }}" placeholder="dd.mm.yyyy">
                                                                                     <button type="button" class="btn-input-append calendar-trigger"><i class="ti-calendar"></i></button>
                                                                                 </div>
                                                                             </div>

                                                                             <div class="form-group-custom">
                                                                                 <label class="form-label-custom">Responsible manager</label>
                                                                                 <input type="text" name="responsible_manager" class="form-input-custom" value="{{ $hub->responsible_manager }}">
                                                                             </div>
                                                                         </div>

                                                                          <!-- Pillar 2: Imported documents -->
                                            <div class="form-pillar">
                                                <div class="form-section-header">Imported documents</div>
                                                
                                                <div id="sop_documents_list">
                                                    @foreach($hub->documents as $doc)
                                                        <div class="file-list-item" id="doc_item_{{ $doc->id }}">
                                                            <div class="file-info">
                                                                <span class="file-name"><a href="{{ $doc->fileUrl() }}" target="_blank" style="color: inherit; text-decoration: none;">{{ $doc->file_name }}</a></span>
                                                                <span class="file-meta">Uploaded {{ $doc->created_at->format('d.m.Y H:i') }}</span>
                                                            </div>
                                                            <div class="btn-delete-file delete-doc" data-id="{{ $doc->id }}"><i class="ti-trash"></i></div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <input type="file" id="sop_file_input" style="display: none;" accept=".pdf,.doc,.docx,.jpg,.png">
                                                
                                                <div class="upload-area" id="sop_upload_area">
                                                    <div class="upload-text">Drag files here or click to browse</div>
                                                    <div class="upload-icon"><i class="ti-arrow-up"></i></div>
                                                </div>
                                            </div>
                                       </div>
                                   </div>

                                   <!-- Pricing Tab -->
                                   <div id="pricing" class="tab-content-custom">
                                       <div class="form-pillar-container" style="grid-template-columns: 1fr 1fr 1.2fr; gap: 40px;">
                                           <!-- Pillar 1: Pricing details -->
                                           <div class="form-pillar">
                                               <div class="form-section-header">Pricing details</div>
                                               
                                               <div class="input-grid">
                                                   <div class="checkbox-group">
                                                       <input type="checkbox" class="checkbox-custom" name="agreement_implemented" id="agreement_implemented" value="1" {{ ($hub->agreement_implemented ?? true) ? 'checked' : '' }}>
                                                       <label class="checkbox-label" for="agreement_implemented">Agreement implemented</label>
                                                   </div>

                                                   <div class="form-group-custom">
                                                       <label class="form-label-custom">Agreement type</label>
                                                       <select name="agreement_type" class="form-select-custom select2-single">
                                                           @foreach(['Framework', 'Spot', 'Long-term', 'Trial'] as $type)
                                                               <option value="{{ $type }}" {{ ($hub->agreement_type ?? 'Spot') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                                           @endforeach
                                                       </select>
                                                   </div>

                                                   <div class="form-group-custom">
                                                       <label class="form-label-custom">Agreement starting date</label>
                                                       <div class="input-group-custom">
                                                           <input type="text" name="agreement_start_date" id="agreement_start_date" class="form-input-custom datepicker-input" value="{{ $hub->agreement_start_date ? $hub->agreement_start_date->format('d.m.Y') : '' }}" placeholder="DD.MM.YYYY">
                                                           <button type="button" class="btn-input-append calendar-trigger" data-target="agreement_start_date"><i class="ti-calendar"></i></button>
                                                       </div>
                                                   </div>

                                                   <div class="form-group-custom">
                                                       <label class="form-label-custom">Agreement expiration date</label>
                                                       <div class="input-group-custom">
                                                           <input type="text" name="agreement_expiry_date" id="agreement_expiry_date" class="form-input-custom datepicker-input" value="{{ $hub->agreement_expiry_date ? $hub->agreement_expiry_date->format('d.m.Y') : '' }}" placeholder="DD.MM.YYYY">
                                                           <button type="button" class="btn-input-append calendar-trigger" data-target="agreement_expiry_date"><i class="ti-calendar"></i></button>
                                                       </div>
                                                   </div>
                                               </div>
                                           </div>

                                           <!-- Pillar 2: Storage costs -->
                                           <div class="form-pillar">
                                               <div class="form-section-header">Storage costs</div>
                                               
                                               <div class="input-grid">
                                                   <div class="form-group-custom">
                                                       <label class="form-label-custom">Minimal CBM</label>
                                                       <input type="text" name="minimal_cbm" class="form-input-custom" value="{{ $hub->minimal_cbm }}">
                                                   </div>
                                                   <div class="form-group-custom">
                                                       <label class="form-label-custom">Minimal weight</label>
                                                       <input type="text" name="minimal_weight" class="form-input-custom" value="{{ $hub->minimal_weight }}">
                                                   </div>

                                                   <div class="form-group-custom">
                                                       <label class="form-label-custom">Free storage days</label>
                                                       <input type="text" name="free_storage_days" class="form-input-custom" value="{{ $hub->free_storage_days }}">
                                                   </div>
                                                   <div class="form-group-custom">
                                                       <label class="form-label-custom">CBM charge (USD)</label>
                                                       <input type="text" name="cbm_charge_usd" class="form-input-custom" value="{{ $hub->cbm_charge_usd }}">
                                                   </div>
                                               </div>
                                           </div>

                                           <!-- Pillar 3: Imported documents -->
                                           <div class="form-pillar">
                                               <div class="form-section-header">Imported documents</div>
                                               
                                               <div id="pricing_documents_list">
                                                   @foreach($hub->pricingDocuments as $doc)
                                                       <div class="file-list-item" id="pricing_doc_item_{{ $doc->id }}">
                                                           <div class="file-info">
                                                               <span class="file-name"><a href="{{ $doc->fileUrl() }}" target="_blank" style="color: inherit; text-decoration: none;">{{ $doc->file_name }}</a></span>
                                                               <span class="file-meta">Uploaded {{ $doc->created_at->format('d.m.Y H:i') }}</span>
                                                           </div>
                                                           <div class="btn-delete-file delete-pricing-doc" data-id="{{ $doc->id }}"><i class="ti-trash"></i></div>
                                                       </div>
                                                   @endforeach
                                               </div>

                                               <input type="file" id="pricing_file_input" style="display: none;" accept=".pdf,.doc,.docx,.jpg,.png,.xlsx,.xls">
                                               
                                               <div class="upload-area" id="pricing_upload_area">
                                                   <div class="upload-text">Drag files here or click to browse</div>
                                                   <div class="upload-icon"><i class="ti-arrow-up"></i></div>
                                               </div>
                                           </div>
                                       </div>
                                   </div>

                                                                 <!-- Email Settings Tab -->
                                                                 <div id="email-settings" class="tab-content-custom">
                                                                     <div class="form-pillar-container">
                                                                         <!-- Pillar 1: Export email settings -->
                                                                         <div class="form-pillar">
                                                                             <div class="form-section-header">Export email settings</div>
                                                                             
                                                                             <div class="form-group-custom">
                                                                                  <label class="form-label-custom">Select services to add export emails</label>
                                                                                   <select id="export_services_select" name="export_services[]" class="form-select-custom select2-multiple" multiple>
                                                                                       @foreach(['Airfreight', 'Courier', 'Onboard delivery', 'Release', 'Seafreight', 'Truck', 'Hand carry'] as $svc)
                                                                                           <option value="{{ $svc }}" {{ in_array($svc, $hub->export_services ?? []) ? 'selected' : '' }}>{{ $svc }}</option>
                                                                                       @endforeach
                                                                                   </select>
                                                                              </div>

                                                                              <div id="export_emails_dynamic_container">
                                                                                  <!-- Dynamic email fields will be appended here -->
                                                                              </div>
                                                                         </div>

                                                                         <!-- Pillar 2: Import email settings -->
                                                                         <div class="form-pillar">
                                                                             <div class="form-section-header">Import email settings</div>
                                                                             
                                                                             <div class="form-group-custom">
                                                                                  <label class="form-label-custom">Select services to add import emails</label>
                                                                                   <select id="import_services_select" name="import_services[]" class="form-select-custom select2-multiple" multiple>
                                                                                       @foreach(['Airfreight', 'Courier', 'Onboard delivery', 'Release', 'Seafreight', 'Truck', 'Hand carry'] as $svc)
                                                                                           <option value="{{ $svc }}" {{ in_array($svc, $hub->import_services ?? []) ? 'selected' : '' }}>{{ $svc }}</option>
                                                                                       @endforeach
                                                                                   </select>
                                                                              </div>

                                                                              <div id="import_emails_dynamic_container">
                                                                                  <!-- Dynamic email fields will be appended here -->
                                                                              </div>
                                                                         </div>

                                                                         <!-- Pillar 3: Other email settings -->
                                                                         <div class="form-pillar">
                                                                             <div class="form-section-header">Other email settings</div>
                                                                             
                                                                             <div class="form-group-custom">
                                                                                  <label class="form-label-custom">Send "Stock item changed" emails to</label>
                                                                                   <input type="text" name="stock_item_changed_emails" class="form-input-custom" value="{{ $hub->stock_item_changed_emails }}" placeholder="Enter emails">
                                                                              </div>

                                                                             <div class="form-group-custom">
                                                                                  <label class="form-label-custom">Send quote requests emails to</label>
                                                                                   <input type="text" name="quote_requests_emails" class="form-input-custom" value="{{ $hub->quote_requests_emails }}" placeholder="Enter emails">
                                                                              </div>
                                                                         </div>
                                                                     </div>
                                                                 </div>

                                                                  <!-- Scan gun Tab -->
                                                                  <div id="scan-gun" class="tab-content-custom">
                                                                      <div class="form-pillar-container" style="display: block; max-width: 400px;">
                                                                          <!-- Credentials Section -->
                                                                          <div class="form-pillar" style="margin-bottom: 40px;">
                                                                              <div class="form-section-header">Credentials</div>
                                                                              
                                                                              <div class="form-group-custom">
                                                                                  <label class="form-label-custom">Login</label>
                                                                                  <div class="input-group-custom">
                                                                                      <input type="text" name="scan_gun_login" class="form-input-custom" value="{{ $hub->scan_gun_login }}">
                                                                                      <button type="button" class="btn-input-append"><i class="ti-more-alt"></i></button>
                                                                                  </div>
                                                                              </div>

                                                                              <div class="checkbox-group">
                                                                                  <input type="checkbox" class="checkbox-custom" name="set_new_password" id="set_new_password" value="1">
                                                                                  <label class="checkbox-label" for="set_new_password">Set a new password</label>
                                                                              </div>

                                                                              <div class="form-group-custom">
                                                                                  <label class="form-label-custom">Password</label>
                                                                                  <div class="input-group-custom">
                                                                                      <input type="password" name="scan_gun_password" id="scan_gun_password_input" class="form-input-custom" value="{{ $hub->scan_gun_password }}" readonly>
                                                                                      <button type="button" class="btn-input-append" style="right: 35px;"><i class="ti-more-alt"></i></button>
                                                                                      <button type="button" class="btn-input-append toggle-password" data-target="scan_gun_password_input"><i class="ti-eye"></i></button>
                                                                                  </div>
                                                                              </div>
                                                                          </div>

                                                                          <!-- Features Section -->
                                                                          <div class="form-pillar">
                                                                              <div class="form-section-header">Features</div>
                                                                              
                                                                              <div class="hub-checkbox-stack">
                                                                                  <div class="checkbox-group">
                                                                                      <input type="checkbox" class="checkbox-custom" name="scangun_photo_taking" id="scangun_photo_taking" value="1" {{ $hub->scangun_photo_taking ? 'checked' : '' }}>
                                                                                      <label class="checkbox-label" for="scangun_photo_taking">Enable picture taking in scangun app</label>
                                                                                  </div>
                                                                                  <div class="checkbox-group">
                                                                                      <input type="checkbox" class="checkbox-custom" name="scangun_detailed_shipment_out" id="scangun_detailed_shipment_out" value="1" {{ $hub->scangun_detailed_shipment_out ? 'checked' : '' }}>
                                                                                      <label class="checkbox-label" for="scangun_detailed_shipment_out">Enable detailed shipment out</label>
                                                                                  </div>
                                                                              </div>
                                                                          </div>
                                                                      </div>
                                                                  </div>

            </form>

            <!-- Hub Users / Contacts tabs live outside hubEditForm (separate create/edit pages). -->
            <div id="hub-users" class="tab-content-custom">
                <div class="hub-pane-toolbar">
                    <a href="{{ route('hub.users.create', $hub->id) }}" class="btn-hub-pane-action">Add hub user</a>
                </div>
                <div class="hub-table-wrap">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th style="width: 35%;">Name</th>
                                <th style="width: 25%;">Email</th>
                                <th style="width: 20%;">Phone number</th>
                                <th style="width: 15%; text-align: center;">Scan Gun</th>
                                <th style="width: 5%; text-align: right;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hub->hubUsers as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone_number }}</td>
                                    <td style="text-align: center;">@if($user->show_in_scan_gun)<i class="ti-check" style="color: #01a9ac;"></i>@endif</td>
                                    <td style="text-align: right;">
                                        <a href="{{ route('hub.users.edit', [$hub->id, $user->id]) }}">
                                            <i class="ti-pencil btn-action-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 40px; color: #8da2b5;">No hub users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="contacts" class="tab-content-custom">
                <div class="hub-pane-toolbar">
                    <a href="{{ route('hub.contacts.create', $hub->id) }}" class="btn-hub-pane-action">Add contact</a>
                </div>
                <div class="hub-table-wrap">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Name</th>
                                <th style="width: 25%;">Email</th>
                                <th style="width: 20%;">Phone number</th>
                                <th style="width: 20%;">Description</th>
                                <th style="width: 5%;">Main</th>
                                <th style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hub->contacts as $contact)
                                <tr>
                                    <td>
                                        <a href="{{ route('hub.contacts.edit', [$hub->id, $contact->id]) }}" class="table-link">{{ $contact->name }}</a>
                                    </td>
                                    <td>{{ $contact->email }}</td>
                                    <td>{{ $contact->phone_number }}</td>
                                    <td>{{ $contact->description }}</td>
                                    <td style="text-align: center;">@if($contact->is_main_contact)<i class="ti-check" style="color: #01a9ac;"></i>@endif</td>
                                    <td style="text-align: right;">
                                        <a href="{{ route('hub.contacts.edit', [$hub->id, $contact->id]) }}">
                                            <i class="ti-pencil btn-action-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px; color: #9ca3af;">No contacts found for this hub.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
                                                            </div>
        </div>

        <div class="hub-edit-footer">
            <button type="submit" class="btn-save-custom" form="hubEditForm">Save hub</button>
            <a href="{{ route('hub.index') }}" class="btn-cancel-custom">Cancel</a>
            <div class="audit-info">
                @include('partials.audit-info', ['record' => $hub, 'bold' => true])
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function() {
            $('body').addClass('edit-hub-page');
            // Tab switching logic (keeps URL hash + hidden field so update redirects restore the active tab)
            function activateHubTab(tabId) {
                if (!tabId || !$('#' + tabId).length || !$('.tab-item[data-tab="' + tabId + '"]').length) {
                    return false;
                }
                $('.tab-item').removeClass('active');
                $('.tab-item[data-tab="' + tabId + '"]').addClass('active');
                $('.tab-content-custom').removeClass('active');
                $('#' + tabId).addClass('active');
                $('#active_tab').val(tabId);
                return true;
            }

            $('.tab-item').on('click', function() {
                var tabId = $(this).data('tab');
                activateHubTab(tabId);
                if (history.replaceState) {
                    history.replaceState(null, '', '#' + tabId);
                } else {
                    window.location.hash = tabId;
                }
                
                // Re-initialize or trigger change for Select2 in hidden tabs to fix layout issues
                if (tabId === 'email-settings') {
                    $('.select2-multiple, .select2-tags').each(function() {
                        if ($(this).data('select2')) {
                            $(this).select2('destroy');
                        }
                    });
                    
                    $('.select2-multiple').select2({
                        placeholder: 'Select services',
                        width: '100%'
                    });

                    $('.select2-tags').select2({
                        placeholder: 'Add emails',
                        tags: true,
                        tokenSeparators: [',', ';', ' '],
                        width: '100%'
                    });

                    // Pass initial dynamic email data from PHP to JS
                    var initialExportEmails = @json($hub->export_emails ?? []);
                    var initialImportEmails = @json($hub->import_emails ?? []);

                    // Trigger initial update for dynamic fields
                    updateServiceEmailFields('export_services_select', 'export_emails_dynamic_container', 'export', initialExportEmails);
                    updateServiceEmailFields('import_services_select', 'import_emails_dynamic_container', 'import', initialImportEmails);
                }
            });

            // Datepicker Initialization
            $('.datepicker-input').datepicker({
                dateFormat: 'dd.mm.yy',
                changeMonth: true,
                changeYear: true,
                yearRange: "-100:+10"
            });

            // Calendar icon trigger
            $(document).on('click', '.calendar-trigger', function() {
                var input = $(this).closest('.input-group-custom').find('.datepicker-input');
                if (input.length) {
                    input.datepicker('show');
                }
            });

            // Dynamic Service Email Field Logic
            function updateServiceEmailFields(selectId, containerId, type, initialData = {}) {
                var select = $('#' + selectId);
                var container = $('#' + containerId);
                var selectedOptions = select.val() || [];

                // Remove fields for options no longer selected
                container.find('.dynamic-email-group').each(function() {
                    var serviceName = $(this).data('service');
                    if (!selectedOptions.includes(serviceName)) {
                        $(this).remove();
                    }
                });

                // Add fields for NEWLY selected options
                selectedOptions.forEach(function(service) {
                    if (container.find('[data-service="' + service + '"]').length === 0) {
                        var existingValue = initialData[service] || '';
                        var fieldHtml = `
                            <div class="dynamic-email-group" data-service="${service}" style="margin-top: 20px;">
                                <div class="form-section-header" style="text-transform: capitalize;">${service} ${type} emails</div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom">Send ${service.toLowerCase()} ${type} email to</label>
                                    <input type="text" name="${type}_emails[${service}]" class="form-input-custom" value="${existingValue}" placeholder="Enter ${service.toLowerCase()} emails">
                                </div>
                            </div>
                        `;
                        container.append(fieldHtml);
                    }
                });
            }

            // Register change listeners for dynamic fields
            $('#export_services_select').on('change', function() {
                updateServiceEmailFields('export_services_select', 'export_emails_dynamic_container', 'export');
            });
            $('#import_services_select').on('change', function() {
                updateServiceEmailFields('import_services_select', 'import_emails_dynamic_container', 'import');
            });

            // Restore active tab from URL hash after helpers are defined
            var hashTab = window.location.hash.replace(/^#/, '');
            if (hashTab === 'email-settings') {
                $('.tab-item[data-tab="email-settings"]').triggerHandler('click');
            } else if (hashTab) {
                activateHubTab(hashTab);
            }

            // Initialize Select2
            $('.select2-single').select2({
                placeholder: 'Select an option',
                allowClear: true,
                width: '100%'
            });

            $('.select2-multiple').select2({
                placeholder: 'Select services',
                width: '100%'
            });

            $('.select2-tags').select2({
                placeholder: 'Add emails',
                tags: true,
                tokenSeparators: [',', ';', ' '],
                width: '100%'
            });

            // Hub Document Upload Logic
            $('#sop_upload_area').on('click', function() {
                $('#sop_file_input').click();
            });

            $('#pricing_upload_area').on('click', function() {
                $('#pricing_file_input').click();
            });

            $('#sop_file_input').on('change', function() {
                var file = this.files[0];
                if (file) {
                    uploadHubDocument(file, 'sop', '#sop_documents_list', '#sop_file_input');
                }
            });

            $('#pricing_file_input').on('change', function() {
                var file = this.files[0];
                if (file) {
                    uploadHubDocument(file, 'pricing', '#pricing_documents_list', '#pricing_file_input');
                }
            });

            function uploadHubDocument(file, type, listSelector, inputSelector) {
                var formData = new FormData();
                formData.append('file', file);
                formData.append('document_type', type);
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: '{{ route("hub.documents.upload", $hub->id) }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            var doc = response.document;
                            var html = `
                                <div class="file-list-item" id="${type}_doc_item_${doc.id}">
                                    <div class="file-info">
                                        <span class="file-name"><a href="${doc.url || '#'}" target="_blank" style="color: inherit; text-decoration: none;">${doc.name}</a></span>
                                        <span class="file-meta">Uploaded ${doc.uploaded_at}</span>
                                    </div>
                                    <div class="btn-delete-file delete-doc" data-id="${doc.id}" data-type="${type}"><i class="ti-trash"></i></div>
                                </div>
                            `;
                            $(listSelector).append(html);
                            $(inputSelector).val('');
                        }
                    },
                    error: function(xhr) {
                        alert('Upload failed: ' + (xhr.responseJSON.message || 'Unknown error'));
                    }
                });
            }

            // Hub Document Delete Logic (SOP and Pricing)
            $(document).on('click', '.delete-doc, .delete-pricing-doc', function() {
                var btn = $(this);
                var docId = btn.data('id');
                var type = btn.hasClass('delete-pricing-doc') ? 'pricing' : (btn.data('type') || 'sop');
                var item = btn.closest('.file-list-item');

                if (confirm('Are you sure you want to delete this document?')) {
                    $.ajax({
                        url: '/hubs/documents/' + docId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}',
                            type: type
                        },
                        success: function(response) {
                            if (response.success) {
                                item.fadeOut(300, function() {
                                    $(this).remove();
                                });
                            }
                        }
                    });
                }
            });

            // Scan gun Password Toggle
            $(document).on('click', '.toggle-password', function() {
                var targetId = $(this).data('target');
                var input = $('#' + targetId);
                var icon = $(this).find('i');
                
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('ti-eye').addClass('ti-eye-close'); // Or similar eye-off icon
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('ti-eye-close').addClass('ti-eye');
                }
            });

            // Set New Password Checkbox Toggle
            $('#set_new_password').on('change', function() {
                var isChecked = $(this).is(':checked');
                var passwordInput = $('#scan_gun_password_input');
                
                if (isChecked) {
                    passwordInput.prop('readonly', false).focus();
                    passwordInput.val(''); // Clear for new password entry
                } else {
                    passwordInput.prop('readonly', true);
                    passwordInput.val('{{ $hub->scan_gun_password }}'); // Restore current value
                }
            });

            // jQuery Validation for Hub Edit Form
            $.validator.addMethod('multiEmail', function (value, element) {
                if (this.optional(element)) {
                    return true;
                }

                var emails = value.split(/[;,]+/).map(function (part) {
                    return $.trim(part);
                }).filter(Boolean);

                if (!emails.length) {
                    return false;
                }

                return emails.every(function (email) {
                    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
                });
            }, 'Please enter valid email address(es), separated by comma or semicolon');

            $('#hubEditForm').validate({
                rules: {
                    hub_name: {
                        required: true,
                        minlength: 3
                    },
                    contact_person: {
                        required: true
                    },
                    email: {
                        multiEmail: true
                    },
                    portal_email: {
                        multiEmail: true
                    }
                },
                messages: {
                    hub_name: {
                        required: "Please enter the hub name",
                        minlength: "Hub name must be at least 3 characters"
                    },
                    contact_person: {
                        required: "Please enter the contact person"
                    },
                    email: {
                        multiEmail: "Please enter valid email address(es), separated by comma or semicolon"
                    },
                    portal_email: {
                        multiEmail: "Please enter valid email address(es), separated by comma or semicolon"
                    }
                },
                errorElement: 'div',
                errorClass: 'error-message',
                errorPlacement: function(error, element) {
                    if (element.hasClass('select2-flag') || element.is('[data-country-select]')) {
                        error.insertAfter(element.next('.select2-container'));
                    } else if (element.parent('.input-group-custom').length) {
                        error.insertAfter(element.parent());
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass("error");
                    if ($(element).hasClass('select2-flag') || $(element).is('[data-country-select]')) {
                        $(element).next('.select2-container').addClass('error');
                    }
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass("error");
                    if ($(element).hasClass('select2-flag') || $(element).is('[data-country-select]')) {
                        $(element).next('.select2-container').removeClass('error');
                    }
                }
            });
        });
    </script>

    @include('partials.unsaved-changes-guard', ['formSelector' => '#hubEditForm', 'fallbackUrl' => route('hub.index')])
@endsection
