<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use App\Repositories\Contracts\AgentDocumentRepositoryInterface;
use App\Repositories\Contracts\AgentRepositoryInterface;
use App\Repositories\Contracts\AgentUserRepositoryInterface;
use App\Repositories\Contracts\ContactRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\UserNotificationService;
use App\Support\CountryCache;
use App\Support\PrivateDisk;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function __construct(
        private AgentRepositoryInterface $agents,
        private UserRepositoryInterface $users,
        private ContactRepositoryInterface $contacts,
        private AgentUserRepositoryInterface $agentUsers,
        private AgentDocumentRepositoryInterface $agentDocuments,
    ) {}

    public function index(Request $request)
    {
        $filters = [
            'name'          => $request->input('name', ''),
            'code'          => $request->input('code', ''),
            'address'       => $request->input('address', ''),
            'city'          => $request->input('city', ''),
            'country'       => $request->input('country', []),
            'type'          => $request->input('type', []),
            'hide_inactive' => $request->boolean('hide_inactive', false),
        ];
        $perPage = max(10, min(100, (int) $request->input('per_page', 25)));

        $agents = $this->agents->paginate($filters, $perPage);

        if ($request->ajax()) {
            return response()->json([
                'html'       => view('Agents.partials.rows', compact('agents'))->render(),
                'pagination' => (string) $agents->links(),
                'total'      => $agents->total(),
            ]);
        }

        $countries  = $this->agents->distinctCountries();
        $agentTypes = $this->agents->distinctTypes();

        return view('Agents.index', compact('agents', 'countries', 'agentTypes'));
    }

    public function create()
    {
        $countries = CountryCache::active();
        return view('Agents.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'agent_name'          => 'required|string|max:255',
            'company_id'          => 'nullable|string|max:255',
            'code'                => 'nullable|string|max:255',
            'code_description'    => 'nullable|string|max:255',
            'phone'               => 'nullable|string|max:255',
            'contact_person'      => 'required|string|max:255',
            'email'               => ['nullable', 'string', 'max:255', $this->multipleEmailsValidator()],
            'remarks'             => 'nullable|string',
            'special_considerations' => 'nullable|string',
            'show_pre_alert'      => 'nullable|boolean',
            'agent_address'       => 'nullable|string',
            'city'                => 'nullable|string|max:255',
            'district_state'      => 'nullable|string|max:255',
            'zip_code'            => 'nullable|string|max:255',
            'country_id'          => 'nullable|exists:countries,id',
            'port_code'           => 'nullable|string|max:255',
            'office_address'      => 'nullable|string',
            'office_city'         => 'nullable|string|max:255',
            'office_district_state' => 'nullable|string|max:255',
            'office_zip_code'     => 'nullable|string|max:255',
            'office_country_id'   => 'nullable|exists:countries,id',
            'eori_number'         => 'nullable|string|max:255',
            'un_locode'           => 'nullable|string|max:255',
            'agent_type'          => 'nullable|string|max:255',
        ]);

        $validated['show_pre_alert'] = $request->has('show_pre_alert');

        $this->agents->create($validated);

        return redirect()->route('agents.index')->with('success', 'Agent created successfully.');
    }

    public function edit($id)
    {
        $agent     = $this->agents->findWithRelations((int) $id, ['creator', 'updater']);
        $countries = CountryCache::active();
        return view('Agents.edit', compact('agent', 'countries'));
    }

    public function update(Request $request, $id)
    {
        $agent     = $this->agents->findOrFail((int) $id);
        $activeTab = $this->resolveActiveTab($request);

        try {
            $validated = $request->validate([
                'agent_name'          => 'required|string|max:255',
                'company_id'          => 'nullable|string|max:255',
                'code'                => 'nullable|string|max:255',
                'code_description'    => 'nullable|string|max:255',
                'phone'               => 'nullable|string|max:255',
                'contact_person'      => 'required|string|max:255',
                'email'               => ['nullable', 'string', 'max:255', $this->multipleEmailsValidator()],
                'remarks'             => 'nullable|string',
                'special_considerations' => 'nullable|string',
                'show_pre_alert'      => 'nullable|boolean',
                'agent_address'       => 'nullable|string',
                'city'                => 'nullable|string|max:255',
                'district_state'      => 'nullable|string|max:255',
                'zip_code'            => 'nullable|string|max:255',
                'country_id'          => 'nullable|exists:countries,id',
                'port_code'           => 'nullable|string|max:255',
                'office_address'      => 'nullable|string',
                'office_city'         => 'nullable|string|max:255',
                'office_district_state' => 'nullable|string|max:255',
                'office_zip_code'     => 'nullable|string|max:255',
                'office_country_id'   => 'nullable|exists:countries,id',
                'eori_number'         => 'nullable|string|max:255',
                'un_locode'           => 'nullable|string|max:255',
                'agent_type'          => 'nullable|string|max:255',

                // Billing
                'invoicing_name'        => 'nullable|string|max:255',
                'billing_address'       => 'nullable|string',
                'billing_city'          => 'nullable|string|max:255',
                'billing_district_state'=> 'nullable|string|max:255',
                'billing_zip_code'      => 'nullable|string|max:255',
                'billing_country_id'    => 'nullable|exists:countries,id',
                'invoicing_emails'      => 'nullable|string|max:255',
                'invoicing_emails_cc'   => 'nullable|string|max:255',
                'vat_number'            => 'nullable|string|max:255',
                'invoicing_frequency'   => 'nullable|string|max:255',
                'rebate_percentage'     => 'nullable|numeric',
                'outgoing_currency'     => 'nullable|string|max:255',
                'outgoing_payment_terms'=> 'nullable|string|max:255',
                'incoming_currency'     => 'nullable|string|max:255',
                'incoming_payment_terms'=> 'nullable|string|max:255',

                // SOP
                'coc_signed_date'     => 'nullable|date',
                'responsible_manager' => 'nullable|string|max:255',

                // Pricing
                'purchase_rate' => 'nullable|string|max:255',
                'sell_rate'     => 'nullable|string|max:255',
                'profit'        => 'nullable|string|max:255',

                // Email settings
                'export_email_services'     => 'nullable|string',
                'import_email_services'     => 'nullable|string',
                'status_changed_emails'     => 'nullable|string|max:255',
                'stock_item_changed_emails' => 'nullable|string|max:255',
                'quote_requests_emails'     => 'nullable|string|max:255',

                // Scan gun
                'scangun_login'    => 'nullable|string|max:255',
                'scangun_password' => 'nullable|string|max:255',

                // Files & arrays
                'billing_exceptions'   => 'nullable|array',
                'sop_documents'        => 'nullable|array',
                'sop_documents.*'      => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp|max:10240',
                'pricing_documents'    => 'nullable|array',
                'pricing_documents.*'  => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp|max:10240',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $e->redirectTo(route('agents.edit', $id) . '#' . $activeTab);
            throw $e;
        }

        $validated['show_pre_alert']                      = $request->has('show_pre_alert');
        $validated['applies_to_rebate']                   = $request->has('applies_to_rebate');
        $validated['coc_signed']                          = $request->has('coc_signed');
        $validated['sop_implemented']                     = $request->has('sop_implemented');
        $validated['calculate_sell_rates']                = $request->has('calculate_sell_rates');
        $validated['scangun_enable_picture']              = $request->has('scangun_enable_picture');
        $validated['scangun_enable_detailed_shipment']    = $request->has('scangun_enable_detailed_shipment');

        $this->agents->update($agent, $validated);

        // Billing exceptions — clear old, insert new
        $agent->billingExceptions()->delete();
        if ($request->has('billing_exceptions') && is_array($request->billing_exceptions)) {
            $exceptions = $request->billing_exceptions;
            if (isset($exceptions['office']) && is_array($exceptions['office'])) {
                foreach ($exceptions['office'] as $i => $office) {
                    if ($office || ($exceptions['invoice_to_agent'][$i] ?? null) || ($exceptions['currency'][$i] ?? null) || ($exceptions['payment_terms'][$i] ?? null)) {
                        $agent->billingExceptions()->create([
                            'office'           => $office,
                            'invoice_to_agent' => $exceptions['invoice_to_agent'][$i] ?? null,
                            'currency'         => $exceptions['currency'][$i] ?? null,
                            'payment_terms'    => $exceptions['payment_terms'][$i] ?? null,
                        ]);
                    }
                }
            }
        }

        // SOP file uploads
        if ($request->hasFile('sop_documents')) {
            foreach ($request->file('sop_documents') as $file) {
                $agent->documents()->create([
                    'section'   => 'sop',
                    'filename'  => $file->getClientOriginalName(),
                    'file_path' => $file->store('agent_documents', 'private'),
                ]);
            }
        }

        // Pricing file uploads
        if ($request->hasFile('pricing_documents')) {
            foreach ($request->file('pricing_documents') as $file) {
                $agent->documents()->create([
                    'section'   => 'pricing',
                    'filename'  => $file->getClientOriginalName(),
                    'file_path' => $file->store('agent_documents', 'private'),
                ]);
            }
        }

        return redirect()
            ->route('agents.edit', $id)
            ->with('success', 'Agent updated successfully.')
            ->withFragment($activeTab);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:active,inactive'],
        ]);

        $agent    = $this->agents->findOrFail((int) $id);
        $isActive = $validated['status'] === 'active';

        $this->agents->updateStatus($agent, $isActive);

        if (! $isActive) {
            $actor   = auth()->user()?->name ?? 'System';
            $message = 'Agent ' . $agent->agent_name . ' has been blocked by ' . $actor . '. Shipments can be created, but it is not possible to send manifests, pre-alerts or finalize shipments until the blocking is removed.';
            $linkUrl = route('agents.edit', $agent->id);

            $recipientIds = $this->users->notificationRecipientsForAgent(
                (int) $agent->id,
                auth()->id()
            );

            $notifier = app(UserNotificationService::class);
            foreach ($recipientIds as $userId) {
                $notifier->notify(
                    (int) $userId,
                    $message,
                    UserNotification::CATEGORY_OTHER,
                    $agent->agent_name,
                    $linkUrl,
                    'other',
                    $agent
                );
            }
        }

        return response()->json([
            'success'     => true,
            'status'      => $isActive ? 'Active' : 'Inactive',
            'is_inactive' => ! $isActive,
            'message'     => $agent->agent_name . ' is now ' . ($isActive ? 'active.' : 'inactive.'),
        ]);
    }

    public function destroy($id)
    {
        try {
            $this->agents->deleteById((int) $id);
            return response()->json(['success' => true, 'message' => 'Agent deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting agent.'], 500);
        }
    }

    public function deleteDocument($id)
    {
        $document = $this->agentDocuments->findOrFail((int) $id);
        $agentId  = $document->agent_id;
        $tab      = $document->section === 'pricing' ? 'pricing' : 'sop';

        PrivateDisk::delete($document->file_path);
        $document->delete();

        return redirect()
            ->route('agents.edit', $agentId)
            ->with('success', 'Document deleted successfully.')
            ->withFragment($tab);
    }

    public function showDocument($agentId, $docId)
    {
        $document = $this->agentDocuments->findByAgentOrFail((int) $agentId, (int) $docId);
        $filename = $document->filename ?: basename($document->file_path);

        return PrivateDisk::downloadResponse((string) $document->file_path, (string) $filename);
    }

    public function storeContact(Request $request, $agent_id)
    {
        $request->validate([
            'name'  => 'required',
            'email' => 'required|email',
        ]);

        $this->contacts->create([
            'agent_id'        => $agent_id,
            'name'            => $request->name,
            'email'           => $request->email,
            'phone_number'    => $request->phone_number,
            'description'     => $request->description,
            'is_main_contact' => $request->has('is_main_contact') ? 1 : 0,
        ]);

        return redirect()
            ->route('agents.edit', $agent_id)
            ->with('success', 'Contact added successfully.')
            ->withFragment('contacts');
    }

    public function editContact($id)
    {
        $contact = $this->contacts->findOrFail((int) $id, ['creator', 'updater']);
        return view('Agents.contacts.edit', compact('contact'));
    }

    public function updateContact(Request $request, $id)
    {
        $contact = $this->contacts->findOrFail((int) $id);

        $request->validate([
            'name'  => 'required',
            'email' => 'required|email',
        ]);

        $this->contacts->update($contact, [
            'name'            => $request->name,
            'email'           => $request->email,
            'phone_number'    => $request->phone_number,
            'description'     => $request->description,
            'is_main_contact' => $request->has('is_main_contact') ? 1 : 0,
        ]);

        return redirect()
            ->route('agents.edit', $contact->agent_id)
            ->with('success', 'Contact updated successfully.')
            ->withFragment('contacts');
    }

    public function destroyContact($id)
    {
        $contact  = $this->contacts->findOrFail((int) $id);
        $agent_id = $contact->agent_id;
        $this->contacts->deleteById((int) $id);

        return redirect()
            ->route('agents.edit', $agent_id)
            ->with('success', 'Contact deleted successfully.')
            ->withFragment('contacts');
    }

    public function storeUser(Request $request, $agent_id)
    {
        $request->validate([
            'name'  => 'required',
            'email' => 'required|email',
        ]);

        $this->agentUsers->create([
            'agent_id'     => $agent_id,
            'name'         => $request->name,
            'email'        => $request->email,
            'phone_number' => $request->phone_number,
            'description'  => $request->description,
        ]);

        return redirect()
            ->route('agents.edit', $agent_id)
            ->with('success', 'User added successfully.')
            ->withFragment('agent-users');
    }

    public function editUser($id)
    {
        $user = $this->agentUsers->findOrFail((int) $id, ['creator', 'updater']);
        return view('Agents.Users.edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = $this->agentUsers->findOrFail((int) $id);

        $request->validate([
            'name'  => 'required',
            'email' => 'required|email',
        ]);

        $this->agentUsers->update($user, [
            'name'         => $request->name,
            'email'        => $request->email,
            'phone_number' => $request->phone_number,
            'description'  => $request->description,
        ]);

        return redirect()
            ->route('agents.edit', $user->agent_id)
            ->with('success', 'User updated successfully.')
            ->withFragment('agent-users');
    }

    public function destroyUser($id)
    {
        $user     = $this->agentUsers->findOrFail((int) $id);
        $agent_id = $user->agent_id;
        $this->agentUsers->deleteById((int) $id);

        return redirect()
            ->route('agents.edit', $agent_id)
            ->with('success', 'User deleted successfully.')
            ->withFragment('agent-users');
    }

    private function resolveActiveTab(Request $request): string
    {
        $allowed = [
            'agent-details', 'billing-details', 'sop', 'pricing',
            'agent-users', 'contacts', 'email-settings', 'scan-gun',
        ];

        $tab = (string) $request->input('active_tab', 'agent-details');

        return in_array($tab, $allowed, true) ? $tab : 'agent-details';
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
