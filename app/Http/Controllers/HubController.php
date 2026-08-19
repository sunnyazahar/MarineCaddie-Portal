<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\HubDocument;
use App\Models\HubPricingDocument;
use App\Models\HubUser;
use App\Repositories\Contracts\HubRepositoryInterface;
use App\Support\CountryCache;
use App\Support\PrivateDisk;
use Illuminate\Http\Request;

class HubController extends Controller
{
    public function __construct(private HubRepositoryInterface $hubs) {}

    public function index(Request $request)
    {
        $perPage     = max(10, min(100, (int) $request->input('per_page', 25)));
        $filters     = array_merge(
            $request->only(['name', 'code', 'address', 'city', 'country']),
            ['hide_inactive' => $request->boolean('hide_inactive', true)]
        );
        $hubs        = $this->hubs->paginate($filters, $perPage);
        $countryFlags = $this->hubs->countryFlags();

        if ($request->ajax()) {
            return response()->json([
                'html'       => view('hub.partials.rows', compact('hubs', 'countryFlags'))->render(),
                'pagination' => (string) $hubs->links(),
                'total'      => $hubs->total(),
            ]);
        }

        $countries = $this->hubs->distinctCountries();

        return view('hub.index', compact('hubs', 'countries', 'countryFlags'));
    }

    public function create()
    {
        $countries = CountryCache::activeRaw();
        return view('hub.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hub_name'               => 'required|string|max:255',
            'company_id'             => 'nullable|string|max:255',
            'customer_number_fm'     => 'nullable|string|max:255',
            'code'                   => 'nullable|string|max:255',
            'code_description'       => 'nullable|string|max:255',
            'phone_number'           => 'nullable|string|max:255',
            'contact_person'         => 'required|string|max:255',
            'email'                  => ['nullable', 'string', 'max:255', $this->multipleEmailsValidator()],
            'is_gts_company'         => 'nullable|boolean',
            'remarks'                => 'nullable|string',
            'special_considerations' => 'nullable|string',
            'show_pre_alert'         => 'nullable|boolean',
            'hub_address'            => 'nullable|string',
            'city'                   => 'nullable|string|max:255',
            'district_state'         => 'nullable|string|max:255',
            'zip_code'               => 'nullable|string|max:255',
            'country'                => 'nullable|string|max:255',
            'port_code'              => 'nullable|string|max:255',
            'office_address'         => 'nullable|string',
            'office_city'            => 'nullable|string|max:255',
            'office_district_state'  => 'nullable|string|max:255',
            'office_zip_code'        => 'nullable|string|max:255',
            'office_country'         => 'nullable|string|max:255',
            'eori_number'            => 'nullable|string|max:255',
            'un_locode'              => 'nullable|string|max:255',
            'hide_in_portal'         => 'nullable|boolean',
            'portal_remarks'         => 'nullable|string',
            'portal_email'           => ['nullable', 'string', 'max:255', $this->multipleEmailsValidator()],
        ]);

        $validated['is_gts_company'] = $request->has('is_gts_company');
        $validated['show_pre_alert'] = $request->has('show_pre_alert');
        $validated['hide_in_portal'] = $request->has('hide_in_portal');

        $this->hubs->create($validated);

