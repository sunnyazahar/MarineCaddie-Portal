<?php

namespace App\Http\Controllers;

use App\Support\CountryCache;
use App\Models\OtherCompany;
use App\Models\Office;
use App\Models\OfficeBankAccount;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfficeController extends Controller
{
    public function index()
    {
        $offices = Office::with('country')->get();
        return view('offices.index', compact('offices'));
    }

    public function create()
    {
        $companies = OtherCompany::all();
        $countries = CountryCache::active();
        return view('offices.create', compact('companies', 'countries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'office_name' => 'required|string|max:150',
            'office_short_name' => 'nullable|string|max:50',
            'phone_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'eori_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'district_state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country_id' => 'nullable|exists:countries,id',
            'postal_address' => 'nullable|string',
            'postal_city' => 'nullable|string|max:100',
            'postal_district_state' => 'nullable|string|max:100',
            'postal_zip_code' => 'nullable|string|max:20',
            'office_country_id' => 'nullable|exists:countries,id',
            'invoicing_currency' => 'nullable|string|max:3',
            'reporting_currency' => 'nullable|string|max:3',
            'vat_number' => 'nullable|string|max:50',
            'invoicing_emails' => 'nullable|string',
            'heading_invoice' => 'nullable|string',
            'information_invoice' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $office = Office::create([
                'office_name' => $request->office_name,
                'office_short_name' => $request->office_short_name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'eori_number' => $request->eori_number,
                'address' => $request->address,
                'city' => $request->city,
                'district_state' => $request->district_state,
                'zip_code' => $request->zip_code,
                'country_id' => $request->country_id,
                'postal_address' => $request->postal_address,
                'postal_city' => $request->postal_city,
                'postal_district_state' => $request->postal_district_state,
                'postal_zip_code' => $request->postal_zip_code,
                'office_country_id' => $request->office_country_id,
                'invoicing_currency' => $request->invoicing_currency,
                'reporting_currency' => $request->reporting_currency,
                'vat_rates' => $request->vat_rates,
                'vat_country_specific_name' => $request->vat_country_specific_name,
                'vat_number' => $request->vat_number,
                'invoicing_emails' => $request->invoicing_emails,
                'heading_invoice' => $request->heading_invoice,
                'information_invoice' => $request->information_invoice,
                'use_vat_check' => $request->has('use_vat_check'),
                'show_imo' => $request->has('show_imo'),
                'enable_reader' => $request->has('enable_reader'),
            ]);

            // Save bank accounts
            if ($request->has('bank')) {
                foreach ($request->bank as $index => $bank) {
                    if (!empty($bank)) {
                        OfficeBankAccount::create([
                            'office_id' => $office->id,
                            'bank' => $bank,
                            'currency' => $request->currency[$index] ?? null,
                            'account_number' => $request->account_number[$index] ?? null,
                            'iban' => $request->iban[$index] ?? null,
                            'swift' => $request->swift[$index] ?? null,
                            'is_main_account' => ($request->is_main_account_status[$index] ?? '0') == '1',
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Office created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating office: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $office = Office::with(['bankAccounts', 'contacts', 'creator', 'updater'])->findOrFail($id);
        $companies = OtherCompany::all();
        $countries = CountryCache::active();
        return view('offices.edit', compact('office', 'companies', 'countries'));
    }

    public function createOperationUser($officeId)
    {
        $office = Office::findOrFail($officeId);
        return view('offices.operations_users.create', compact('office'));
    }

    public function storeOperationUser(Request $request, $officeId)
    {
        $office = Office::findOrFail($officeId);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:50',
            'reply_to_email' => 'nullable|email|max:255',
        ]);

        Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'reply_to_email' => $request->reply_to_email,
            'is_cc_enabled' => $request->has('is_cc_enabled'),
            'office_id' => $office->id,
            'category' => 'operations',
            'status' => true,
        ]);

        return redirect()
            ->route('offices.edit', $office->id)
            ->with('success', 'Operation user added successfully.')
            ->withFragment('operations-users');
    }

    public function editOperationUser($officeId, $contactId)
    {
        $office = Office::findOrFail($officeId);
        $contact = Contact::findOrFail($contactId);
        return view('offices.operations_users.edit', compact('office', 'contact'));
    }

    public function updateOperationUser(Request $request, $officeId, $contactId)
    {
        $office = Office::findOrFail($officeId);
        $contact = Contact::findOrFail($contactId);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:50',
            'reply_to_email' => 'nullable|email|max:255',
        ]);

        $contact->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'reply_to_email' => $request->reply_to_email,
            'is_cc_enabled' => $request->has('is_cc_enabled'),
        ]);

        return redirect()
            ->route('offices.edit', $office->id)
            ->with('success', 'Operation user updated successfully.')
            ->withFragment('operations-users');
    }

    public function createAccountUser($officeId)
    {
        $office = Office::findOrFail($officeId);
        return view('offices.account_users.create', compact('office'));
    }

    public function storeAccountUser(Request $request, $officeId)
    {
        $office = Office::findOrFail($officeId);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:50',
            'reply_to_email' => 'nullable|email|max:255',
        ]);

        Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'reply_to_email' => $request->reply_to_email,
            'is_cc_enabled' => $request->has('is_cc_enabled'),
            'office_id' => $office->id,
            'category' => 'account',
            'status' => true,
        ]);

        return redirect()
            ->route('offices.edit', $office->id)
            ->with('success', 'Account user added successfully.')
            ->withFragment('accounting-users');
    }

    public function editAccountUser($officeId, $contactId)
    {
        $office = Office::findOrFail($officeId);
        $contact = Contact::where('category', 'account')->findOrFail($contactId);
        return view('offices.account_users.edit', compact('office', 'contact'));
    }

    public function updateAccountUser(Request $request, $officeId, $contactId)
    {
        $office = Office::findOrFail($officeId);
        $contact = Contact::where('category', 'account')->findOrFail($contactId);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:50',
            'reply_to_email' => 'nullable|email|max:255',
        ]);

        $contact->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'reply_to_email' => $request->reply_to_email,
            'is_cc_enabled' => $request->has('is_cc_enabled'),
        ]);

        return redirect()
            ->route('offices.edit', $office->id)
            ->with('success', 'Account user updated successfully.')
            ->withFragment('accounting-users');
    }

    public function createSalesUser($officeId)
    {
        $office = Office::findOrFail($officeId);
        return view('offices.sales_users.create', compact('office'));
    }

    public function storeSalesUser(Request $request, $officeId)
    {
        $office = Office::findOrFail($officeId);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:50',
            'reply_to_email' => 'nullable|email|max:255',
        ]);

        Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'reply_to_email' => $request->reply_to_email,
            'is_cc_enabled' => $request->has('is_cc_enabled'),
            'office_id' => $office->id,
            'category' => 'sales',
            'status' => true,
        ]);

        return redirect()
            ->route('offices.edit', $office->id)
            ->with('success', 'Sales user added successfully.')
            ->withFragment('sales-users');
    }

    public function editSalesUser($officeId, $contactId)
    {
        $office = Office::findOrFail($officeId);
        $contact = Contact::where('category', 'sales')->findOrFail($contactId);
        return view('offices.sales_users.edit', compact('office', 'contact'));
    }

    public function updateSalesUser(Request $request, $officeId, $contactId)
    {
        $office = Office::findOrFail($officeId);
        $contact = Contact::where('category', 'sales')->findOrFail($contactId);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:50',
            'reply_to_email' => 'nullable|email|max:255',
        ]);

        $contact->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'reply_to_email' => $request->reply_to_email,
            'is_cc_enabled' => $request->has('is_cc_enabled'),
        ]);

        return redirect()
            ->route('offices.edit', $office->id)
            ->with('success', 'Sales user updated successfully.')
            ->withFragment('sales-users');
    }

    public function createManagerUser($officeId)
    {
        $office = Office::findOrFail($officeId);
        return view('offices.manager_users.create', compact('office'));
    }

    public function storeManagerUser(Request $request, $officeId)
    {
        $office = Office::findOrFail($officeId);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:50',
            'reply_to_email' => 'nullable|email|max:255',
        ]);

        Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'reply_to_email' => $request->reply_to_email,
            'is_cc_enabled' => $request->has('is_cc_enabled'),
            'office_id' => $office->id,
            'category' => 'manager',
            'status' => true,
        ]);

        return redirect()
            ->route('offices.edit', $office->id)
            ->with('success', 'Manager user added successfully.')
            ->withFragment('manager-users');
    }

    public function editManagerUser($officeId, $contactId)
    {
        $office = Office::findOrFail($officeId);
        $contact = Contact::where('category', 'manager')->findOrFail($contactId);
        return view('offices.manager_users.edit', compact('office', 'contact'));
    }

    public function updateManagerUser(Request $request, $officeId, $contactId)
    {
        $office = Office::findOrFail($officeId);
        $contact = Contact::where('category', 'manager')->findOrFail($contactId);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:50',
            'reply_to_email' => 'nullable|email|max:255',
        ]);

        $contact->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'reply_to_email' => $request->reply_to_email,
            'is_cc_enabled' => $request->has('is_cc_enabled'),
        ]);

        return redirect()
            ->route('offices.edit', $office->id)
            ->with('success', 'Manager user updated successfully.')
            ->withFragment('manager-users');
    }

    public function update(Request $request, $id)
    {
        $office = Office::findOrFail($id);
        
        $validated = $request->validate([
            'office_name' => 'required|string|max:150',
            'office_short_name' => 'nullable|string|max:50',
            'phone_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'eori_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'district_state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country_id' => 'nullable|exists:countries,id',
            'postal_address' => 'nullable|string',
            'postal_city' => 'nullable|string|max:100',
            'postal_district_state' => 'nullable|string|max:100',
            'postal_zip_code' => 'nullable|string|max:20',
            'office_country_id' => 'nullable|exists:countries,id',
            'invoicing_currency' => 'nullable|string|max:3',
            'reporting_currency' => 'nullable|string|max:3',
            'vat_number' => 'nullable|string|max:50',
            'invoicing_emails' => 'nullable|string',
            'heading_invoice' => 'nullable|string',
            'information_invoice' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $office->update([
                'office_name' => $request->office_name,
                'office_short_name' => $request->office_short_name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'eori_number' => $request->eori_number,
                'address' => $request->address,
                'city' => $request->city,
                'district_state' => $request->district_state,
                'zip_code' => $request->zip_code,
                'country_id' => $request->country_id,
                'postal_address' => $request->postal_address,
                'postal_city' => $request->postal_city,
                'postal_district_state' => $request->postal_district_state,
                'postal_zip_code' => $request->postal_zip_code,
                'office_country_id' => $request->office_country_id,
                'invoicing_currency' => $request->invoicing_currency,
                'reporting_currency' => $request->reporting_currency,
                'vat_rates' => $request->vat_rates,
                'vat_country_specific_name' => $request->vat_country_specific_name,
                'vat_number' => $request->vat_number,
                'invoicing_emails' => $request->invoicing_emails,
                'heading_invoice' => $request->heading_invoice,
                'information_invoice' => $request->information_invoice,
                'use_vat_check' => $request->has('use_vat_check'),
                'show_imo' => $request->has('show_imo'),
                'enable_reader' => $request->has('enable_reader'),
                'status' => $request->status ?? 1,
            ]);

            // Sync bank accounts (Delete existing and recreate)
            $office->bankAccounts()->delete();
            if ($request->has('bank')) {
                foreach ($request->bank as $index => $bank) {
                    if (!empty($bank)) {
                        OfficeBankAccount::create([
                            'office_id' => $office->id,
                            'bank' => $bank,
                            'currency' => $request->currency[$index] ?? null,
                            'account_number' => $request->account_number[$index] ?? null,
                            'iban' => $request->iban[$index] ?? null,
                            'swift' => $request->swift[$index] ?? null,
                            'is_main_account' => ($request->is_main_account_status[$index] ?? '0') == '1',
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Office updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating office: ' . $e->getMessage());
        }
    }
}
