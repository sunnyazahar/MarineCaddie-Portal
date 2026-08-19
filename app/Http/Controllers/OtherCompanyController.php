<?php

namespace App\Http\Controllers;

use App\Support\CountryCache;
use Illuminate\Http\Request;
use App\Models\OtherCompany;

class OtherCompanyController extends Controller
{
    public function index(Request $request)
    {
        $name = trim((string) $request->input('name', ''));
        $code = trim((string) $request->input('code', ''));
        $address = trim((string) $request->input('address', ''));
        $city = trim((string) $request->input('city', ''));
        $countriesFilter = array_values(array_filter((array) $request->input('country', [])));
        $perPage = max(10, min(100, (int) $request->input('per_page', 25)));

        $nameLike = \App\Support\ListSearch::contains($name);
        $codeLike = \App\Support\ListSearch::contains($code);
        $addressLike = \App\Support\ListSearch::contains($address);
        $cityLike = \App\Support\ListSearch::contains($city);

        $companies = OtherCompany::query()
            ->with('country')
            ->when($nameLike, fn ($query, $pattern) => $query->where('company_name', 'like', $pattern))
            ->when($codeLike, fn ($query, $pattern) => $query->where('code', 'like', $pattern))
            ->when($cityLike, fn ($query, $pattern) => $query->where('city', 'like', $pattern))
            ->when($addressLike, function ($query, $pattern) {
                $query->where(function ($sub) use ($pattern) {
                    $sub->where('street_address', 'like', $pattern)
                        ->orWhere('office_street_address', 'like', $pattern)
                        ->orWhere('district_state', 'like', $pattern)
                        ->orWhere('zip_code', 'like', $pattern);
                });
            })
            ->when($countriesFilter, fn ($query) => $query->whereHas('country', fn ($sub) => $sub->whereIn('name', $countriesFilter)))
            ->orderBy('company_name')
            ->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('Other Companies.partials.rows', compact('companies'))->render(),
                'pagination' => (string) $companies->links(),
                'total' => $companies->total(),
            ]);
        }

        $countries = OtherCompany::query()
            ->join('countries', 'countries.id', '=', 'other_companies.country_id')
            ->whereNotNull('countries.name')
            ->distinct()
            ->orderBy('countries.name')
            ->pluck('countries.name')
            ->values();

        return view('Other Companies.index', compact('companies', 'countries'));
    }

    public function create()
    {
        $countries = CountryCache::active();
        $currencies = ['USD', 'EUR', 'GBP', 'JPY', 'CNY', 'AED', 'SGD'];
        $companyTypes = $this->companyTypeOptions();

        return view('Other Companies.create', compact('countries', 'currencies', 'companyTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_type' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:255',
            'contact_person' => 'required|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'office_country_id' => 'nullable|exists:countries,id',
        ]);

        OtherCompany::create($request->only([
            'company_name', 'company_type', 'code', 'code_description', 'phone_number', 'contact_person', 'email',
            'remarks', 'special_considerations', 'street_address', 'city', 'district_state', 'zip_code',
            'country_id', 'port_code', 'office_street_address', 'office_city', 'office_district_state',
            'office_zip_code', 'office_country_id', 'vat_number', 'eori_number', 'currency', 'un_locode',
        ]));

        return redirect()->route('other-companies.index')->with('success', 'Company created successfully.');
    }

    public function edit(OtherCompany $otherCompany)
    {
        $otherCompany->load(['creator', 'updater']);
        $countries = CountryCache::active();
        $currencies = ['USD', 'EUR', 'GBP', 'JPY', 'CNY', 'AED', 'SGD', 'INR', 'AUD', 'CAD'];
        $companyTypes = $this->companyTypeOptions();

        return view('Other Companies.edit', compact('otherCompany', 'countries', 'currencies', 'companyTypes'));
    }

    public function update(Request $request, OtherCompany $otherCompany)
    {
        $activeTab = $this->resolveActiveTab($request);

        try {
            $request->validate([
                'company_name'      => 'required|string|max:255',
                'company_type'      => 'nullable|string|max:255',
                'email'             => 'nullable|email|max:255',
                'phone_number'      => 'nullable|string|max:255',
                'contact_person'    => 'required|string|max:255',
                'country_id'        => 'nullable|exists:countries,id',
                'office_country_id' => 'nullable|exists:countries,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $e->redirectTo(route('other-companies.edit', $otherCompany->id) . '#' . $activeTab);
            throw $e;
        }

        $otherCompany->fill($request->only([
            'company_name', 'company_type', 'code', 'code_description', 'phone_number', 'contact_person', 'email',
            'remarks', 'special_considerations', 'street_address', 'city', 'district_state', 'zip_code',
            'country_id', 'port_code', 'office_street_address', 'office_city', 'office_district_state',
            'office_zip_code', 'office_country_id', 'vat_number', 'eori_number', 'currency', 'un_locode',
        ]));

        if (blank($otherCompany->created_by)) {
            $otherCompany->created_by = auth()->id();
        }
        $otherCompany->updated_by = auth()->id();
        $otherCompany->save();

        return redirect()
            ->route('other-companies.edit', $otherCompany->id)
            ->with('success', 'Company updated successfully.')
            ->withFragment($activeTab);
    }

    public function destroy(OtherCompany $otherCompany)
    {
        try {
            $otherCompany->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Company deleted successfully.',
                ]);
            }

            return redirect()->route('other-companies.index')->with('success', 'Company deleted.');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting company.',
                ], 500);
            }

            return redirect()->route('other-companies.index')->with('error', 'Error deleting company.');
        }
    }

    public function createContact($otherCompanyId)
    {
        $otherCompany = OtherCompany::findOrFail($otherCompanyId);
        return view('Other Companies.contacts.create', compact('otherCompany'));
    }

    public function storeContact(Request $request, $otherCompanyId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_main_contact' => 'nullable|boolean',
        ]);

        $otherCompany = OtherCompany::findOrFail($otherCompanyId);
        
        $otherCompany->contacts()->create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'description' => $request->description,
            'is_main_contact' => $request->has('is_main_contact'),
        ]);

        return redirect()
            ->route('other-companies.edit', $otherCompanyId)
            ->with('success', 'Contact added successfully.')
            ->withFragment('contacts');
    }

    public function editContact($otherCompanyId, $contactId)
    {
        $otherCompany = OtherCompany::findOrFail($otherCompanyId);
        $contact = \App\Models\Contact::findOrFail($contactId);
        return view('Other Companies.contacts.edit', compact('otherCompany', 'contact'));
    }

    public function updateContact(Request $request, $otherCompanyId, $contactId)
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
            ->route('other-companies.edit', $otherCompanyId)
            ->with('success', 'Contact updated successfully.')
            ->withFragment('contacts');
    }

    public function destroyContact($otherCompanyId, $contactId)
    {
        $contact = \App\Models\Contact::findOrFail($contactId);
        $contact->delete();
        return redirect()
            ->route('other-companies.edit', $otherCompanyId)
            ->with('success', 'Contact deleted successfully.')
            ->withFragment('contacts');
    }

    private function resolveActiveTab(Request $request): string
    {
        $allowed = ['company-details', 'contacts'];
        $tab = (string) $request->input('active_tab', 'company-details');

        return in_array($tab, $allowed, true) ? $tab : 'company-details';
    }

    private function companyTypeOptions(): array
    {
        return [
            'Ship agent',
            'External agent',
            'Vessel owner',
            'Delivery address',
            'Customer group',
            'Customer procurement group',
        ];
    }
}
