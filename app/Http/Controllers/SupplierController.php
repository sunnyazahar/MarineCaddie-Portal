<?php

namespace App\Http\Controllers;

use App\Support\CountryCache;
use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $perPage = max(10, min(100, (int) $request->input('per_page', 25)));
        $searchLike = \App\Support\ListSearch::contains($search);

        $suppliers = Supplier::query()
            ->with('country')
            ->when($searchLike, function ($query, $pattern) {
                $query->where(function ($sub) use ($pattern) {
                    $sub->where('supplier_name', 'like', $pattern)
                        ->orWhere('supplier_address', 'like', $pattern)
                        ->orWhere('city', 'like', $pattern)
                        ->orWhere('email', 'like', $pattern)
                        ->orWhere('phone_number', 'like', $pattern)
                        ->orWhere('contact_person', 'like', $pattern)
                        ->orWhereHas('country', fn ($country) => $country->where('name', 'like', $pattern));
                });
            })
            ->orderBy('supplier_name')
            ->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('Suppliers.partials.rows', compact('suppliers'))->render(),
                'pagination' => (string) $suppliers->links(),
                'total' => $suppliers->total(),
            ]);
        }

        return view('Suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $countries = CountryCache::active();
        $currencies = ['USD', 'EUR', 'GBP', 'JPY', 'CNY', 'AED', 'SGD'];
        return view('Suppliers.create', compact('countries', 'currencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:255',
            'email' => ['nullable', 'string', 'max:255', $this->multipleEmailsValidator()],
            'phone_number' => 'nullable|string|max:255',
            'contact_person' => 'required|string|max:255',
        ]);

        $supplier = Supplier::create($request->validated());
        $supplier->load('country');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Supplier created successfully.',
                'supplier' => [
                    'id' => $supplier->id,
                    'supplier_name' => $supplier->supplier_name,
                    'supplier_address' => $supplier->supplier_address,
                    'city' => $supplier->city,
                    'country' => $supplier->country?->name,
                ],
            ]);
        }

        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function edit($id)
    {
        $supplier = Supplier::with(['creator', 'updater'])->findOrFail($id);
        $countries = CountryCache::active();
        $currencies = ['USD', 'EUR', 'GBP', 'JPY', 'CNY', 'AED', 'SGD'];
        return view('Suppliers.edit', compact('supplier', 'countries', 'currencies'));
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $activeTab = $this->resolveActiveTab($request);

        try {
            $request->validate([
                'supplier_name' => 'required|string|max:255',
                'email' => ['nullable', 'string', 'max:255', $this->multipleEmailsValidator()],
                'phone_number' => 'nullable|string|max:255',
                'contact_person' => 'required|string|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $e->redirectTo(route('suppliers.edit', $id) . '#' . $activeTab);
            throw $e;
        }

        $supplier->update($request->validated());

        return redirect()
            ->route('suppliers.edit', $id)
            ->with('success', 'Supplier updated successfully.')
            ->withFragment($activeTab);
    }

    public function destroy($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->delete();
            return response()->json(['success' => true, 'message' => 'Supplier deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting supplier.'], 500);
        }
    }

    // Supplier Contact Methods
    public function createContact($supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        return view('Suppliers.contacts.create', compact('supplier'));
    }

    public function storeContact(Request $request, $supplierId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_main_contact' => 'nullable|boolean',
        ]);

        $supplier = Supplier::findOrFail($supplierId);
        
        $supplier->contacts()->create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'description' => $request->description,
            'is_main_contact' => $request->has('is_main_contact'),
        ]);

        return redirect()
            ->route('suppliers.edit', $supplierId)
            ->with('success', 'Contact added successfully.')
            ->withFragment('contacts');
    }

    public function editContact($supplierId, $contactId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        $contact = \App\Models\Contact::findOrFail($contactId);
        return view('Suppliers.contacts.edit', compact('supplier', 'contact'));
    }

    public function updateContact(Request $request, $supplierId, $contactId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_main_contact' => 'nullable|boolean',
        ]);

        $contact = \App\Models\Contact::findOrFail($contactId);
        
        $contact->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'description' => $request->description,
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
        $tab = (string) $request->input('active_tab', 'supplier-details');

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
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $fail('Each email address must be valid.');
                    return;
                }
            }
        };
    }
}
