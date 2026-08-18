<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerInvoiceDetail;
use App\Models\CustomerResponsible;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Country;
use App\Models\User;
use App\Models\CustomerGroup;
use App\Models\CustomerSop;
use App\Models\CustomerNotificationSetting;
use App\Models\CustomerDocument;
use App\Models\CustomerVessel;
use App\Models\Contact;
use App\Services\AdministrationChangeLogService;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $responsibleOfficesFilter = array_values(array_filter((array) $request->input('responsible_office', [])));
        $accountManagersFilter = array_values(array_filter((array) $request->input('account_manager', [])));
        $salesManagersFilter = array_values(array_filter((array) $request->input('sales_manager', [])));
        $countriesFilter = array_values(array_filter((array) $request->input('country', [])));
        $perPage = max(10, min(100, (int) $request->query('per_page', 25)));

        $customers = Customer::query()
            ->with([
                'primaryAddress.country',
                'responsible.accountManager.office',
                'responsible.salesManager',
                'contacts' => fn ($query) => $query->where('is_main_contact', true),
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('customer_name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('contact_person', 'like', '%' . $search . '%')
                        ->orWhere('customer_number', 'like', '%' . $search . '%');
                });
            })
            ->when($responsibleOfficesFilter, function ($query) use ($responsibleOfficesFilter) {
                $query->whereHas('responsible.accountManager.office', function ($sub) use ($responsibleOfficesFilter) {
                    $sub->where(function ($officeQuery) use ($responsibleOfficesFilter) {
                        $officeQuery->whereIn('office_short_name', $responsibleOfficesFilter)
                            ->orWhereIn('office_name', $responsibleOfficesFilter);
                    });
                });
            })
            ->when($accountManagersFilter, fn ($query) => $query->whereHas('responsible.accountManager', fn ($sub) => $sub->whereIn('name', $accountManagersFilter)))
            ->when($salesManagersFilter, fn ($query) => $query->whereHas('responsible.salesManager', fn ($sub) => $sub->whereIn('name', $salesManagersFilter)))
            ->when($countriesFilter, fn ($query) => $query->whereHas('primaryAddress.country', fn ($sub) => $sub->whereIn('name', $countriesFilter)))
            ->orderBy('customer_name')
            ->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('customers.partials.rows', compact('customers'))->render(),
                'pagination' => (string) $customers->links(),
                'total' => $customers->total(),
            ]);
        }

        $responsibleOffices = Customer::query()
            ->join('customer_responsibles', 'customer_responsibles.customer_id', '=', 'customers.id')
            ->join('contacts as account_contacts', 'account_contacts.id', '=', 'customer_responsibles.account_manager_id')
            ->join('offices', 'offices.id', '=', 'account_contacts.office_id')
            ->select('offices.office_short_name')
            ->whereNotNull('offices.office_short_name')
            ->distinct()
            ->orderBy('offices.office_short_name')
            ->pluck('offices.office_short_name')
            ->values();

        $accountManagers = Contact::query()
            ->whereIn('id', CustomerResponsible::query()->whereNotNull('account_manager_id')->pluck('account_manager_id'))
            ->orderBy('name')
            ->pluck('name')
            ->values();

        $salesManagers = Contact::query()
            ->whereIn('id', CustomerResponsible::query()->whereNotNull('sales_manager_id')->pluck('sales_manager_id'))
            ->orderBy('name')
            ->pluck('name')
            ->values();

        $countries = Country::query()
            ->whereIn('id', CustomerAddress::query()->where('type', 'primary')->whereNotNull('country_id')->pluck('country_id'))
            ->orderBy('name')
            ->pluck('name')
            ->values();

        return view('customers.index', compact(
            'customers',
            'responsibleOffices',
            'accountManagers',
            'salesManagers',
            'countries'
        ));
    }

    public function create()
    {
        $countries = Country::all();
        $salesManagers = Contact::where('category', 'sales')->get();
        $groups = CustomerGroup::all();
        
        return view('customers.create', compact('countries', 'salesManagers', 'groups'));
    }

    public function store(Request $request)
    {
        // Server-side validation
        $validatedData = $request->validate([
            'customer_name' => 'required|string|max:150',
            'email' => ['required', 'string', 'max:500', $this->multipleEmailsValidator()],
            'contact_person' => 'required|string|max:255',
            'street_address' => 'required|string',
            'city' => 'required|string',
            'country' => 'required',
            'invoice_recipient_name' => 'required|string',
            'invoice_recipient_address' => 'required|string',
            'invoice_city' => 'required|string',
            'invoice_country' => 'required',
            'currency' => 'required',
            'invoicing_email' => 'required|email',
            'sales_manager' => 'required',
            'main_account_manager' => 'required',
            'logo' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
            'sop_documents' => 'nullable|array',
            'sop_documents.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp|max:10240',
        ]);

        DB::beginTransaction();

        try {
            // Handle customer_group_id (if N/A, set to null)
            $customerGroupId = ($request->customer_group === 'N/A') ? null : $request->customer_group;

            // 1. Create Customer
            $customer = Customer::create([
                'customer_name' => $request->customer_name,
                'customer_number' => $request->customer_number_fm,
                'customer_group_id' => $customerGroupId,
                'phone' => $request->phone_number,
                'contact_person' => $request->contact_person,
                'email' => $request->email,
                'internal_shipment' => $request->internal_shipment,
                'remarks' => $request->remarks,
                'special_considerations' => $request->special_considerations,
                'un_locode' => $request->un_locode,
                'show_transport_details' => $request->has('show_transport_details') ? 1 : 0,
                'esea_store_stock_only' => $request->has('esea_store_stock_only') ? 1 : 0,
            ]);

            // 2. Create Addresses
            // Primary Address
            CustomerAddress::create([
                'customer_id' => $customer->id,
                'type' => 'primary',
                'street' => $request->street_address,
                'city' => $request->city,
                'state' => $request->district_state,
                'zip_code' => $request->zip_code,
                'country_id' => $request->country,
                'port_code' => $request->port_code,
            ]);

            // Postal Address (Optional)
            if ($request->filled('postal_street_address') || $request->filled('postal_city')) {
                CustomerAddress::create([
                    'customer_id' => $customer->id,
                    'type' => 'postal',
                    'street' => $request->postal_street_address,
                    'city' => $request->postal_city,
                    'state' => $request->postal_district_state,
                    'zip_code' => $request->postal_zip_code,
                    'country_id' => $request->postal_country,
                ]);
            }

            // Invoice Address (Using dedicated fields from form)
            CustomerAddress::create([
                'customer_id' => $customer->id,
                'type' => 'invoice',
                'street' => $request->invoice_recipient_address,
                'city' => $request->invoice_city,
                'state' => $request->invoice_district_state,
                'zip_code' => $request->invoice_zip_code,
                'country_id' => $request->invoice_country,
            ]);

            // 3. Create Invoice Details
            CustomerInvoiceDetail::create([
                'customer_id' => $customer->id,
                'invoice_recipient_name' => $request->invoice_recipient_name,
                'invoice_email' => $request->invoicing_email,
                'invoice_email_cc' => $request->invoicing_email_cc,
                'currency_code' => $request->currency,
                'payment_terms_days' => $request->payment_terms ?? 30,
                'invoice_frequency' => $request->invoice_frequency,
                'invoice_remarks' => $request->invoicing_remarks,
                'vat_number' => $request->vat_number,
                'eori_number' => $request->eori_number,
            ]);

            // 4. Create Responsible
            CustomerResponsible::create([
                'customer_id' => $customer->id,
                'sales_manager_id' => $request->sales_manager,
                'account_manager_id' => $request->main_account_manager,
                'accounting_user_id' => $request->responsible_accounting_users,
            ]);

            // 5. Create SOP
            CustomerSop::create([
                'customer_id' => $customer->id,
                'send_stocklist' => $request->send_stocklist,
                'onboard_delivery' => $request->onboard_delivery,
                'quotes_prior_to_instructions' => $request->quotes_prior_to_instructions,
                'agreed_rate' => $request->agreed_rate,
                'invoicing_procedure' => $request->invoicing_procedure,
                'pending_entry' => $request->pending_entry,
                'special_pending_routines' => $request->special_pending_routines,
                'other_procedures_comments' => $request->other_procedures_comments,
            ]);

            // 6. Create Notification Settings
            CustomerNotificationSetting::create([
                'customer_id' => $customer->id,
                'notify_stock_items' => $request->notify_stock_items,
                'send_automatic_first_mile_email' => $request->has('send_automatic_first_mile_email') ? 1 : 0,
                'notify_first_mile_email_sent' => $request->notify_first_mile_email_sent,
                'shipping_free_storage_days' => $request->shipping_free_storage_days,
                'shipping_free_storage_weight' => $request->shipping_free_storage_weight,
                'shipping_free_storage_volume' => $request->shipping_free_storage_volume,
                'notify_free_storage_exceeded' => $request->notify_free_storage_exceeded,
            ]);

            DB::commit();

            return redirect()->route('customers.index')->with('success', 'Customer created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving customer: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withInput()->with('error', 'Failed to save customer. Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $customer = Customer::with([
            'primaryAddress',
            'postalAddress',
            'invoiceAddress',
            'invoiceDetail',
            'responsible.accountManager',
            'responsible.salesManager',
            'responsible.accountingUser.office',
            'group',
            'sop',
            'notificationSetting',
            'documents',
            'vessels',
            'contacts',
            'creator',
            'updater',
        ])->findOrFail($id);
        $countries = Country::all();
        $salesManagers = Contact::where('category', 'sales')->get();
        $groups = CustomerGroup::all();
        
        return view('customers.edit', compact('customer', 'countries', 'salesManagers', 'groups', 'id'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        // Server-side validation (matching create rules)
        $validatedData = $request->validate([
            'customer_name' => 'required|string|max:150',
            'email' => ['required', 'string', 'max:500', $this->multipleEmailsValidator()],
            'contact_person' => 'required|string|max:255',
            'street_address' => 'required|string',
            'city' => 'required|string',
            'country' => 'required',
            'invoice_recipient_name' => 'required|string',
            'invoice_recipient_address' => 'required|string',
            'invoice_city' => 'required|string',
            'invoice_country' => 'required',
            'currency' => 'required',
            'invoicing_email' => 'required|email',
            'sales_manager' => 'required',
            'main_account_manager' => 'required',
            'logo' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
            'sop_documents' => 'nullable|array',
            'sop_documents.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp|max:10240',
        ]);

        DB::beginTransaction();

        try {
            $changeLogService = app(AdministrationChangeLogService::class);
            $beforeRelated = $this->customerRelatedChangeSnapshot($customer);

            // Handle customer_group_id
            $customerGroupId = ($request->customer_group === 'N/A') ? null : $request->customer_group;

            // Handle Logo Upload
            $logoPath = $customer->logo;
            if ($request->hasFile('logo')) {
                if ($logoPath) {
                    \App\Support\PrivateDisk::delete($logoPath);
                }
                $logoPath = $request->file('logo')->store('logos', 'private');
            }

            // 1. Update Customer
            $customer->update([
                'customer_name' => $request->customer_name,
                'customer_number' => $request->customer_number_fm,
                'customer_group_id' => $customerGroupId,
                'phone' => $request->phone_number,
                'contact_person' => $request->contact_person,
                'email' => $request->email,
                'internal_shipment' => $request->internal_shipment,
                'remarks' => $request->remarks,
                'special_considerations' => $request->special_considerations,
                'un_locode' => $request->un_locode,
                'show_transport_details' => $request->has('show_transport_details') ? 1 : 0,
                'esea_store_stock_only' => $request->has('esea_store_stock_only') ? 1 : 0,
                'logo' => $logoPath,
            ]);

            // 2. Update/Create Addresses
            // Primary Address
            CustomerAddress::updateOrCreate(
                ['customer_id' => $customer->id, 'type' => 'primary'],
                [
                    'street' => $request->street_address,
                    'city' => $request->city,
                    'state' => $request->district_state,
                    'zip_code' => $request->zip_code,
                    'country_id' => $request->country,
                    'port_code' => $request->port_code,
                ]
            );

            // Postal Address
            if (
                $request->filled('postal_street_address')
                || $request->filled('postal_city')
                || $request->filled('postal_district_state')
                || $request->filled('postal_zip_code')
                || $request->filled('postal_country')
            ) {
                CustomerAddress::updateOrCreate(
                    ['customer_id' => $customer->id, 'type' => 'postal'],
                    [
                        'street' => $request->postal_street_address,
                        'city' => $request->postal_city,
                        'state' => $request->postal_district_state,
                        'zip_code' => $request->postal_zip_code,
                        'country_id' => $request->postal_country,
                    ]
                );
            }

            // Invoice Address
            CustomerAddress::updateOrCreate(
                ['customer_id' => $customer->id, 'type' => 'invoice'],
                [
                    'street' => $request->invoice_recipient_address,
                    'city' => $request->invoice_city,
                    'state' => $request->invoice_district_state,
                    'zip_code' => $request->invoice_zip_code,
                    'country_id' => $request->invoice_country,
                ]
            );

            // 3. Update Invoice Details
            CustomerInvoiceDetail::updateOrCreate(
                ['customer_id' => $customer->id],
                [
                    'invoice_recipient_name' => $request->invoice_recipient_name,
                    'invoice_email' => $request->invoicing_email,
                    'invoice_email_cc' => $request->invoicing_email_cc,
                    'currency_code' => $request->currency,
                    'payment_terms_days' => $request->payment_terms ?? 30,
                    'invoice_frequency' => $request->invoice_frequency,
                    'invoice_remarks' => $request->invoicing_remarks,
                    'vat_number' => $request->vat_number,
                    'eori_number' => $request->eori_number,
                ]
            );

            // 4. Update Responsible
            CustomerResponsible::updateOrCreate(
                ['customer_id' => $customer->id],
                [
                    'sales_manager_id' => $request->sales_manager,
                    'account_manager_id' => $request->main_account_manager,
                    'accounting_user_id' => $request->responsible_accounting_users,
                ]
            );

            // 5. Update SOP
            CustomerSop::updateOrCreate(
                ['customer_id' => $customer->id],
                [
                    'send_stocklist' => $request->send_stocklist,
                    'onboard_delivery' => $request->onboard_delivery,
                    'quotes_prior_to_instructions' => $request->quotes_prior_to_instructions,
                    'agreed_rate' => $request->agreed_rate,
                    'invoicing_procedure' => $request->invoicing_procedure,
                    'pending_entry' => $request->pending_entry,
                    'special_pending_routines' => $request->special_pending_routines,
                    'other_procedures_comments' => $request->other_procedures_comments,
                ]
            );

            // 6. Update Notification Settings
            CustomerNotificationSetting::updateOrCreate(
                ['customer_id' => $customer->id],
                [
                    'notify_stock_items' => $request->notify_stock_items,
                    'send_automatic_first_mile_email' => $request->has('send_automatic_first_mile_email') ? 1 : 0,
                    'notify_first_mile_email_sent' => $request->notify_first_mile_email_sent,
                    'shipping_free_storage_days' => $request->shipping_free_storage_days,
                    'shipping_free_storage_weight' => $request->shipping_free_storage_weight,
                    'shipping_free_storage_volume' => $request->shipping_free_storage_volume,
                    'notify_free_storage_exceeded' => $request->notify_free_storage_exceeded,
                ]
            );

            // 7. Handle SOP Document Removals
            if ($request->filled('removed_documents')) {
                $idsToRemove = array_filter(explode(',', $request->removed_documents));
                foreach ($idsToRemove as $docId) {
                    $doc = CustomerDocument::find(trim($docId));
                    if ($doc && $doc->customer_id == $customer->id) {
                        $fileName = $doc->file_name;
                        \App\Support\PrivateDisk::delete($doc->file_path);
                        $doc->delete();
                        $changeLogService->log($customer, 'SOP document removed', $fileName, 'sop_document');
                    }
                }
            }

            // 8. Handle SOP Document Uploads
            if ($request->hasFile('sop_documents')) {
                foreach ($request->file('sop_documents') as $file) {
                    $path = $file->store('sop_documents', 'private');
                    CustomerDocument::create([
                        'customer_id' => $customer->id,
                        'file_name'   => $file->getClientOriginalName(),
                        'file_path'   => $path,
                        'file_type'   => 'sop',
                    ]);
                    $changeLogService->log(
                        $customer,
                        'SOP document added',
                        $file->getClientOriginalName(),
                        'sop_document'
                    );
                }
            }

            $customer->unsetRelations();
            $afterRelated = $this->customerRelatedChangeSnapshot($customer);
            $changeLogService->logMappedChanges(
                $customer,
                $beforeRelated,
                $afterRelated,
                $this->customerRelatedChangeLogLabels()
            );

            DB::commit();

            return redirect()->back()->with('success', 'Customer updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating customer: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update customer. Error: ' . $e->getMessage());
        }
    }

    /**
     * Snapshot related customer form fields for administration change logging.
     * Customer table fields are logged automatically via LogsFieldChanges.
     */
    private function customerRelatedChangeSnapshot(Customer $customer): array
    {
        $customer->loadMissing([
            'group',
            'primaryAddress.country',
            'postalAddress.country',
            'invoiceAddress.country',
            'invoiceDetail',
            'responsible.salesManager',
            'responsible.accountManager',
            'responsible.accountingUser',
            'sop',
            'notificationSetting',
        ]);

        $primary = $customer->primaryAddress;
        $postal = $customer->postalAddress;
        $invoiceAddress = $customer->invoiceAddress;
        $invoice = $customer->invoiceDetail;
        $responsible = $customer->responsible;
        $sop = $customer->sop;
        $notify = $customer->notificationSetting;

        return [
            'customer_group' => $customer->group?->name ?? $customer->customer_group_id,

            'primary_street' => $primary?->street,
            'primary_city' => $primary?->city,
            'primary_state' => $primary?->state,
            'primary_zip_code' => $primary?->zip_code,
            'primary_country' => $primary?->country?->name ?? $primary?->country_id,
            'primary_port_code' => $primary?->port_code,

            'postal_street' => $postal?->street,
            'postal_city' => $postal?->city,
            'postal_state' => $postal?->state,
            'postal_zip_code' => $postal?->zip_code,
            'postal_country' => $postal?->country?->name ?? $postal?->country_id,

            'invoice_street' => $invoiceAddress?->street,
            'invoice_city' => $invoiceAddress?->city,
            'invoice_state' => $invoiceAddress?->state,
            'invoice_zip_code' => $invoiceAddress?->zip_code,
            'invoice_country' => $invoiceAddress?->country?->name ?? $invoiceAddress?->country_id,

            'invoice_recipient_name' => $invoice?->invoice_recipient_name,
            'invoice_email' => $invoice?->invoice_email,
            'invoice_email_cc' => $invoice?->invoice_email_cc,
            'currency_code' => $invoice?->currency_code,
            'payment_terms_days' => $invoice?->payment_terms_days,
            'invoice_frequency' => $invoice?->invoice_frequency,
            'invoice_remarks' => $invoice?->invoice_remarks,
            'vat_number' => $invoice?->vat_number,
            'eori_number' => $invoice?->eori_number,

            'sales_manager' => $responsible?->salesManager?->name ?? $responsible?->sales_manager_id,
            'account_manager' => $responsible?->accountManager?->name ?? $responsible?->account_manager_id,
            'accounting_user' => $responsible?->accountingUser?->name ?? $responsible?->accounting_user_id,

            'send_stocklist' => $sop?->send_stocklist,
            'onboard_delivery' => $sop?->onboard_delivery,
            'quotes_prior_to_instructions' => $sop?->quotes_prior_to_instructions,
            'agreed_rate' => $sop?->agreed_rate,
            'invoicing_procedure' => $sop?->invoicing_procedure,
            'pending_entry' => $sop?->pending_entry,
            'special_pending_routines' => $sop?->special_pending_routines,
            'other_procedures_comments' => $sop?->other_procedures_comments,

            'notify_stock_items' => $notify?->notify_stock_items,
            'send_automatic_first_mile_email' => $notify?->send_automatic_first_mile_email,
            'notify_first_mile_email_sent' => $notify?->notify_first_mile_email_sent,
            'shipping_free_storage_days' => $notify?->shipping_free_storage_days,
            'shipping_free_storage_weight' => $notify?->shipping_free_storage_weight,
            'shipping_free_storage_volume' => $notify?->shipping_free_storage_volume,
            'notify_free_storage_exceeded' => $notify?->notify_free_storage_exceeded,
        ];
    }

    private function customerRelatedChangeLogLabels(): array
    {
        return [
            'customer_group' => 'Customer group',

            'primary_street' => 'Primary street',
            'primary_city' => 'Primary city',
            'primary_state' => 'Primary state',
            'primary_zip_code' => 'Primary zip code',
            'primary_country' => 'Primary country',
            'primary_port_code' => 'Primary port code',

            'postal_street' => 'Postal street',
            'postal_city' => 'Postal city',
            'postal_state' => 'Postal state',
            'postal_zip_code' => 'Postal zip code',
            'postal_country' => 'Postal country',

            'invoice_street' => 'Invoice street',
            'invoice_city' => 'Invoice city',
            'invoice_state' => 'Invoice state',
            'invoice_zip_code' => 'Invoice zip code',
            'invoice_country' => 'Invoice country',

            'invoice_recipient_name' => 'Invoice recipient name',
            'invoice_email' => 'Invoicing email',
            'invoice_email_cc' => 'Invoicing email CC',
            'currency_code' => 'Currency',
            'payment_terms_days' => 'Payment terms',
            'invoice_frequency' => 'Invoice frequency',
            'invoice_remarks' => 'Invoicing remarks',
            'vat_number' => 'VAT number',
            'eori_number' => 'EORI number',

            'sales_manager' => 'Sales manager',
            'account_manager' => 'Account manager',
            'accounting_user' => 'Accounting user',

            'send_stocklist' => 'Send stocklist',
            'onboard_delivery' => 'Onboard delivery',
            'quotes_prior_to_instructions' => 'Quotes prior to instructions',
            'agreed_rate' => 'Agreed rate',
            'invoicing_procedure' => 'Invoicing procedure',
            'pending_entry' => 'Pending entry',
            'special_pending_routines' => 'Special pending routines',
            'other_procedures_comments' => 'Other procedures comments',

            'notify_stock_items' => 'Notify stock items',
            'send_automatic_first_mile_email' => 'Send automatic first mile email',
            'notify_first_mile_email_sent' => 'Notify first mile email sent',
            'shipping_free_storage_days' => 'Shipping free storage days',
            'shipping_free_storage_weight' => 'Shipping free storage weight',
            'shipping_free_storage_volume' => 'Shipping free storage volume',
            'notify_free_storage_exceeded' => 'Notify free storage exceeded',
        ];
    }

    /**
     * AJAX: Upload a single SOP document for a customer.
     */
    public function uploadDocument(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp|max:10240',
        ]);

        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file provided'], 422);
        }

        $file = $request->file('file');
        $path = $file->store('sop_documents', 'private');

        $doc = CustomerDocument::create([
            'customer_id' => $customer->id,
            'file_name'   => $file->getClientOriginalName(),
            'file_path'   => $path,
            'file_type'   => 'sop',
        ]);

        app(AdministrationChangeLogService::class)->log(
            $customer,
            'SOP document added',
            $doc->file_name,
            'sop_document'
        );

        return response()->json([
            'id'        => $doc->id,
            'file_name' => $doc->file_name,
            'file_url'  => $doc->fileUrl(),
        ]);
    }

    /**
     * Stream a private customer SOP document (auth required).
     */
    public function showDocument($customerId, $docId)
    {
        $doc = CustomerDocument::where('customer_id', $customerId)->findOrFail($docId);

        return \App\Support\PrivateDisk::downloadResponse((string) $doc->file_path, (string) $doc->file_name);
    }

    /**
     * Stream a private customer logo (auth required).
     */
    public function showLogo($id)
    {
        $customer = Customer::findOrFail($id);

        if (! $customer->logo) {
            abort(404);
        }

        return \App\Support\PrivateDisk::imageResponse((string) $customer->logo, 'customer-logo');
    }

    /**
     * AJAX: Delete a SOP document.
     */
    public function deleteDocument(Request $request, $docId)
    {
        $doc = CustomerDocument::findOrFail($docId);
        $customer = Customer::find($doc->customer_id);
        $fileName = $doc->file_name;

        \App\Support\PrivateDisk::delete($doc->file_path);
        $doc->delete();

        if ($customer) {
            app(AdministrationChangeLogService::class)->log(
                $customer,
                'SOP document removed',
                $fileName,
                'sop_document'
            );
        }

        return response()->json(['success' => true]);
    }

    /**
     * Show the form for creating a new vessel.
     */
    public function createVessel($id)
    {
        $customer = Customer::findOrFail($id);
        $customerContacts = $customer->contacts;

        return view('customers.customer-vessels-add', compact('id', 'customerContacts'));
    }

    /**
     * Show the form for creating a new contact.
     */
    public function createContact($id)
    {
        return view('contacts.create', ['customer_id' => $id]);
    }

    /**
     * Show the form for editing a contact.
     */
    public function editContact($id)
    {
        $contact = Contact::findOrFail($id);
        return view('contacts.edit', compact('contact'));
    }

    /**
     * Update a contact's details.
     */
    public function updateContact(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        $contact->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'description' => $request->description,
            'is_main_contact' => $request->has('is_main_contact'),
        ]);

        return redirect()
            ->route('customers.edit', $contact->customer_id)
            ->with('success', 'Contact updated successfully.')
            ->withFragment('contacts');
    }

    /**
     * Delete a contact.
     */
    public function deleteContact($id)
    {
        $contact = Contact::findOrFail($id);
        $customer = $contact->customer_id ? Customer::find($contact->customer_id) : null;
        $contactName = $contact->name;

        $contact->delete();

        if ($customer) {
            app(AdministrationChangeLogService::class)->log(
                $customer,
                'Contact removed',
                $contactName,
                'contact'
            );
        }

        return response()->json(['success' => true]);
    }

    /**
     * Store a new contact for a customer.
     */
    public function storeContact(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        $contact = $customer->contacts()->create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'description' => $request->description,
            'is_main_contact' => $request->has('is_main_contact'),
        ]);

        app(AdministrationChangeLogService::class)->log(
            $customer,
            'Contact added',
            $contact->name,
            'contact'
        );

        return redirect()
            ->route('customers.edit', $id)
            ->with('success', 'Contact added successfully.')
            ->withFragment('contacts');
    }

    /**
     * Store a new vessel for a customer.
     */
    public function storeVessel(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'vessel' => 'required|string|max:255',
            'account_manager' => 'required|string|max:255',
        ]);

        // Extract contact data (expecting only one contact in the array based on form logic)
        $contactData = $request->input('contacts.1', []); // We use key 1 as per the JS implementation

        $vessel = CustomerVessel::create([
            'customer_id' => $customer->id,
            // Vessel information
            'vessel' => $request->vessel,
            'vessel_name_alias' => $request->vessel_name_alias,
            'vessel_imo' => $request->vessel_imo,
            'shipyard' => $request->shipyard,
            'shipyard_location' => $request->shipyard_location,
            'not_in_transit' => $request->has('not_in_transit') ? 1 : 0,
            'inactive_vessel' => $request->has('inactive_vessel') ? 1 : 0,
            'sanction_blocked' => $request->has('sanction_blocked') ? 1 : 0,
            'financially_blocked' => $request->has('financially_blocked') ? 1 : 0,
            'pre_payment_only' => $request->has('pre_payment_only') ? 1 : 0,
            'customer_vessel_code' => $request->customer_vessel_code,
            'vessel_type_alias' => $request->vessel_type_alias,
            'po_example' => $request->po_example,
            'internal_shipment' => $request->internal_shipment,
            'except_from_hubs' => $request->except_from_hubs,
            'remarks' => $request->remarks,
            // Responsible managers
            'manager' => $request->manager,
            'account_manager' => $request->account_manager,
            'receivers_stocklists' => $request->receivers_stocklists,
            // Invoice details
            'invoice_vessel_separately' => $request->has('invoice_vessel_separately') ? 1 : 0,
            'title_invoice_recipient' => $request->title_invoice_recipient,
            'yearly_customer_reference' => $request->yearly_customer_reference,
            // Home ports
            'home_consolidation_port' => $request->home_consolidation_port,
            'home_delivery_port' => $request->home_delivery_port,
            // Contact details
            'contact_id' => $contactData['contact_id'] ?? null,
            'contact_stocklists' => isset($contactData['stocklists']) ? 1 : 0,
            'contact_pre_alerts' => isset($contactData['pre_alerts']) ? 1 : 0,
            'contact_stock_notifications' => isset($contactData['stock_notifications']) ? 1 : 0,
            'contact_free_storage_notifications' => isset($contactData['free_storage_notifications']) ? 1 : 0,
            'contact_offers' => isset($contactData['offers']) ? 1 : 0,
        ]);

        app(AdministrationChangeLogService::class)->log(
            $customer,
            'Vessel added',
            $vessel->vessel,
            'vessel'
        );

        return redirect()
            ->route('customers.edit', $id)
            ->with('success', 'Vessel added successfully.')
            ->withFragment('vessels');
    }

    /**
     * Show the edit form for a vessel.
     */
    public function editVessel($id)
    {
        $vessel = CustomerVessel::with(['customer.contacts', 'creator', 'updater'])->findOrFail($id);
        $customerContacts = $vessel->customer->contacts;

        return view('customers.customer-vessels', compact('vessel', 'customerContacts'));
    }

    /**
     * Update the specified vessel.
     */
    public function updateVessel(Request $request, $id)
    {
        $vessel = CustomerVessel::with('contact')->findOrFail($id);

        $request->validate([
            'vessel' => 'required|string|max:255',
            'account_manager' => 'required|string|max:255',
        ]);

        // Extract contact data
        $contactData = $request->input('contacts.1', []);
        $changeLogService = app(AdministrationChangeLogService::class);
        $beforeContactName = $vessel->contact?->name;

        $vessel->update([
            // Vessel information
            'vessel' => $request->vessel,
            'vessel_name_alias' => $request->vessel_name_alias,
            'vessel_imo' => $request->vessel_imo,
            'shipyard' => $request->shipyard,
            'shipyard_location' => $request->shipyard_location,
            'not_in_transit' => $request->has('not_in_transit') ? 1 : 0,
            'inactive_vessel' => $request->has('inactive_vessel') ? 1 : 0,
            'sanction_blocked' => $request->has('sanction_blocked') ? 1 : 0,
            'financially_blocked' => $request->has('financially_blocked') ? 1 : 0,
            'pre_payment_only' => $request->has('pre_payment_only') ? 1 : 0,
            'customer_vessel_code' => $request->customer_vessel_code,
            'vessel_type_alias' => $request->vessel_type_alias,
            'po_example' => $request->po_example,
            'internal_shipment' => $request->internal_shipment,
            'except_from_hubs' => $request->except_from_hubs,
            'remarks' => $request->remarks,
            // Responsible managers
            'manager' => $request->manager,
            'account_manager' => $request->account_manager,
            'receivers_stocklists' => $request->receivers_stocklists,
            // Invoice details
            'invoice_vessel_separately' => $request->has('invoice_vessel_separately') ? 1 : 0,
            'title_invoice_recipient' => $request->title_invoice_recipient,
            'yearly_customer_reference' => $request->yearly_customer_reference,
            // Home ports
            'home_consolidation_port' => $request->home_consolidation_port,
            'home_delivery_port' => $request->home_delivery_port,
            // Contact details
            'contact_id' => $contactData['contact_id'] ?? null,
            'contact_stocklists' => isset($contactData['stocklists']) ? 1 : 0,
            'contact_pre_alerts' => isset($contactData['pre_alerts']) ? 1 : 0,
            'contact_stock_notifications' => isset($contactData['stock_notifications']) ? 1 : 0,
            'contact_free_storage_notifications' => isset($contactData['free_storage_notifications']) ? 1 : 0,
            'contact_offers' => isset($contactData['offers']) ? 1 : 0,
        ]);

        $vessel->load('contact');
        $changeLogService->logMappedChanges(
            $vessel,
            ['contact' => $beforeContactName],
            ['contact' => $vessel->contact?->name],
            ['contact' => 'Notification contact']
        );

        return redirect()
            ->route('customers.edit', $vessel->customer_id)
            ->with('success', 'Vessel updated successfully.')
            ->withFragment('vessels');
    }

    private function multipleEmailsValidator(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if ($value === null || $value === '') {
                return;
            }

            $emails = preg_split('/\s*[,;]\s*/', (string) $value, -1, PREG_SPLIT_NO_EMPTY);

            foreach ($emails as $email) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $fail('Each email address must be valid.');
                    return;
                }
            }
        };
    }
}
