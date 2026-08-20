<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\ContactRepositoryInterface;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Support\CountryCache;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct(
        private SupplierRepositoryInterface $suppliers,
        private ContactRepositoryInterface $contacts,
    ) {}

    public function index(Request $request)
    {
        $perPage   = max(10, min(100, (int) $request->input('per_page', 25)));
        $suppliers = $this->suppliers->paginate(
            ['search' => $request->input('search', '')],
            $perPage
        );

        if ($request->ajax()) {
            return response()->json([
                'html'       => view('Suppliers.partials.rows', compact('suppliers'))->render(),
                'pagination' => (string) $suppliers->links(),
                'total'      => $suppliers->total(),
            ]);
        }

        return view('Suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $countries  = CountryCache::active();
        $currencies = ['USD', 'EUR', 'GBP', 'JPY', 'CNY', 'AED', 'SGD'];
        return view('Suppliers.create', compact('countries', 'currencies'));
    }

    public function store(Request $request)
    {
        $request->validate($this->supplierRules());

        $supplier = $this->suppliers->create($this->supplierPayload($request));
        $supplier->load('country');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Supplier created successfully.',
                'supplier' => [
                    'id'               => $supplier->id,
                    'supplier_name'    => $supplier->supplier_name,
                    'supplier_address' => $supplier->supplier_address,
                    'city'             => $supplier->city,
                    'country'          => $supplier->country?->name,
                ],
            ]);
        }

        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function edit($id)
    {
        $supplier   = $this->suppliers->findWithRelations((int) $id, ['creator', 'updater']);
        $countries  = CountryCache::active();
        $currencies = ['USD', 'EUR', 'GBP', 'JPY', 'CNY', 'AED', 'SGD'];
        return view('Suppliers.edit', compact('supplier', 'countries', 'currencies'));
    }

    public function update(Request $request, $id)
    {
        $supplier  = $this->suppliers->findOrFail((int) $id);
        $activeTab = $this->resolveActiveTab($request);

        try {
            $request->validate($this->supplierRules());
        } catch (\Illuminate\Validation\ValidationException $e) {
            $e->redirectTo(route('suppliers.edit', $id) . '#' . $activeTab);
            throw $e;
        }

        $this->suppliers->update($supplier, $this->supplierPayload($request));

        return redirect()
            ->route('suppliers.edit', $id)
            ->with('success', 'Supplier updated successfully.')
            ->withFragment($activeTab);
    }

    public function destroy($id)
    {
        try {
            $this->suppliers->deleteById((int) $id);
            return response()->json(['success' => true, 'message' => 'Supplier deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting supplier.'], 500);
        }
    }

    public function createContact($supplierId)
    {
        $supplier = $this->suppliers->findOrFail((int) $supplierId);
        return view('Suppliers.contacts.create', compact('supplier'));
    }

    public function storeContact(Request $request, $supplierId)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'nullable|email|max:255',
            'phone_number'    => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'is_main_contact' => 'nullable|boolean',
        ]);

        $supplier = $this->suppliers->findOrFail((int) $supplierId);
        $supplier->contacts()->create([
            'name'            => $request->name,
            'email'           => $request->email,
            'phone_number'    => $request->phone_number,
            'description'     => $request->description,
            'is_main_contact' => $request->has('is_main_contact'),
        ]);

        return redirect()
            ->route('suppliers.edit', $supplierId)
            ->with('success', 'Contact added successfully.')
            ->withFragment('contacts');
    }

    public function editContact($supplierId, $contactId)
    {
        $supplier = $this->suppliers->findOrFail((int) $supplierId);
        $contact  = $this->contacts->findOrFail((int) $contactId);
        return view('Suppliers.contacts.edit', compact('supplier', 'contact'));
    }

    public function updateContact(Request $request, $supplierId, $contactId)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'nullable|email|max:255',
            'phone_number'    => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'is_main_contact' => 'nullable|boolean',
        ]);

        $contact = $this->contacts->findOrFail((int) $contactId);
        $this->contacts->update($contact, [
            'name'            => $request->name,
            'email'           => $request->email,
            'phone_number'    => $request->phone_number,
            'description'     => $request->description,
            'is_main_contact' => $request->has('is_main_contact'),
        ]);

        return redirect()
            ->route('suppliers.edit', $supplierId)
            ->with('success', 'Contact updated successfully.')
            ->withFragment('contacts');
    }

    private function supplierRules(): array
    {
        return [
            'supplier_name'      => 'required|string|max:255',
            'email'              => ['nullable', 'string', 'max:255', $this->multipleEmailsValidator()],
            'phone_number'       => 'nullable|string|max:255',
            'contact_person'     => 'required|string|max:255',
            'country_id'         => 'nullable|exists:countries,id',
            'office_country_id'  => 'nullable|exists:countries,id',
        ];
    }

    private function supplierPayload(Request $request): array
    {
        return $request->only([
            'supplier_name', 'phone_number', 'contact_person', 'email', 'remarks', 'special_considerations',
            'supplier_address', 'city', 'district_state', 'zip_code', 'country_id', 'port_code',
            'office_address', 'office_city', 'office_district_state', 'office_zip_code', 'office_country_id',
            'vat_number', 'eori_number', 'currency', 'un_locode',
        ]);
    }

    private function resolveActiveTab(Request $request): string
    {
        $allowed = ['supplier-details', 'contacts'];
        $tab     = (string) $request->input('active_tab', 'supplier-details');
        return in_array($tab, $allowed, true) ? $tab : 'supplier-details';
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
