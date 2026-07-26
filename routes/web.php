<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/otp', [App\Http\Controllers\Auth\OtpController::class, 'show'])->name('otp.show');
    Route::post('/otp/verify', [App\Http\Controllers\Auth\OtpController::class, 'verify'])->name('otp.verify');
    Route::post('/otp/resend', [App\Http\Controllers\Auth\OtpController::class, 'resend'])->name('otp.resend');

    Route::middleware('otp.verified')->group(function () {
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard.home');
Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.mark-read');
Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users.index');
Route::post('/users', [App\Http\Controllers\UserController::class, 'store'])->name('users.store');
Route::put('/users/{user}', [App\Http\Controllers\UserController::class, 'update'])->name('users.update');
Route::post('/customers', [App\Http\Controllers\CustomerController::class, 'store'])->name('customers.store');

Route::get('/offices', [App\Http\Controllers\OfficeController::class, 'index'])->name('offices.index');
Route::get('/offices/create', function () {
    return view('offices.create');
})->name('offices.create');

Route::get('/offices/edit/{id}', [App\Http\Controllers\OfficeController::class, 'edit'])->name('offices.edit');
Route::put('/offices/update/{id}', [App\Http\Controllers\OfficeController::class, 'update'])->name('offices.update');

Route::get('/offices/{office}/operations_users/create', [App\Http\Controllers\OfficeController::class, 'createOperationUser'])->name('offices.operations_users.create');
Route::post('/offices/{office}/operations_users', [App\Http\Controllers\OfficeController::class, 'storeOperationUser'])->name('offices.operations_users.store');
Route::get('/offices/{office}/operations_users/{contact}/edit', [App\Http\Controllers\OfficeController::class, 'editOperationUser'])->name('offices.operations_users.edit');
Route::put('/offices/{office}/operations_users/{contact}', [App\Http\Controllers\OfficeController::class, 'updateOperationUser'])->name('offices.operations_users.update');

Route::get('/offices/{office}/account_users/create', [App\Http\Controllers\OfficeController::class, 'createAccountUser'])->name('offices.account_users.create');
Route::post('/offices/{office}/account_users', [App\Http\Controllers\OfficeController::class, 'storeAccountUser'])->name('offices.account_users.store');
Route::get('/offices/{office}/account_users/{contact}/edit', [App\Http\Controllers\OfficeController::class, 'editAccountUser'])->name('offices.account_users.edit');
Route::put('/offices/{office}/account_users/{contact}', [App\Http\Controllers\OfficeController::class, 'updateAccountUser'])->name('offices.account_users.update');

Route::get('/offices/{office}/sales_users/create', [App\Http\Controllers\OfficeController::class, 'createSalesUser'])->name('offices.sales_users.create');
Route::post('/offices/{office}/sales_users', [App\Http\Controllers\OfficeController::class, 'storeSalesUser'])->name('offices.sales_users.store');
Route::get('/offices/{office}/sales_users/{contact}/edit', [App\Http\Controllers\OfficeController::class, 'editSalesUser'])->name('offices.sales_users.edit');
Route::put('/offices/{office}/sales_users/{contact}', [App\Http\Controllers\OfficeController::class, 'updateSalesUser'])->name('offices.sales_users.update');

Route::get('/offices/{office}/manager_users/create', [App\Http\Controllers\OfficeController::class, 'createManagerUser'])->name('offices.manager_users.create');
Route::post('/offices/{office}/manager_users', [App\Http\Controllers\OfficeController::class, 'storeManagerUser'])->name('offices.manager_users.store');
Route::get('/offices/{office}/manager_users/{contact}/edit', [App\Http\Controllers\OfficeController::class, 'editManagerUser'])->name('offices.manager_users.edit');
Route::put('/offices/{office}/manager_users/{contact}', [App\Http\Controllers\OfficeController::class, 'updateManagerUser'])->name('offices.manager_users.update');

Route::get('/customers', [App\Http\Controllers\CustomerController::class, 'index'])->name('customers.index');

Route::get('/customers/create', [App\Http\Controllers\CustomerController::class, 'create'])->name('customers.create');


Route::get('/customers/edit/{id}', [App\Http\Controllers\CustomerController::class, 'edit'])->name('customers.edit');
Route::put('/customers/{id}', [App\Http\Controllers\CustomerController::class, 'update'])->name('customers.update');

Route::get('/customers/{customer}/vessels/create', [App\Http\Controllers\CustomerController::class, 'createVessel'])->name('customers.vessels.create');

Route::post('/customers/{id}/vessels', [App\Http\Controllers\CustomerController::class, 'storeVessel'])->name('customers.vessels.store');
Route::get('/customers/vessels/{vessel}/edit', [App\Http\Controllers\CustomerController::class, 'editVessel'])->name('customers.vessels.edit');
Route::put('/customers/vessels/{vessel}', [App\Http\Controllers\CustomerController::class, 'updateVessel'])->name('customers.vessels.update');

Route::post('/customers/{id}/documents', [App\Http\Controllers\CustomerController::class, 'uploadDocument'])->name('customers.documents.upload');
Route::get('/customers/{customerId}/documents/{docId}', [App\Http\Controllers\CustomerController::class, 'showDocument'])->name('customers.documents.show');
Route::get('/customers/{id}/logo', [App\Http\Controllers\CustomerController::class, 'showLogo'])->name('customers.logo.show');
Route::delete('/customers/documents/{docId}', [App\Http\Controllers\CustomerController::class, 'deleteDocument'])->name('customers.documents.delete');

Route::get('/shipments', [App\Http\Controllers\ShipmentController::class, 'index'])->name('shipments');

Route::post('/shipments', [App\Http\Controllers\ShipmentController::class, 'store'])->name('shipments.store');

Route::get('/shipments/edit/{id}', [App\Http\Controllers\ShipmentController::class, 'edit'])->name('shipments.edit');
Route::get('/shipments/{id}/combined-po-documents', [App\Http\Controllers\ShipmentController::class, 'combinedPoDocuments'])->name('shipments.combined-po-documents');
Route::get('/shipments/{id}/combined-manifest-documents', [App\Http\Controllers\ShipmentController::class, 'combinedManifestDocuments'])->name('shipments.combined-manifest-documents');
Route::post('/shipments/{id}/manifests/generate', [App\Http\Controllers\ShipmentController::class, 'generateManifest'])->name('shipments.manifests.generate');
Route::get('/shipments/{shipmentId}/manifests/{manifestId}', [App\Http\Controllers\ShipmentController::class, 'showManifest'])->name('shipments.manifests.show');
Route::delete('/shipments/{shipmentId}/manifests/{manifestId}', [App\Http\Controllers\ShipmentController::class, 'deleteManifest'])->name('shipments.manifests.delete');
Route::post('/shipments/{id}/manifest-mail/prepare', [App\Http\Controllers\ShipmentController::class, 'prepareManifestMail'])->name('shipments.manifest-mail.prepare');
Route::get('/shipments/{id}/manifest-mail/open', [App\Http\Controllers\ShipmentController::class, 'manifestMailOpen'])->name('shipments.manifest-mail.open');
Route::get('/shipments/{id}/manifest-mail/launcher', [App\Http\Controllers\ShipmentController::class, 'manifestMailLauncher'])->name('shipments.manifest-mail-launcher');
Route::get('/shipments/{id}/manifest-mail', [App\Http\Controllers\ShipmentController::class, 'manifestMail'])->name('shipments.manifest-mail');
Route::post('/shipments/{id}/pre-alerts/generate', [App\Http\Controllers\ShipmentController::class, 'generatePreAlert'])->name('shipments.pre-alerts.generate');
Route::get('/shipments/{shipmentId}/pre-alerts/{preAlertId}', [App\Http\Controllers\ShipmentController::class, 'showPreAlert'])->name('shipments.pre-alerts.show');
Route::delete('/shipments/{shipmentId}/pre-alerts/{preAlertId}', [App\Http\Controllers\ShipmentController::class, 'deletePreAlert'])->name('shipments.pre-alerts.delete');
Route::post('/shipments/{id}/pre-alert-mail/prepare', [App\Http\Controllers\ShipmentController::class, 'preparePreAlertMail'])->name('shipments.pre-alert-mail.prepare');
Route::get('/shipments/{id}/pre-alert-mail/open', [App\Http\Controllers\ShipmentController::class, 'preAlertMailOpen'])->name('shipments.pre-alert-mail.open');
Route::get('/shipments/{id}/pre-alert-mail', [App\Http\Controllers\ShipmentController::class, 'preAlertMail'])->name('shipments.pre-alert-mail');
Route::post('/shipments/{id}/documents', [App\Http\Controllers\ShipmentController::class, 'uploadDocument'])->name('shipments.documents.upload');
Route::get('/shipments/{shipmentId}/documents/{docId}', [App\Http\Controllers\ShipmentController::class, 'showDocument'])->name('shipments.documents.show');
Route::delete('/shipments/documents/{docId}', [App\Http\Controllers\ShipmentController::class, 'deleteDocument'])->name('shipments.documents.delete');
Route::patch('/shipments/documents/{docId}/type', [App\Http\Controllers\ShipmentController::class, 'updateDocumentType'])->name('shipments.documents.update-type');
Route::patch('/shipments/documents/{docId}/internal', [App\Http\Controllers\ShipmentController::class, 'updateDocumentInternal'])->name('shipments.documents.update-internal');
Route::put('/shipments/{id}', [App\Http\Controllers\ShipmentController::class, 'update'])->name('shipments.update');
Route::post('/shipments/{id}/finalize', [App\Http\Controllers\ShipmentController::class, 'finalize'])->name('shipments.finalize');
Route::post('/shipments/{id}/mark-as-arrived', [App\Http\Controllers\ShipmentController::class, 'markAsArrived'])->name('shipments.mark-as-arrived');
Route::post('/shipments/{id}/status', [App\Http\Controllers\ShipmentController::class, 'updateStatus'])->name('shipments.update-status');
Route::post('/shipments/{id}/flags', [App\Http\Controllers\ShipmentController::class, 'updateFlags'])->name('shipments.update-flags');

Route::get('/pre-alert-reminders', [App\Http\Controllers\ShipmentController::class, 'preAlertReminders'])->name('pre-alert-reminders');
Route::get('/shipments/{id}/pre-alert-reminder-mail/preview', [App\Http\Controllers\ShipmentController::class, 'preAlertReminderMailPreview'])->name('shipments.pre-alert-reminder-mail.preview');
Route::post('/shipments/{id}/pre-alert-reminder-mail/send', [App\Http\Controllers\ShipmentController::class, 'recordPreAlertReminderSend'])->name('shipments.pre-alert-reminder-mail.send');
Route::get('/shipments/{id}/pre-alert-reminder-mail', [App\Http\Controllers\ShipmentController::class, 'preAlertReminderMail'])->name('shipments.pre-alert-reminder-mail');
Route::get('/shipments/{id}/delivery-status-reminder-mail/preview', [App\Http\Controllers\ShipmentController::class, 'deliveryStatusReminderMailPreview'])->name('shipments.delivery-status-reminder-mail.preview');
Route::get('/shipments/{id}/delivery-status-reminder-mail', [App\Http\Controllers\ShipmentController::class, 'deliveryStatusReminderMail'])->name('shipments.delivery-status-reminder-mail');
Route::get('/shipments/{id}/invoice-request-mail/preview', [App\Http\Controllers\ShipmentController::class, 'invoiceRequestMailPreview'])->name('shipments.invoice-request-mail.preview');
Route::get('/shipments/{id}/invoice-request-mail', [App\Http\Controllers\ShipmentController::class, 'invoiceRequestMail'])->name('shipments.invoice-request-mail');

Route::get('/shipment-follow-up', [App\Http\Controllers\ShipmentController::class, 'shipmentFollowUp'])->name('shipment-follow-up');

Route::get('/cost-follow-up', [App\Http\Controllers\ShipmentController::class, 'costFollowUp'])->name('cost-follow-up');
Route::get('/cost-follow-up/search', [App\Http\Controllers\ShipmentController::class, 'costFollowUpSearch'])->name('cost-follow-up.search');

Route::get('/billable-shipments', function () {
    return view('Billing.billable-shipments');
})->name('billable-shipments');

Route::get('/unbillable-shipments', function () {
    return view('Billing.unbillable-shipments');
})->name('unbillable-shipments');

Route::get('/all-invoices', function () {
    return view('Billing.all-invoices');
})->name('all-invoices');

Route::get('/all-incoming-invoices', function () {
    return view('Billing.all-incoming-invoices');
})->name('all-incoming-invoices');

Route::get('/all-costs', function () {
    return view('Billing.all-costs');
})->name('all-costs');

Route::get('/accounting', function () {
    return view('Billing.accounting');
})->name('accounting');

Route::get('/create-shipment', function (\Illuminate\Http\Request $request) {
    $countries = \App\Models\Country::where('is_active', true)->orderBy('name')->get();
    $crrs = \App\Models\Crr::with(['packages', 'documents', 'customerVessel.customer.responsible.accountManager'])
        ->selectableForShipment()
        ->latest()
        ->get();
    $hubs = \App\Models\Hub::orderBy('hub_name')->get();
    $agents = \App\Models\Agent::with('country')->orderBy('agent_name')->get();

    $preselectedCrrIds = collect(explode(',', (string) $request->query('crr_ids', '')))
        ->map(fn ($id) => (int) trim((string) $id))
        ->filter(fn ($id) => $id > 0)
        ->unique()
        ->values()
        ->all();

    if ($preselectedCrrIds !== []) {
        $preselectedCrrIds = \App\Models\Crr::query()
            ->selectableForShipment()
            ->whereIn('id', $preselectedCrrIds)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    $resolveShipmentDepartureParty = function (\App\Models\Crr $crr, $hubs, $agents): ?array {
        $candidates = array_values(array_unique(array_filter([
            trim((string) ($crr->hub_code ?? '')),
            trim((string) ($crr->hub_agent ?? '')),
        ])));

        foreach ($candidates as $candidate) {
            $matchedHub = $hubs->first(function ($hub) use ($candidate) {
                return strcasecmp((string) ($hub->code ?? ''), $candidate) === 0
                    || strcasecmp((string) ($hub->hub_name ?? ''), $candidate) === 0;
            });

            if ($matchedHub) {
                return [
                    'id' => 'hub:' . $matchedHub->id,
                    'text' => $matchedHub->hub_name,
                    'type' => 'hub',
                    'hub_code' => $matchedHub->code,
                    'port_code' => $matchedHub->port_code,
                ];
            }

            $matchedAgent = $agents->first(function ($agent) use ($candidate) {
                return strcasecmp((string) ($agent->code ?? ''), $candidate) === 0
                    || strcasecmp((string) ($agent->agent_name ?? ''), $candidate) === 0;
            });

            if ($matchedAgent) {
                return [
                    'id' => 'agent:' . $matchedAgent->id,
                    'text' => $matchedAgent->agent_name,
                    'type' => 'agent',
                    'port_code' => $matchedAgent->port_code,
                ];
            }
        }

        return null;
    };

    $departurePartiesByCrrId = [];
    foreach ($crrs as $crr) {
        $party = $resolveShipmentDepartureParty($crr, $hubs, $agents);
        if ($party) {
            $departurePartiesByCrrId[$crr->id] = $party;
        }
    }

    $preselectedDepartureParty = ($preselectedCrrIds[0] ?? null)
        ? ($departurePartiesByCrrId[$preselectedCrrIds[0]] ?? null)
        : null;

    return view('Shipment.create', compact(
        'countries',
        'crrs',
        'hubs',
        'agents',
        'preselectedCrrIds',
        'departurePartiesByCrrId',
        'preselectedDepartureParty'
    ));
})->name('create-shipment');

// API: port code search for Select2 (ports table, mapped to countries)
Route::get('/api/ports', function (\Illuminate\Http\Request $request) {
    $q = trim((string) $request->query('q', ''));
    $limit = $q === '' ? 10 : 30;

    $portsQuery = \App\Models\Port::query()
        ->with('country')
        ->active()
        ->when($q !== '', function ($query) use ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('iata_code', 'like', "%{$q}%")
                    ->orWhere('port_name', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%")
                    ->orWhere('country_name', 'like', "%{$q}%");
            });
        });

    if ($q !== '') {
        $portsQuery->orderByRaw('CASE WHEN iata_code = ? THEN 0 ELSE 1 END', [strtoupper($q)]);
    }

    $ports = $portsQuery
        ->orderBy('iata_code')
        ->limit($limit)
        ->get()
        ->map(function (\App\Models\Port $port) {
            $code = $port->iata_code ?? '';
            return [
                'id' => $code,
                'text' => trim($code . ($port->city ? ', ' . $port->city : '')),
                'code' => $code,
                'city' => $port->city,
                'port_name' => $port->port_name,
                'country' => $port->country?->name ?? $port->country_name,
            ];
        })
        ->values();

    return response()->json(['results' => $ports]);
})->name('api.ports');

// API: combined parties search for Departure select2 (hubs, agents, customers)
Route::get('/api/parties', function (\Illuminate\Http\Request $request) {
    $q = $request->query('q');

    $hubs = \App\Models\Hub::orderBy('hub_name')
        ->when($q, function ($qbuilder) use ($q) {
            $qbuilder->where('hub_name', 'like', "%{$q}%")->orWhere('code', 'like', "%{$q}%");
        })->get()->map(function ($h) {
            return [
                'id' => 'hub:'.$h->id,
                'text' => $h->hub_name,
                'subtitle' => ($h->city ? $h->city . ', ' : '') . ($h->country ?? ''),
                'type' => 'hub',
                'hub_code' => $h->code,
                'port_code' => $h->port_code,
            ];
        });

    $agents = \App\Models\Agent::orderBy('agent_name')
        ->when($q, function ($qbuilder) use ($q) {
            $qbuilder->where('agent_name', 'like', "%{$q}%")->orWhere('code', 'like', "%{$q}%");
        })->get()->map(function ($a) {
            return [
                'id' => 'agent:'.$a->id,
                'text' => $a->agent_name,
                'subtitle' => ($a->city ? $a->city . ', ' : '') . (optional($a->country)->name ?? ''),
                'type' => 'agent',
                'port_code' => $a->port_code
            ];
        });

    $customers = \App\Models\Customer::with(['primaryAddress.country'])->orderBy('customer_name')
        ->when($q, function ($qbuilder) use ($q) {
            $qbuilder->where('customer_name', 'like', "%{$q}%");
        })->get()->map(function ($c) {
            $address = $c->primaryAddress;
            return [
                'id' => 'customer:'.$c->id,
                'text' => $c->customer_name,
                'subtitle' => ($address ? ($address->city ? $address->city . ', ' : '') . (optional($address->country)->name ?? '') : ''),
                'type' => 'customer',
                'port_code' => $address ? $address->port_code : null
            ];
        });

    // Group results by type
    $results = [];
    if ($hubs->isNotEmpty()) {
        $results[] = [
            'text' => 'Hubs',
            'children' => $hubs->toArray()
        ];
    }
    if ($agents->isNotEmpty()) {
        $results[] = [
            'text' => 'Agents',
            'children' => $agents->toArray()
        ];
    }
    if ($customers->isNotEmpty()) {
        $results[] = [
            'text' => 'Customers',
            'children' => $customers->toArray()
        ];
    }

    return response()->json($results);
});

// API: combined parties search for Consignee select2 (hubs, agents, offices, other_companies, suppliers, customers)
Route::get('/api/consignees', function (\Illuminate\Http\Request $request) {
    $q = $request->query('q');

    $hubs = \App\Models\Hub::orderBy('hub_name')
        ->when($q, function ($qbuilder) use ($q) {
            $qbuilder->where('hub_name', 'like', "%{$q}%")->orWhere('code', 'like', "%{$q}%");
        })->get()->map(function ($h) {
            return [
                'id' => 'hub:'.$h->id,
                'text' => $h->hub_name,
                'subtitle' => ($h->city ? $h->city . ', ' : '') . ($h->country ?? ''),
                'type' => 'hub',
                'address' => $h->hub_address,
                'city' => $h->city,
                'district' => $h->district_state,
                'zip' => $h->zip_code,
                'country' => $h->country,
                'port_code' => $h->port_code,
                'code' => $h->code,
                'email' => $h->email,
                'special_considerations' => $h->special_considerations,
            ];
        });

    $agents = \App\Models\Agent::orderBy('agent_name')
        ->when($q, function ($qbuilder) use ($q) {
            $qbuilder->where('agent_name', 'like', "%{$q}%")->orWhere('code', 'like', "%{$q}%");
        })->get()->map(function ($a) {
            return [
                'id' => 'agent:'.$a->id,
                'text' => $a->agent_name,
                'subtitle' => ($a->city ? $a->city . ', ' : '') . (optional($a->country)->name ?? ''),
                'type' => 'agent',
                'address' => $a->agent_address,
                'city' => $a->city,
                'district' => $a->district_state,
                'zip' => $a->zip_code,
                'country' => optional($a->country)->name,
                'port_code' => $a->port_code,
                'code' => $a->code,
                'email' => $a->email,
                'special_considerations' => $a->special_considerations,
            ];
        });

    $offices = \App\Models\Office::with('country')->orderBy('office_name')
        ->when($q, function ($qbuilder) use ($q) {
            $qbuilder->where('office_name', 'like', "%{$q}%")->orWhere('office_short_name', 'like', "%{$q}%");
        })->get()->map(function ($o) {
            return [
                'id' => 'office:'.$o->id,
                'text' => $o->office_name,
                'subtitle' => ($o->city ? $o->city . ', ' : '') . (optional($o->country)->name ?? ''),
                'type' => 'office',
                'address' => $o->address,
                'city' => $o->city,
                'district' => $o->district_state,
                'zip' => $o->zip_code,
                'country' => optional($o->country)->name,
                'port_code' => $o->port_code,
                'code' => $o->office_short_name,
                'email' => $o->email
            ];
        });

    $otherCompanies = \App\Models\OtherCompany::with('country')->orderBy('company_name')
        ->when($q, function ($qbuilder) use ($q) {
            $qbuilder->where('company_name', 'like', "%{$q}%")->orWhere('code', 'like', "%{$q}%");
        })->get()->map(function ($oc) {
            return [
                'id' => 'other_company:'.$oc->id,
                'text' => $oc->company_name,
                'subtitle' => ($oc->city ? $oc->city . ', ' : '') . (optional($oc->country)->name ?? ''),
                'type' => 'other_company',
                'address' => $oc->street_address,
                'city' => $oc->city,
                'district' => $oc->district_state,
                'zip' => $oc->zip_code,
                'country' => optional($oc->country)->name,
                'port_code' => $oc->port_code,
                'email' => $oc->email,
                'special_considerations' => $oc->special_considerations,
            ];
        });

    $suppliers = \App\Models\Supplier::with('country')->orderBy('supplier_name')
        ->when($q, function ($qbuilder) use ($q) {
            $qbuilder->where('supplier_name', 'like', "%{$q}%");
        })->get()->map(function ($s) {
            return [
                'id' => 'supplier:'.$s->id,
                'text' => $s->supplier_name,
                'subtitle' => ($s->city ? $s->city . ', ' : '') . (optional($s->country)->name ?? ''),
                'type' => 'supplier',
                'address' => $s->supplier_address,
                'city' => $s->city,
                'district' => $s->district_state,
                'zip' => $s->zip_code,
                'country' => optional($s->country)->name,
                'port_code' => $s->port_code,
                'email' => $s->email,
                'special_considerations' => $s->special_considerations,
            ];
        });

    $customers = \App\Models\Customer::with(['primaryAddress.country'])->orderBy('customer_name')
        ->when($q, function ($qbuilder) use ($q) {
            $qbuilder->where('customer_name', 'like', "%{$q}%");
        })->get()->map(function ($c) {
            $address = $c->primaryAddress;
            return [
                'id' => 'customer:'.$c->id,
                'text' => $c->customer_name,
                'subtitle' => ($address ? ($address->city ? $address->city . ', ' : '') . (optional($address->country)->name ?? '') : ''),
                'type' => 'customer',
                'address' => $address ? $address->street : null,
                'city' => $address ? $address->city : null,
                'district' => $address ? $address->state : null,
                'zip' => $address ? $address->zip_code : null,
                'country' => $address ? optional($address->country)->name : null,
                'port_code' => $address ? $address->port_code : null,
                'email' => $c->email,
                'special_considerations' => $c->special_considerations,
            ];
        });

    // Group results by type
    $results = [];
    if ($hubs->isNotEmpty()) {
        $results[] = [
            'text' => 'Hubs',
            'children' => $hubs->toArray()
        ];
    }
    if ($agents->isNotEmpty()) {
        $results[] = [
            'text' => 'Agents',
            'children' => $agents->toArray()
        ];
    }
    if ($offices->isNotEmpty()) {
        $results[] = [
            'text' => 'Offices',
            'children' => $offices->toArray()
        ];
    }
    if ($otherCompanies->isNotEmpty()) {
        $results[] = [
            'text' => 'Other Companies',
            'children' => $otherCompanies->toArray()
        ];
    }
    if ($suppliers->isNotEmpty()) {
        $results[] = [
            'text' => 'Suppliers',
            'children' => $suppliers->toArray()
        ];
    }
    if ($customers->isNotEmpty()) {
        $results[] = [
            'text' => 'Customers',
            'children' => $customers->toArray()
        ];
    }

    return response()->json($results);
});

// API: account managers from office contacts (all user types, grouped)
Route::get('/api/account-managers', function (\Illuminate\Http\Request $request) {
    $q = $request->query('q');

    $categoryLabels = [
        'operations' => 'Operations users',
        'account' => 'Accounting users',
        'sales' => 'Sales users',
        'manager' => 'Manager users',
    ];

    $allowedCategories = collect(explode(',', (string) $request->query('categories', '')))
        ->map(fn ($category) => trim($category))
        ->filter()
        ->intersect(array_keys($categoryLabels))
        ->values()
        ->all();

    if ($allowedCategories !== []) {
        $categoryLabels = array_intersect_key($categoryLabels, array_flip($allowedCategories));
    }

    $contacts = \App\Models\Contact::with('office')
        ->whereNotNull('office_id')
        ->when($allowedCategories !== [], function ($query) use ($allowedCategories) {
            $query->whereIn('category', $allowedCategories);
        })
        ->when($q, function ($qbuilder) use ($q) {
            $qbuilder->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        })
        ->orderBy('name')
        ->get();

    $mapContact = function ($contact) use ($categoryLabels) {
        $officeName = optional($contact->office)->office_name ?? '';
        $typeLabel = $categoryLabels[$contact->category] ?? ($contact->category ? ucfirst(str_replace('_', ' ', $contact->category)) : 'Other users');
        $subtitle = trim(($contact->email ? $contact->email : '') . ($officeName ? ($contact->email ? ' · ' : '') . $officeName : ''));

        return [
            'id' => $contact->id,
            'text' => $contact->name,
            'subtitle' => $subtitle,
            'type' => $contact->category,
            'type_label' => $typeLabel,
            'email' => $contact->email,
            'office_short_name' => optional($contact->office)->office_short_name ?? '',
        ];
    };

    $results = [];

    foreach ($categoryLabels as $category => $label) {
        $children = $contacts->where('category', $category)->map($mapContact)->values();
        if ($children->isNotEmpty()) {
            $results[] = [
                'text' => $label,
                'children' => $children->all(),
            ];
        }
    }

    $otherContacts = $contacts->filter(function ($contact) use ($categoryLabels) {
        return $contact->category === null || !array_key_exists($contact->category, $categoryLabels);
    })->map($mapContact)->values();

    if ($otherContacts->isNotEmpty()) {
        $results[] = [
            'text' => 'Other users',
            'children' => $otherContacts->all(),
        ];
    }

    return response()->json($results);
});

Route::get('/stocks', [App\Http\Controllers\CrrController::class, 'index'])->name('stocks');

Route::get('/stocks/create-crr', function () {
    $vessels = \App\Models\CustomerVessel::with('customer')
        ->where(function ($query) {
            $query->where('inactive_vessel', false)
                ->orWhereNull('inactive_vessel');
        })
        ->select('vessel', 'customer_id')
        ->groupBy('vessel', 'customer_id')
        ->get();
    $countries = \App\Models\Country::where('is_active', true)->orderBy('name')->get();
    $currencies = \App\Models\Country::where('is_active', true)->whereNotNull('currency')->distinct()->pluck('currency')->sort();
    $hubs = \App\Models\Hub::where(function ($query) {
        $query->where('hide_in_portal', false)
            ->orWhereNull('hide_in_portal');
    })->orderBy('hub_name')->get();
    $agents = \App\Models\Agent::with('country')
        ->where('is_active', true)
        ->orderBy('agent_name')
        ->get();
    $suppliers = \App\Models\Supplier::with('country')->orderBy('supplier_name')->get();
    return view('Stock.Create-CRR', compact('vessels', 'countries', 'currencies', 'hubs', 'agents', 'suppliers'));
})->name('create-crr');

Route::post('/stocks/create-crr', [App\Http\Controllers\CrrController::class, 'store'])->name('stocks.crr.store');

Route::get('/stocks/edit/{id}', [App\Http\Controllers\CrrController::class, 'edit'])->name('stocks.edit');
Route::put('/stocks/update/{id}', [App\Http\Controllers\CrrController::class, 'update'])->name('stocks.crr.update');
Route::get('/stocks/print-crr/{id}', [App\Http\Controllers\CrrController::class, 'showPrintCrr'])->name('stocks.print-crr');
Route::get('/stocks/print-labels/{id}', [App\Http\Controllers\CrrController::class, 'showPrintLabels'])->name('stocks.print-labels');
Route::get('/stocks/addPackages/{id}', [App\Http\Controllers\CrrController::class, 'addPackages'])->name('stocks.addPackages');
Route::post('/stocks/{id}/documents', [App\Http\Controllers\CrrController::class, 'uploadDocument'])->name('stocks.documents.upload');
Route::get('/stocks/{crrId}/documents/{docId}', [App\Http\Controllers\CrrController::class, 'showDocument'])->name('stocks.documents.show');
Route::patch('/stocks/documents/{docId}/type', [App\Http\Controllers\CrrController::class, 'updateDocumentType'])->name('stocks.documents.update-type');
Route::patch('/stocks/documents/{docId}/internal', [App\Http\Controllers\CrrController::class, 'updateDocumentInternal'])->name('stocks.documents.update-internal');
Route::delete('/stocks/documents/{docId}', [App\Http\Controllers\CrrController::class, 'deleteDocument'])->name('stocks.documents.delete');
Route::post('/stocks/{id}/status', [App\Http\Controllers\CrrController::class, 'updateStatus'])->name('stocks.crr.update-status');
Route::post('/stocks/{id}/flags', [App\Http\Controllers\CrrController::class, 'updateFlags'])->name('stocks.crr.update-flags');
Route::post('/stocks/{id}/accept', [App\Http\Controllers\CrrController::class, 'updateAccept'])->name('stocks.crr.update-accept');
Route::get('/stocks/print', [App\Http\Controllers\CrrController::class, 'printStockList'])->name('stocks.print');

Route::get('/stock-follow-up', [App\Http\Controllers\CrrController::class, 'stockFollowUp'])->name('stock-follow-up');

Route::get('/pickup-work-list', [App\Http\Controllers\CrrController::class, 'pickupWorkList'])->name('pickup-work-list');

Route::get('/etl-stock-items', function () {
    return view('Stock.etl-stock-items');
})->name('etl-stock-items');

Route::get('/offices/create', [App\Http\Controllers\OfficeController::class, 'create'])->name('offices.create');
Route::post('/offices/store', [App\Http\Controllers\OfficeController::class, 'store'])->name('offices.store');

Route::get('/hubs', [App\Http\Controllers\HubController::class, 'index'])->name('hub.index');
Route::get('/hubs/create', [App\Http\Controllers\HubController::class, 'create'])->name('hub.create');
Route::post('/hubs', [App\Http\Controllers\HubController::class, 'store'])->name('hub.store');

Route::get('/hubs/{id}', [App\Http\Controllers\HubController::class, 'show'])->name('hub.show');
Route::put('/hubs/{id}', [App\Http\Controllers\HubController::class, 'update'])->name('hub.update');
Route::patch('/hubs/{id}/status', [App\Http\Controllers\HubController::class, 'updateStatus'])->name('hub.status.update');
Route::delete('/hubs/{id}', [App\Http\Controllers\HubController::class, 'destroy'])->name('hub.destroy');
Route::post('/hubs/{id}/documents', [App\Http\Controllers\HubController::class, 'uploadDocument'])->name('hub.documents.upload');
Route::get('/hubs/documents/{type}/{docId}', [App\Http\Controllers\HubController::class, 'showDocument'])->name('hub.documents.show');
Route::delete('/hubs/documents/{docId}', [App\Http\Controllers\HubController::class, 'deleteDocument'])->name('hub.documents.delete');

Route::get('/hubs/{hub}/contacts/create', [App\Http\Controllers\HubController::class, 'createContact'])->name('hub.contacts.create');
Route::post('/hubs/{hub}/contacts', [App\Http\Controllers\HubController::class, 'storeContact'])->name('hub.contacts.store');
Route::get('/hubs/{hub}/contacts/{contact}/edit', [App\Http\Controllers\HubController::class, 'editContact'])->name('hub.contacts.edit');
Route::put('/hubs/{hub}/contacts/{contact}', [App\Http\Controllers\HubController::class, 'updateContact'])->name('hub.contacts.update');

// Hub User Routes
Route::get('/hubs/{hub}/users/create', [App\Http\Controllers\HubController::class, 'createUser'])->name('hub.users.create');
Route::post('/hubs/{hub}/users', [App\Http\Controllers\HubController::class, 'storeUser'])->name('hub.users.store');
Route::get('/hubs/{hub}/users/{user}/edit', [App\Http\Controllers\HubController::class, 'editUser'])->name('hub.users.edit');
Route::put('/hubs/{hub}/users/{user}', [App\Http\Controllers\HubController::class, 'updateUser'])->name('hub.users.update');

// Supplier Contact Routes
Route::get('/suppliers/{supplier}/contacts/create', [App\Http\Controllers\SupplierController::class, 'createContact'])->name('suppliers.contacts.create');
Route::post('/suppliers/{supplier}/contacts', [App\Http\Controllers\SupplierController::class, 'storeContact'])->name('suppliers.contacts.store');
Route::get('/suppliers/{supplier}/contacts/{contact}/edit', [App\Http\Controllers\SupplierController::class, 'editContact'])->name('suppliers.contacts.edit');
Route::put('/suppliers/{supplier}/contacts/{contact}', [App\Http\Controllers\SupplierController::class, 'updateContact'])->name('suppliers.contacts.update');

Route::resource('other-companies', App\Http\Controllers\OtherCompanyController::class);
Route::get('/other-companies/{other_company}/contacts/create', [App\Http\Controllers\OtherCompanyController::class, 'createContact'])->name('other-companies.contacts.create');
Route::post('/other-companies/{other_company}/contacts', [App\Http\Controllers\OtherCompanyController::class, 'storeContact'])->name('other-companies.contacts.store');
Route::get('/other-companies/{other_company}/contacts/{contact}/edit', [App\Http\Controllers\OtherCompanyController::class, 'editContact'])->name('other-companies.contacts.edit');
Route::put('/other-companies/{other_company}/contacts/{contact}', [App\Http\Controllers\OtherCompanyController::class, 'updateContact'])->name('other-companies.contacts.update');
Route::delete('/other-companies/{other_company}/contacts/{contact}', [App\Http\Controllers\OtherCompanyController::class, 'destroyContact'])->name('other-companies.contacts.destroy');

Route::get('/Vessels', [App\Http\Controllers\VesselController::class, 'index'])->name('vessels.index');

Route::get('/administration/change-logs', [App\Http\Controllers\AdministrationChangeLogController::class, 'index'])
    ->name('administration.change-logs');
Route::get('/administration/change-logs/search', [App\Http\Controllers\AdministrationChangeLogController::class, 'search'])
    ->name('administration.change-logs.search');


Route::get('/Vessels/create', function () {
    return view('vessels.create');
})->name('vessels.create');

// Route::get('/Contacts', function () {
//     return view('contacts.index');
// })->name('contacts.index');

Route::get('/customers/{customer}/contacts/create', [App\Http\Controllers\CustomerController::class, 'createContact'])->name('contacts.create');
Route::post('/customers/{id}/contacts', [App\Http\Controllers\CustomerController::class, 'storeContact'])->name('contacts.store');
Route::get('/contacts/{id}/edit', [App\Http\Controllers\CustomerController::class, 'editContact'])->name('contacts.edit');
Route::put('/contacts/{id}', [App\Http\Controllers\CustomerController::class, 'updateContact'])->name('contacts.update');
Route::delete('/contacts/{id}', [App\Http\Controllers\CustomerController::class, 'deleteContact'])->name('contacts.destroy');

Route::get('/contacts', function () {
    return view('contacts.index');
})->name('contacts.index');

Route::get('/Suppliers', [App\Http\Controllers\SupplierController::class, 'index'])->name('suppliers.index');

Route::get('/Suppliers/create', [App\Http\Controllers\SupplierController::class, 'create'])->name('suppliers.create');
Route::post('/Suppliers', [App\Http\Controllers\SupplierController::class, 'store'])->name('suppliers.store');

Route::get('/Suppliers/edit/{id}', [App\Http\Controllers\SupplierController::class, 'edit'])->name('suppliers.edit');
Route::put('/Suppliers/{id}', [App\Http\Controllers\SupplierController::class, 'update'])->name('suppliers.update');
Route::delete('/Suppliers/{id}', [App\Http\Controllers\SupplierController::class, 'destroy'])->name('suppliers.destroy');


Route::get('/Agents', [App\Http\Controllers\AgentController::class, 'index'])->name('agents.index');
Route::get('/Agents/create', [App\Http\Controllers\AgentController::class, 'create'])->name('agents.create');
Route::post('/Agents/store', [App\Http\Controllers\AgentController::class, 'store'])->name('agents.store');

Route::get('/Agents/edit/{id}', [App\Http\Controllers\AgentController::class, 'edit'])->name('agents.edit');
Route::put('/Agents/update/{id}', [App\Http\Controllers\AgentController::class, 'update'])->name('agents.update');
Route::patch('/Agents/{id}/status', [App\Http\Controllers\AgentController::class, 'updateStatus'])->name('agents.status.update');
Route::delete('/Agents/{id}', [App\Http\Controllers\AgentController::class, 'destroy'])->name('agents.destroy');
Route::delete('/Agents/documents/{id}', [App\Http\Controllers\AgentController::class, 'deleteDocument'])->name('agents.documents.delete');
Route::get('/Agents/{agentId}/documents/{docId}', [App\Http\Controllers\AgentController::class, 'showDocument'])->name('agents.documents.show');

Route::get('/Agents/{id}/contacts/create', function ($id) {
    return view('Agents.contacts.create', ['agent_id' => $id]);
})->name('agents.contacts.create');

Route::post('/Agents/{id}/contacts/store', [App\Http\Controllers\AgentController::class, 'storeContact'])->name('agents.contacts.store');
Route::get('/Agents/contacts/edit/{id}', [App\Http\Controllers\AgentController::class, 'editContact'])->name('agents.contacts.edit');
Route::put('/Agents/contacts/update/{id}', [App\Http\Controllers\AgentController::class, 'updateContact'])->name('agents.contacts.update');
Route::delete('/Agents/contacts/destroy/{id}', [App\Http\Controllers\AgentController::class, 'destroyContact'])->name('agents.contacts.destroy');
Route::get('/Agents/{id}/users/create', function ($id) {
    return view('Agents.Users.create', ['agent_id' => $id]);
})->name('agents.users.create');
Route::post('/Agents/{id}/users/store', [App\Http\Controllers\AgentController::class, 'storeUser'])->name('agents.users.store');
Route::get('/Agents/users/edit/{id}', [App\Http\Controllers\AgentController::class, 'editUser'])->name('agents.users.edit');
Route::put('/Agents/users/update/{id}', [App\Http\Controllers\AgentController::class, 'updateUser'])->name('agents.users.update');
Route::delete('/Agents/users/destroy/{id}', [App\Http\Controllers\AgentController::class, 'destroyUser'])->name('agents.users.destroy');

Route::get('/Agents/company-agent/{id}', function ($id) {
    return view('Agents.company-agent', compact('id'));
})->name('agents.company-agent');
Route::get('/update-currency-rates', [App\Http\Controllers\CurrencyController::class, 'updateRates'])->name('currency.update');
    });
});
