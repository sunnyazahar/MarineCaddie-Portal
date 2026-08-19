<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Support\CountryCache;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct(private SupplierRepositoryInterface $suppliers) {}

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
        $request->validate([
            'supplier_name'  => 'required|string|max:255',
            'email'          => ['nullable', 'string', 'max:255', $this->multipleEmailsValidator()],
            'phone_number'   => 'nullable|string|max:255',
            'contact_person' => 'required|string|max:255',
        ]);

        $supplier = $this->suppliers->create($request->validated());
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
            $request->validate([
                'supplier_name'  => 'required|string|max:255',
                'email'          => ['nullable', 'string', 'max:255', $this->multipleEmailsValidator()],
                'phone_number'   => 'nullable|string|max:255',
                'contact_person' => 'required|string|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $e->redirectTo(route('suppliers.edit', $id) . '#' . $activeTab);
            throw $e;
        }

        $this->suppliers->update($supplier, $request->validated());

        return redirect()
            ->route('suppliers.edit', $id)
            ->with('success', 'Supplier updated successfully.')
            ->withFragment($activeTab);
    }

    public function destroy($id)
    {
        try {
            $this->suppliers->delete((int) $id);
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
        $contact  = Contact::findOrFail($contactId);
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

        $contact = Contact::findOrFail($contactId);
        $contact->update([
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
