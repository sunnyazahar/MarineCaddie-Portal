<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\ContactRepositoryInterface;
use App\Repositories\Contracts\OtherCompanyRepositoryInterface;
use App\Support\CountryCache;
use Illuminate\Http\Request;

class OtherCompanyController extends Controller
{
    public function __construct(
        private OtherCompanyRepositoryInterface $companies,
        private ContactRepositoryInterface $contacts,
    ) {}

    public function index(Request $request)
    {
        $perPage   = max(10, min(100, (int) $request->input('per_page', 25)));
        $companies = $this->companies->paginate(
            $request->only(['name', 'code', 'address', 'city', 'country']),
            $perPage
        );

        if ($request->ajax()) {
            return response()->json([
                'html'       => view('Other Companies.partials.rows', compact('companies'))->render(),
                'pagination' => (string) $companies->links(),
                'total'      => $companies->total(),
            ]);
        }

        $countries = $this->companies->distinctCountries();

        return view('Other Companies.index', compact('companies', 'countries'));
    }

    public function create()
    {
        $countries    = CountryCache::active();
        $currencies   = ['USD', 'EUR', 'GBP', 'JPY', 'CNY', 'AED', 'SGD'];
        $companyTypes = $this->companyTypeOptions();
        return view('Other Companies.create', compact('countries', 'currencies', 'companyTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name'      => 'required|string|max:255',
            'company_type'      => 'nullable|string|max:255',
            'email'             => 'nullable|email|max:255',
            'phone_number'      => 'nullable|string|max:255',
            'contact_person'    => 'required|string|max:255',
            'country_id'        => 'nullable|exists:countries,id',
            'office_country_id' => 'nullable|exists:countries,id',
        ]);

        $this->companies->create($request->only([
            'company_name', 'company_type', 'code', 'code_description', 'phone_number', 'contact_person', 'email',
            'remarks', 'special_considerations', 'street_address', 'city', 'district_state', 'zip_code',
            'country_id', 'port_code', 'office_street_address', 'office_city', 'office_district_state',
            'office_zip_code', 'office_country_id', 'vat_number', 'eori_number', 'currency', 'un_locode',
        ]));

        return redirect()->route('other-companies.index')->with('success', 'Company created successfully.');
    }

    public function edit(\App\Models\OtherCompany $otherCompany)
    {
        $otherCompany->load(['creator', 'updater']);
        $countries    = CountryCache::active();
        $currencies   = ['USD', 'EUR', 'GBP', 'JPY', 'CNY', 'AED', 'SGD', 'INR', 'AUD', 'CAD'];
        $companyTypes = $this->companyTypeOptions();
        return view('Other Companies.edit', compact('otherCompany', 'countries', 'currencies', 'companyTypes'));
    }

    public function update(Request $request, \App\Models\OtherCompany $otherCompany)
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

    public function destroy(\App\Models\OtherCompany $otherCompany)
    {
        try {
            $this->companies->deleteById((int) $otherCompany->id);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Company deleted successfully.']);
            }

            return redirect()->route('other-companies.index')->with('success', 'Company deleted.');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error deleting company.'], 500);
            }
            return redirect()->route('other-companies.index')->with('error', 'Error deleting company.');
        }
    }

    public function createContact($otherCompanyId)
    {
        $otherCompany = $this->companies->findOrFail((int) $otherCompanyId);
        return view('Other Companies.contacts.create', compact('otherCompany'));
    }

    public function storeContact(Request $request, $otherCompanyId)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'nullable|email|max:255',
            'phone_number'    => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'is_main_contact' => 'nullable|boolean',
        ]);

        $otherCompany = $this->companies->findOrFail((int) $otherCompanyId);
        $otherCompany->contacts()->create([
            'name'            => $request->name,
            'email'           => $request->email,
            'phone_number'    => $request->phone_number,
            'description'     => $request->description,
            'is_main_contact' => $request->has('is_main_contact'),
        ]);

        return redirect()
            ->route('other-companies.edit', $otherCompanyId)
            ->with('success', 'Contact added successfully.')
            ->withFragment('contacts');
    }

    public function editContact($otherCompanyId, $contactId)
    {
        $otherCompany = $this->companies->findOrFail((int) $otherCompanyId);
        $contact      = $this->contacts->findOrFail((int) $contactId);
        return view('Other Companies.contacts.edit', compact('otherCompany', 'contact'));
    }

    public function updateContact(Request $request, $otherCompanyId, $contactId)
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
            ->route('other-companies.edit', $otherCompanyId)
            ->with('success', 'Contact updated successfully.')
            ->withFragment('contacts');
    }

    public function destroyContact($otherCompanyId, $contactId)
    {
        $this->contacts->deleteById((int) $contactId);
        return redirect()
            ->route('other-companies.edit', $otherCompanyId)
            ->with('success', 'Contact deleted successfully.')
            ->withFragment('contacts');
    }

    private function resolveActiveTab(Request $request): string
    {
        $allowed = ['company-details', 'contacts'];
        $tab     = (string) $request->input('active_tab', 'company-details');
        return in_array($tab, $allowed, true) ? $tab : 'company-details';
    }

    private function companyTypeOptions(): array
    {
        return [
            'Ship agent', 'External agent', 'Vessel owner',
            'Delivery address', 'Customer group', 'Customer procurement group',
        ];
    }
}
