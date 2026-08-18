<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Country;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function index(Request $request)
    {
        $name = trim((string) $request->input('name', ''));
        $code = trim((string) $request->input('code', ''));
        $address = trim((string) $request->input('address', ''));
        $city = trim((string) $request->input('city', ''));
        $countriesFilter = array_values(array_filter((array) $request->input('country', [])));
        $typesFilter = array_values(array_filter((array) $request->input('type', [])));
        $hideInactive = $request->boolean('hide_inactive', true);
        $perPage = max(10, min(100, (int) $request->input('per_page', 25)));

        $agents = Agent::query()
            ->with('country')
            ->when($name !== '', fn ($query) => $query->where('agent_name', 'like', '%' . $name . '%'))
            ->when($code !== '', fn ($query) => $query->where('code', 'like', '%' . $code . '%'))
            ->when($city !== '', fn ($query) => $query->where('city', 'like', '%' . $city . '%'))
            ->when($address !== '', function ($query) use ($address) {
                $query->where(function ($sub) use ($address) {
                    $sub->where('agent_address', 'like', '%' . $address . '%')
                        ->orWhere('office_address', 'like', '%' . $address . '%')
                        ->orWhere('district_state', 'like', '%' . $address . '%')
                        ->orWhere('zip_code', 'like', '%' . $address . '%');
                });
            })
            ->when($countriesFilter, fn ($query) => $query->whereHas('country', fn ($sub) => $sub->whereIn('name', $countriesFilter)))
            ->when($typesFilter, fn ($query) => $query->whereIn('agent_type', $typesFilter))
            ->when($hideInactive, fn ($query) => $query->where('is_active', true))
            ->orderBy('agent_name')
            ->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('Agents.partials.rows', compact('agents'))->render(),
                'pagination' => (string) $agents->links(),
                'total' => $agents->total(),
            ]);
        }

        $countries = Agent::query()
            ->join('countries', 'countries.id', '=', 'agents.country_id')
            ->whereNotNull('countries.name')
            ->distinct()
            ->orderBy('countries.name')
            ->pluck('countries.name')
            ->values();

        $agentTypes = Agent::query()
            ->whereNotNull('agent_type')
            ->where('agent_type', '!=', '')
            ->distinct()
            ->orderBy('agent_type')
            ->pluck('agent_type')
            ->values();

        return view('Agents.index', compact('agents', 'countries', 'agentTypes'));
    }

    public function create()
    {
        $countries = Country::where('is_active', true)->orderBy('name')->get();
        return view('Agents.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'agent_name' => 'required|string|max:255',
            'company_id' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:255',
            'code_description' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => ['nullable', 'string', 'max:255', $this->multipleEmailsValidator()],
            'remarks' => 'nullable|string',
            'special_considerations' => 'nullable|string',
            'show_pre_alert' => 'nullable|boolean',
            'agent_address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'district_state' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'port_code' => 'nullable|string|max:255',
            'office_address' => 'nullable|string',
            'office_city' => 'nullable|string|max:255',
            'office_district_state' => 'nullable|string|max:255',
            'office_zip_code' => 'nullable|string|max:255',
            'office_country_id' => 'nullable|exists:countries,id',
            'eori_number' => 'nullable|string|max:255',
            'un_locode' => 'nullable|string|max:255',
            'agent_type' => 'nullable|string|max:255',
        ]);

        $validated['show_pre_alert'] = $request->has('show_pre_alert');

        Agent::create($validated);

        return redirect()->route('agents.index')->with('success', 'Agent created successfully.');
    }

    public function edit($id)
    {
        $agent = Agent::with(['creator', 'updater'])->findOrFail($id);
        $countries = Country::where('is_active', true)->orderBy('name')->get();
        return view('Agents.edit', compact('agent', 'countries'));
    }

    public function update(Request $request, $id)
    {
        $agent = Agent::findOrFail($id);
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
                'invoicing_name'      => 'nullable|string|max:255',
                'billing_address'     => 'nullable|string',
                'billing_city'        => 'nullable|string|max:255',
                'billing_district_state'=> 'nullable|string|max:255',
                'billing_zip_code'    => 'nullable|string|max:255',
                'billing_country_id'  => 'nullable|exists:countries,id',
                'invoicing_emails'    => 'nullable|string|max:255',
                'invoicing_emails_cc' => 'nullable|string|max:255',
                'vat_number'          => 'nullable|string|max:255',
                'invoicing_frequency' => 'nullable|string|max:255',
                'rebate_percentage'   => 'nullable|numeric',
                'outgoing_currency'   => 'nullable|string|max:255',
                'outgoing_payment_terms'=> 'nullable|string|max:255',
                'incoming_currency'   => 'nullable|string|max:255',
                'incoming_payment_terms'=> 'nullable|string|max:255',

                // SOP
                'coc_signed_date'     => 'nullable|date',
                'responsible_manager' => 'nullable|string|max:255',

                // Pricing
                'purchase_rate'       => 'nullable|string|max:255',
                'sell_rate'           => 'nullable|string|max:255',
                'profit'              => 'nullable|string|max:255',

                // Email
                'export_email_services'=> 'nullable|string',
                'import_email_services'=> 'nullable|string',
                'status_changed_emails'=> 'nullable|string|max:255',
                'stock_item_changed_emails'=> 'nullable|string|max:255',
                'quote_requests_emails'=> 'nullable|string|max:255',

                // Scan gun
                'scangun_login'       => 'nullable|string|max:255',
                'scangun_password'    => 'nullable|string|max:255',
                
                // Exceptions array
                'billing_exceptions'  => 'nullable|array',
                'sop_documents' => 'nullable|array',
                'sop_documents.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp|max:10240',
                'pricing_documents' => 'nullable|array',
                'pricing_documents.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp|max:10240',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $e->redirectTo(route('agents.edit', $id) . '#' . $activeTab);
            throw $e;
        }

        $validated['show_pre_alert'] = $request->has('show_pre_alert');
        $validated['applies_to_rebate'] = $request->has('applies_to_rebate');
        $validated['coc_signed'] = $request->has('coc_signed');
        $validated['sop_implemented'] = $request->has('sop_implemented');
        $validated['calculate_sell_rates'] = $request->has('calculate_sell_rates');
        $validated['scangun_enable_picture'] = $request->has('scangun_enable_picture');
        $validated['scangun_enable_detailed_shipment'] = $request->has('scangun_enable_detailed_shipment');

        $agent->update($validated);

        // Handle Billing Exceptions
        $agent->billingExceptions()->delete(); // Clear old exceptions
        if ($request->has('billing_exceptions') && is_array($request->billing_exceptions)) {
            $exceptionsData = $request->billing_exceptions;
            
            // Make sure the structure exists (e.g., if there's at least one office)
            if (isset($exceptionsData['office']) && is_array($exceptionsData['office'])) {
                foreach ($exceptionsData['office'] as $index => $office) {
                    // Only save if there's some data
                    if ($office || $exceptionsData['invoice_to_agent'][$index] || $exceptionsData['currency'][$index] || $exceptionsData['payment_terms'][$index]) {
                        $agent->billingExceptions()->create([
                            'office' => $office,
                            'invoice_to_agent' => $exceptionsData['invoice_to_agent'][$index] ?? null,
                            'currency' => $exceptionsData['currency'][$index] ?? null,
                            'payment_terms' => $exceptionsData['payment_terms'][$index] ?? null,
                        ]);
                    }
                }
            }
        }

        // Handle File Uploads for SOP
        if ($request->hasFile('sop_documents')) {
            foreach ($request->file('sop_documents') as $file) {
                $filename = $file->getClientOriginalName();
                $path = $file->store('agent_documents', 'private');
                $agent->documents()->create([
                    'section' => 'sop',
                    'filename' => $filename,
                    'file_path' => $path,
                ]);
            }
        }

        // Handle File Uploads for Pricing
        if ($request->hasFile('pricing_documents')) {
            foreach ($request->file('pricing_documents') as $file) {
                $filename = $file->getClientOriginalName();
                $path = $file->store('agent_documents', 'private');
                $agent->documents()->create([
                    'section' => 'pricing',
                    'filename' => $filename,
                    'file_path' => $path,
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

        $agent = Agent::findOrFail($id);
        $isActive = $validated['status'] === 'active';

        $agent->update([
            'is_active' => $isActive,
        ]);

        if (! $isActive) {
            $actor = auth()->user()?->name ?? 'System';
            $message = 'Agent ' . $agent->agent_name . ' has been blocked by ' . $actor . '. Shipments can be created, but it is not possible to send manifests, pre-alerts or finalize shipments until the blocking is removed.';
            $linkUrl = route('agents.edit', $agent->id);

            $recipientIds = \App\Models\User::query()
                ->where(function ($q) use ($agent) {
                    $q->where('role', 'Admin')
                        ->orWhereHas('agents', fn ($aq) => $aq->where('agents.id', $agent->id));
                })
                ->when(auth()->id(), fn ($q) => $q->where('id', '!=', auth()->id()))
                ->pluck('id');

            $notifier = app(\App\Services\UserNotificationService::class);
            foreach ($recipientIds as $userId) {
                $notifier->notify(
                    (int) $userId,
                    $message,
                    \App\Models\UserNotification::CATEGORY_OTHER,
                    $agent->agent_name,
                    $linkUrl,
                    'other',
                    $agent
                );
            }
        }

        return response()->json([
            'success' => true,
            'status' => $isActive ? 'Active' : 'Inactive',
            'is_inactive' => ! $isActive,
            'message' => $agent->agent_name . ' is now ' . ($isActive ? 'active.' : 'inactive.'),
        ]);
    }

    public function destroy($id)
    {
        try {
            $agent = Agent::findOrFail($id);
            $agent->delete();

            return response()->json([
                'success' => true,
                'message' => 'Agent deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting agent.',
            ], 500);
        }
    }

    public function deleteDocument($id)
    {
        $document = \App\Models\AgentDocument::findOrFail($id);
        $agentId = $document->agent_id;
        $tab = $document->section === 'pricing' ? 'pricing' : 'sop';
        
        // Delete file from storage
        \App\Support\PrivateDisk::delete($document->file_path);
        
        // Delete record from DB
        $document->delete();

        return redirect()
            ->route('agents.edit', $agentId)
            ->with('success', 'Document deleted successfully.')
            ->withFragment($tab);
    }

    public function showDocument($agentId, $docId)
    {
        $document = \App\Models\AgentDocument::where('agent_id', $agentId)->findOrFail($docId);
        $filename = $document->filename ?: basename($document->file_path);

        return \App\Support\PrivateDisk::downloadResponse((string) $document->file_path, (string) $filename);
    }

    public function storeContact(Request $request, $agent_id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        \App\Models\Contact::create([
            'agent_id' => $agent_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'description' => $request->description,
            'is_main_contact' => $request->has('is_main_contact') ? 1 : 0,
        ]);

        return redirect()
            ->route('agents.edit', $agent_id)
            ->with('success', 'Contact added successfully.')
            ->withFragment('contacts');
    }

    public function editContact($id)
    {
        $contact = \App\Models\Contact::with(['creator', 'updater'])->findOrFail($id);
        return view('Agents.contacts.edit', compact('contact'));
    }

    public function updateContact(Request $request, $id)
    {
        $contact = \App\Models\Contact::findOrFail($id);
        
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        $contact->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'description' => $request->description,
            'is_main_contact' => $request->has('is_main_contact') ? 1 : 0,
        ]);

        return redirect()
            ->route('agents.edit', $contact->agent_id)
            ->with('success', 'Contact updated successfully.')
            ->withFragment('contacts');
    }

    public function destroyContact($id)
    {
        $contact = \App\Models\Contact::findOrFail($id);
        $agent_id = $contact->agent_id;
        $contact->delete();

        return redirect()
            ->route('agents.edit', $agent_id)
            ->with('success', 'Contact deleted successfully.')
            ->withFragment('contacts');
    }

    public function storeUser(Request $request, $agent_id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        \App\Models\AgentUser::create([
            'agent_id' => $agent_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('agents.edit', $agent_id)
            ->with('success', 'User added successfully.')
            ->withFragment('agent-users');
    }

    public function editUser($id)
    {
        $user = \App\Models\AgentUser::with(['creator', 'updater'])->findOrFail($id);
        return view('Agents.Users.edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = \App\Models\AgentUser::findOrFail($id);
        
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('agents.edit', $user->agent_id)
            ->with('success', 'User updated successfully.')
            ->withFragment('agent-users');
    }

    public function destroyUser($id)
    {
        $user = \App\Models\AgentUser::findOrFail($id);
        $agent_id = $user->agent_id;
        $user->delete();

        return redirect()
            ->route('agents.edit', $agent_id)
            ->with('success', 'User deleted successfully.')
            ->withFragment('agent-users');
    }

    private function resolveActiveTab(Request $request): string
    {
        $allowed = [
            'agent-details',
            'billing-details',
            'sop',
            'pricing',
            'agent-users',
            'contacts',
            'email-settings',
            'scan-gun',
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
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $fail('Each email address must be valid.');
                    return;
                }
            }
        };
    }
}