        return redirect()->route('hub.index')->with('success', 'Hub created successfully.');
    }

    public function show($id)
    {
        $hub       = $this->hubs->findWithRelations((int) $id, ['documents', 'pricingDocuments', 'creator', 'updater']);
        $countries = CountryCache::activeRaw();
        return view('hub.show', compact('hub', 'countries'));
    }

    public function update(Request $request, $id)
    {
        $hub       = $this->hubs->findOrFail((int) $id);
        $activeTab = $this->resolveActiveTab($request);

        try {
            $validated = $request->validate([
                'hub_name'               => 'required|string|max:255',
                'company_id'             => 'nullable|string|max:255',
                'customer_number_fm'     => 'nullable|string|max:255',
                'code'                   => 'nullable|string|max:255',
                'code_description'       => 'nullable|string|max:255',
                'phone_number'           => 'nullable|string|max:255',
                'contact_person'         => 'required|string|max:255',
                'email'                  => ['nullable', 'string', 'max:255', $this->multipleEmailsValidator()],
                'is_gts_company'         => 'nullable|boolean',
                'remarks'                => 'nullable|string',
                'special_considerations' => 'nullable|string',
                'show_pre_alert'         => 'nullable|boolean',
                'hub_address'            => 'nullable|string',
                'city'                   => 'nullable|string|max:255',
                'district_state'         => 'nullable|string|max:255',
                'zip_code'               => 'nullable|string|max:255',
                'country'                => 'nullable|string|max:255',
                'port_code'              => 'nullable|string|max:255',
                'office_address'         => 'nullable|string',
                'office_city'            => 'nullable|string|max:255',
                'office_district_state'  => 'nullable|string|max:255',
                'office_zip_code'        => 'nullable|string|max:255',
                'office_country'         => 'nullable|string|max:255',
                'eori_number'            => 'nullable|string|max:255',
                'un_locode'              => 'nullable|string|max:255',
                'hide_in_portal'         => 'nullable|boolean',
                'portal_remarks'         => 'nullable|string',
                'portal_email'           => ['nullable', 'string', 'max:255', $this->multipleEmailsValidator()],
                'invoicing_name'         => 'nullable|string|max:255',
                'invoicing_address'      => 'nullable|string',
                'invoicing_city'         => 'nullable|string|max:255',
                'invoicing_district'     => 'nullable|string|max:255',
                'invoicing_zip'          => 'nullable|string|max:255',
                'billing_country'        => 'nullable|string|max:255',
                'emails_for_invoicing'   => 'nullable|string',
                'emails_for_invoicing_cc'=> 'nullable|string',
                'vat_number'             => 'nullable|string|max:255',
                'invoicing_frequency'    => 'nullable|string|max:255',
                'billing_currency_outgoing'  => 'nullable|string|max:255',
                'payment_terms_outgoing'     => 'nullable|string|max:255',
                'billing_currency_incoming'  => 'nullable|string|max:255',
                'payment_terms_incoming'     => 'nullable|string|max:255',
                'agreement_type'         => 'nullable|string|max:255',
                'rebate_percentage'      => 'nullable|numeric|min:0|max:100',
                'export_services'        => 'nullable|array',
                'import_services'        => 'nullable|array',
                'export_emails'          => 'nullable|array',
                'import_emails'          => 'nullable|array',
                'stock_item_changed_emails' => 'nullable|string',
                'quote_requests_emails'  => 'nullable|string',
                'coc_signed'             => 'nullable|boolean',
                'sop_implemented'        => 'nullable|boolean',
                'coc_signed_date'        => 'nullable|date_format:d.m.Y',
                'responsible_manager'    => 'nullable|string|max:255',
                'scan_gun_login'         => 'nullable|string|max:255',
                'scan_gun_password'      => 'nullable|string|max:255',
                'agreement_start_date'   => 'nullable|date_format:d.m.Y',
                'agreement_expiry_date'  => 'nullable|date_format:d.m.Y',
                'minimal_cbm'            => 'nullable|numeric|min:0',
                'minimal_weight'         => 'nullable|numeric|min:0',
                'free_storage_days'      => 'nullable|integer|min:0',
                'cbm_charge_usd'         => 'nullable|numeric|min:0',
                'agreement_implemented'              => 'nullable|boolean',
                'scangun_photo_taking'               => 'nullable|boolean',
                'scangun_detailed_shipment_out'      => 'nullable|boolean',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $e->redirectTo(route('hub.show', $id) . '#' . $activeTab);
            throw $e;
        }

        $validated['is_gts_company']                   = $request->has('is_gts_company');
        $validated['show_pre_alert']                   = $request->has('show_pre_alert');
        $validated['hide_in_portal']                   = $request->has('hide_in_portal');
        $validated['coc_signed']                       = $request->has('coc_signed');
        $validated['sop_implemented']                  = $request->has('sop_implemented');
        $validated['agreement_implemented']            = $request->has('agreement_implemented');
        $validated['scangun_photo_taking']             = $request->has('scangun_photo_taking');
        $validated['scangun_detailed_shipment_out']    = $request->has('scangun_detailed_shipment_out');

        if (! $request->has('set_new_password')) {
            unset($validated['scan_gun_password']);
        }

        foreach (['coc_signed_date', 'agreement_start_date', 'agreement_expiry_date'] as $dateField) {
            if ($request->filled($dateField)) {
                try {
                    $validated[$dateField] = \Carbon\Carbon::createFromFormat('d.m.Y', $request->input($dateField))->format('Y-m-d');
                } catch (\Exception) {
                    $validated[$dateField] = null;
                }
            } else {
                $validated[$dateField] = null;
            }
        }

        $this->hubs->update($hub, $validated);

        return redirect()
            ->route('hub.show', $id)
            ->with('success', 'Hub updated successfully.')
            ->withFragment($activeTab);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated  = $request->validate(['status' => ['required', 'in:active,inactive']]);
        $hub        = $this->hubs->findOrFail((int) $id);
        $isInactive = $validated['status'] === 'inactive';

        $this->hubs->updateStatus($hub, $isInactive);

        return response()->json([
            'success'     => true,
            'status'      => $isInactive ? 'Inactive' : 'Active',
            'is_inactive' => $isInactive,
            'message'     => $hub->hub_name . ' is now ' . ($isInactive ? 'inactive.' : 'active.'),
        ]);
    }

    public function destroy($id)
    {
        try {
            $this->hubs->delete((int) $id);
            return response()->json(['success' => true, 'message' => 'Hub deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting hub.'], 500);
        }
    }

    public function uploadDocument(Request $request, $id)
    {
        $request->validate([
            'file'          => 'required|file|mimes:pdf,doc,docx,jpg,png,xlsx,xls|max:5120',
            'document_type' => 'required|string|in:sop,pricing',
        ]);

        $hub  = $this->hubs->findOrFail((int) $id);
        $file = $request->file('file');
        $type = $request->document_type;

        $originalName = $file->getClientOriginalName();
        $fileName     = time() . '_' . $originalName;
        $filePath     = $file->storeAs('hubs/' . $id . '/' . $type, $fileName, 'private');

        $document = $type === 'pricing'
            ? $hub->pricingDocuments()->create(['file_name' => $originalName, 'file_path' => $filePath, 'file_type' => $file->getClientOriginalExtension()])
            : $hub->documents()->create(['file_name' => $originalName, 'file_path' => $filePath, 'file_type' => $file->getClientOriginalExtension()]);

        return response()->json([
            'success'  => true,
            'document' => [
                'id'          => $document->id,
                'name'        => $document->file_name,
                'url'         => $document->fileUrl(),
                'uploaded_at' => $document->created_at->format('d.m.Y H:i'),
            ],
        ]);
    }

    public function showDocument($type, $docId)
    {
        $document = $type === 'pricing'
            ? HubPricingDocument::findOrFail($docId)
            : HubDocument::findOrFail($docId);

        return PrivateDisk::downloadResponse((string) $document->file_path, (string) $document->file_name);
    }

    public function deleteDocument(Request $request, $docId)
    {
        $type     = $request->input('type', 'sop');
        $document = $type === 'pricing'
            ? HubPricingDocument::findOrFail($docId)
            : HubDocument::findOrFail($docId);

        if (PrivateDisk::exists($document->file_path)) {
            PrivateDisk::delete($document->file_path);
        }
        $document->delete();

        return response()->json(['success' => true]);
    }

    public function createContact($hubId)
    {
        $hub = $this->hubs->findOrFail((int) $hubId);
        return view('hub.contacts.create', compact('hub'));
    }

    public function storeContact(Request $request, $hubId)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'nullable|email|max:255',
            'phone_number'    => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'is_main_contact' => 'nullable|boolean',
        ]);

        $hub = $this->hubs->findOrFail((int) $hubId);
        $hub->contacts()->create([
            'name'            => $request->name,
            'email'           => $request->email,
            'phone_number'    => $request->phone_number,
            'description'     => $request->description,
            'is_main_contact' => $request->has('is_main_contact'),
        ]);

        return redirect()->route('hub.show', $hubId)->with('success', 'Contact added successfully.')->withFragment('contacts');
    }

    public function editContact($hubId, $contactId)
    {
        $hub     = $this->hubs->findOrFail((int) $hubId);
        $contact = Contact::findOrFail($contactId);
        return view('hub.contacts.edit', compact('hub', 'contact'));
    }

    public function updateContact(Request $request, $hubId, $contactId)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'nullable|email|max:255',
            'phone_number'    => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'is_main_contact' => 'nullable|boolean',
        ]);

        $contact = Contact::findOrFail($contactId);
        $contact->update([
            'name'            => $request->name,
            'email'           => $request->email,
            'phone_number'    => $request->phone_number,
            'description'     => $request->description,
            'is_main_contact' => $request->has('is_main_contact'),
        ]);

        return redirect()->route('hub.show', $hubId)->with('success', 'Contact updated successfully.')->withFragment('contacts');
    }

    public function createUser($hubId)
    {
        $hub = $this->hubs->findOrFail((int) $hubId);
        return view('hub.users.create', compact('hub'));
    }

    public function storeUser(Request $request, $hubId)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'nullable|email|max:255',
            'phone_number'     => 'nullable|string|max:255',
            'show_in_scan_gun' => 'nullable|boolean',
        ]);

        $hub = $this->hubs->findOrFail((int) $hubId);
        $hub->hubUsers()->create([
            'name'             => $request->name,
            'email'            => $request->email,
            'phone_number'     => $request->phone_number,
            'show_in_scan_gun' => $request->has('show_in_scan_gun'),
        ]);

        return redirect()->route('hub.show', $hubId)->with('success', 'Hub User added successfully.')->withFragment('hub-users');
    }

    public function editUser($hubId, $userId)
    {
        $hub  = $this->hubs->findOrFail((int) $hubId);
        $user = HubUser::findOrFail($userId);
        return view('hub.users.edit', compact('hub', 'user'));
    }

    public function updateUser(Request $request, $hubId, $userId)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'nullable|email|max:255',
            'phone_number'     => 'nullable|string|max:255',
            'show_in_scan_gun' => 'nullable|boolean',
        ]);

        HubUser::findOrFail($userId)->update([
            'name'             => $request->name,
            'email'            => $request->email,
            'phone_number'     => $request->phone_number,
            'show_in_scan_gun' => $request->has('show_in_scan_gun'),
        ]);

        return redirect()->route('hub.show', $hubId)->with('success', 'Hub User updated successfully.')->withFragment('hub-users');
    }

    private function resolveActiveTab(Request $request): string
    {
        $allowed = ['hub-details', 'billing-details', 'sop', 'pricing', 'hub-users', 'contacts', 'email-settings', 'scan-gun'];
        $tab     = (string) $request->input('active_tab', 'hub-details');
        return in_array($tab, $allowed, true) ? $tab : 'hub-details';
    }

    private function multipleEmailsValidator(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if ($value === null || $value === '') {
                return;
            }
            $emails = preg_split('/\s*[,;]\s*/', (string) $value, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($emails as $email) {
                if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $fail('Each email address must be valid.');
                    return;
                }
            }
        };
    }
}
