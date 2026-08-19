# MarineCaddie — Project Standards & Architecture Guide

> **IMPORTANT:** Is file ko har non-trivial change se **PEHLE** padho.  
> Yahan likha pattern follow karo — doosri functionality tootne ka risk khatam hoga.

---

## Table of Contents

1. [Environments & URLs](#1-environments--urls)
2. [Tech Stack](#2-tech-stack)
3. [Project Structure](#3-project-structure)
4. [Application Modules & Routes](#4-application-modules--routes)
5. [Request / Auth Flow](#5-request--auth-flow)
6. [List Page Pattern (CRITICAL)](#6-list-page-pattern-critical)
7. [Create / Edit Page Pattern](#7-create--edit-page-pattern)
8. [Select2 & Lookup Fields](#8-select2--lookup-fields)
9. [DataTables 1.10.20 Rules](#9-datatables-11020-rules)
10. [SweetAlert v1 Rules](#10-sweetalert-v1-rules)
11. [Repository Pattern](#11-repository-pattern)
12. [Services vs Controllers](#12-services-vs-controllers)
13. [Validation & Mass Assignment](#13-validation--mass-assignment)
14. [Database & Query Rules](#14-database--query-rules)
15. [File Storage](#15-file-storage)
16. [Mail Flows](#16-mail-flows)
17. [Security Rules](#17-security-rules)
18. [Performance Rules](#18-performance-rules)
19. [QA & Manual Testing](#19-qa--manual-testing)
20. [Git Rules](#20-git-rules)
21. [Production Deploy](#21-production-deploy)
22. [Pre-Change Checklist](#22-pre-change-checklist)
23. [Common Mistakes](#23-common-mistakes)

---

## 1. Environments & URLs

| Environment | URL | Notes |
|---|---|---|
| **Production** | `https://portal.marinecaddie.com` | Hostinger, PHP 8.4 |
| **Local (XAMPP)** | `http://localhost/laravel` | `.env` may point to live DB — be careful |

- Local `.env` changes **never commit** karo.
- Production deploy = `git pull` on server + cache commands (Section 21).

---

## 2. Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Blade, jQuery, Bootstrap |
| Database | MySQL |
| DataTables | **1.10.20** (legacy — strict rules apply) |
| Alerts | SweetAlert v1 (`swal`) |
| Multiselect filters | Bootstrap Multiselect |
| Dropdowns | Select2 |
| PDF | DomPDF, FPDF/FPDI |
| Mail | SMTP (Office365) |

---

## 3. Project Structure

```
app/
  Http/
    Controllers/          — Thin controllers (validate → repository/service → response)
    Middleware/           — auth, otp.verified, ops.admin.readonly, admin
  Models/                 — Eloquent models + casts/fillable
  Repositories/
    Contracts/            — Repository interfaces
    Criteria/             — Reusable query criteria (optional)
    Support/              — PaginateOptions, helpers
    BaseRepository.php    — Shared CRUD/query helpers
    *Repository.php       — Entity-specific data access
  Services/               — Business logic, PDF builders, mail composers
  Support/
    CountryCache.php      — Cached country/currency lists
    ListSearch.php        — LIKE prefix/contains helpers
    PrivateDisk.php       — Private file storage wrapper
  Console/Commands/       — Scheduled/maintenance commands

resources/views/
  Agents/, customers/, hub/, Suppliers/, offices/, Shipment/, Stock/, Vessels/
    index.blade.php       — List + filters
    partials/rows.blade.php — AJAX tbody rows (only <tr> tags)
    create.blade.php / edit.blade.php / show.blade.php
  partials/
    searchable-filter-multiselect-script.blade.php  ← bindAjaxListFilters()
    searchable-filter-multiselect-styles.blade.php

routes/web.php            — All web routes + inline API lookups (ports, parties)
config/app.php            — App config incl. local_otp_bypass flag

scripts/                  — Local QA helpers (not for production runtime)
  browser-qa.js
  check_db.php
  seed_ops_qa_data.php
```

---

## 4. Application Modules & Routes

### Administration (master data)

| Module | List URL | Notes |
|---|---|---|
| Agents | `/Agents` | CRUD + tabs (billing, SOP, contacts, etc.) |
| Customers | `/customers` | CRUD + vessels, documents |
| Hubs | `/hubs` | CRUD via `hub.show` edit page |
| Suppliers | `/Suppliers` | CRUD |
| Other Companies | `/other-companies` | CRUD |
| Offices | `/offices` | Create/edit (no list delete) |
| Vessels | `/Vessels` | List/create only |
| Users | `/users` | Admin-only, modal CRUD |

### Operations

| Module | List URL | Notes |
|---|---|---|
| Shipments | `/shipments` | Heavy edit page, manifests, pre-alerts, mail |
| Stocks (CRR) | `/stocks` | Create via `/stocks/create-crr`, edit tabs |
| Dashboard | `/dashboard` | Operations overview |

### Middleware layers (in order)

```
guest → auth → otp.verified → ops.admin.readonly → (admin for /users)
```

- **`otp.verified`:** Login ke baad OTP mandatory (production).
- **`ops.admin.readonly`:** Operations role ko administration write block (`DenyOperationsAdministrationWrite`).
- **`admin`:** User management routes.

---

## 5. Request / Auth Flow

```
Login (email + password + geolocation)
  → OTP page (/otp) — email code
  → otp.verify → session otp_verified = true
  → Dashboard / intended URL
```

### Login rules

- Login form requires browser geolocation (`browser_latitude`, `browser_longitude`).
- CSRF token refresh on submit via `/login/csrf`.
- Inactive users (`is_active = 0`) cannot log in.

### Local OTP bypass (QA only)

```env
# .env — LOCAL ONLY, never on production
LOCAL_OTP_BYPASS=true
```

- Controlled by `config('app.local_otp_bypass')`.
- **Ignored in `production` environment** even if env is set.
- Used for automated/manual browser QA on localhost.

---

## 6. List Page Pattern (CRITICAL)

**Har list page same pattern follow karta hai. Mat todna.**

### Flow

```
Browser → Controller index()
       → Repository paginate(filters)
       → Full view OR AJAX JSON { html, pagination, total }
       → partials/rows.blade.php renders <tr> rows
       → bindAjaxListFilters() handles filter/pagination AJAX
```

### 6a. Controller `index()`

```php
public function index(Request $request)
{
    $perPage = max(10, min(100, (int) $request->input('per_page', 25)));
    $filters = array_merge(
        $request->only(['name', 'search', 'country']),
        ['hide_inactive' => $request->boolean('hide_inactive', false)] // DEFAULT false
    );

    $items = $this->repository->paginate($filters, $perPage);

    if ($request->ajax()) {
        return response()->json([
            'html'       => view('Module.partials.rows', compact('items'))->render(),
            'pagination' => (string) $items->links(),
            'total'      => $items->total(),
        ]);
    }

    return view('Module.index', compact('items', /* filter dropdown data */));
}
```

### 6b. `partials/rows.blade.php`

```blade
@forelse($items as $item)
    <tr>
        <td>{{ $item->name }}</td>
        {{-- td count MUST match thead th count in index.blade.php --}}
    </tr>
@empty
    <tr>
        <td colspan="N" class="text-center py-4 text-muted">No items found.</td>
    </tr>
@endforelse
```

**RULE:** `colspan` = exact `<th>` count in `<thead>`.

### 6c. JS — `bindAjaxListFilters`

Shared helper: `resources/views/partials/searchable-filter-multiselect-script.blade.php`

```javascript
window.myListFilters = bindAjaxListFilters({
    tableSelector:      '#my-table',
    paginationSelector: '#my-pagination',
    indexUrl:           @json(route('module.index')),
    existingTable:      table,  // DataTables instance if used
    getParams: function(page) {
        return {
            search: $.trim($('#search-input').val() || ''),
            hide_inactive: $('#hide-inactive').is(':checked') ? 1 : 0,
            page: page || 1
        };
    },
    textSelectors:   '#search-input',
    changeSelectors: '#filter-select, #hide-inactive',
    resetFields: function() {
        $('#search-input').val('');
        $('#filter-select').val(null).trigger('change');
    }
});
```

### 6d. AJAX response contract

Every list filter request **must** return:

```json
{
  "html": "<tr>...</tr>",
  "pagination": "<nav>...</nav>",
  "total": 42
}
```

### 6e. `hide_inactive` filter

```php
// Controller — default MUST be false (show all)
$hideInactive = $request->boolean('hide_inactive', false);

// Query — use integer 1, not boolean true
->when($hideInactive, fn ($q) => $q->where('is_active', 1))
```

Checkbox checked = sirf active records. Unchecked/default = sab records.

---

## 7. Create / Edit Page Pattern

### Standard form flow

```
GET create/edit → Blade form with @csrf
POST/PUT → Controller validate → Repository create/update → redirect with flash
```

### Edit pages with tabs

Modules like Agent, Hub, Customer, Shipment, Stock use tabbed edit UI:

- Tab buttons: `.tab-item[data-tab="tab-id"]` or `.nav-tab-item[data-target="tab-id"]`
- Tab panels: `#tab-id` or `#tab-id.tab-panel`
- Hidden field `active_tab` on update forms — redirect back to same tab after save
- URL hash support: `#billing-details` restores tab on load

**Before adding a tab:** ensure save/update handler persists tab-specific fields and validation covers them.

### Delete from list

- SweetAlert confirm → AJAX `DELETE` with CSRF header
- SweetAlert callback mein **destroyed DataTables** par `.draw()` / `.invalidate()` mat karo
- Success par list row remove ya `bindAjaxListFilters` reload

---

## 8. Select2 & Lookup Fields

### Country dropdown (flag)

```javascript
$('.select2-flag').select2({
    placeholder: 'Select Country',
    allowClear: true,
    width: '100%',
    templateResult: formatFlag,
    templateSelection: formatFlag
});
```

Data source: `CountryCache::active()` or `CountryCache::activeRaw()` in controller.

### Port code (from `ports` table)

Use class `select2-port-code` on Agent, Hub, Customer, Supplier, Other Company, Shipment forms.

```javascript
$('.select2-port-code').select2({
    placeholder: 'Search port code',
    width: '100%',
    minimumInputLength: 0,
    ajax: {
        url: '{{ route('api.ports') }}',
        dataType: 'json',
        delay: 200,
        data: params => ({ q: params.term || '' }),
        processResults: data => ({ results: data.results || [] })
    },
    templateResult: formatPortResult,      // code + city, country subtitle
    templateSelection: formatPortSelection
});
```

API: `GET /api/ports?q=ABC` → `{ results: [{ id, text, code, city, country }] }`

**Edit pages:** pre-select saved value:

```blade
@if (old('port_code', $model->port_code))
    <option value="{{ old('port_code', $model->port_code) }}" selected>
        {{ old('port_code', $model->port_code) }}
    </option>
@endif
```

### Party lookups (Shipment)

- Departure: `GET /api/parties`
- Consignee: `GET /api/consignees`
- Composite IDs: `hub:1`, `agent:2`, `customer:3`

---

## 9. DataTables 1.10.20 Rules

**Version 2019 — newer API features crash karte hain.**

### Correct config

```javascript
$('#my-table').DataTable({
    dom: 'rt',
    paging: false,
    info: false,
    searching: false,
    ordering: true,
    autoWidth: false,
    columnDefs: [
        { orderable: false, targets: 8 }  // INTEGER — not [8]
    ]
});
```

### Wrong (crashes)

```javascript
columnDefs: [{ orderable: false, targets: [8] }]  // array = crash
scrollX: true  // crash if parent hidden
table.rows.add($rows)  // DOM nodes don't work in 1.10
```

### After AJAX row replace

```javascript
table.destroy(true);
$('#my-table tbody').html(html);
table = $('#my-table').DataTable(dtConfig);
```

### Stub when DT not used but code references `table`

```javascript
var table = {
    columns: { adjust: function() {} },
    row: function() {
        return { invalidate: function() { return { draw: function() {} }; } };
    }
};
```

**Prefer:** newer list pages use `bindAjaxListFilters` without DataTables entirely.

---

## 10. SweetAlert v1 Rules

```javascript
swal({
    title: 'Are you sure?',
    type: 'warning',
    showCancelButton: true,
    closeOnConfirm: false,      // required
    showLoaderOnConfirm: true
}, function(isConfirm) {
    if (!isConfirm) return;

    $.ajax({ /* ... */ }).done(function(response) {
        swal('Done!', 'Success message', 'success');  // this closes dialog
    }).fail(function() {
        swal('Error', 'Something went wrong.', 'error');
    });
});
```

- Callback mein JS error = alert stuck forever.
- Success callback mein DataTables calls tabhi karo jab table instance valid ho.

---

## 11. Repository Pattern

**Project-wide standard.** Controllers direct `Model::query()` use nahi karte — repository inject karo.

### Layer responsibilities

| Layer | Responsibility |
|---|---|
| **Controller** | HTTP: validate input, call repository/service, return view/JSON/redirect |
| **Repository** | Data access: queries, filters, pagination, CRUD |
| **Service** | Business logic: PDF generation, mail compose, complex multi-step workflows |
| **Model** | Eloquent: relationships, casts, accessors, scopes |

### Base classes

```
App\Repositories\Contracts\BaseRepositoryInterface
App\Repositories\BaseRepository
```

Shared methods:

| Method | Purpose |
|---|---|
| `query()` | New Eloquent builder |
| `findModelOrFail($id, $with)` | Find with optional eager load |
| `create($data)` | Insert |
| `updateModel($model, $data)` | Update (named to avoid child signature clash) |
| `deleteById($id)` | Delete |
| `paginateQuery($query, $perPage)` | Paginate builder |
| `transaction($callback)` | DB::transaction wrapper |
| `applyCriteria($query, $criteria)` | Criteria pipeline |

### New repository checklist

1. Create `App\Repositories\Contracts\XxxRepositoryInterface`
2. Create `App\Repositories\XxxRepository extends BaseRepository`
3. Set `protected string $modelClass = Xxx::class;`
4. Bind in `AppServiceProvider::register()`
5. Inject interface in controller constructor
6. Move query logic from controller to repository methods (`paginate`, `findWithRelations`, etc.)

### Example

```php
// Contract
interface HubRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator;
    public function findOrFail(int $id): Hub;
    public function create(array $data): Hub;
    public function update(Hub $hub, array $data): bool;
}

// Controller
public function __construct(private HubRepositoryInterface $hubs) {}

public function index(Request $request)
{
    $hubs = $this->hubs->paginate($request->all(), 25);
    // ...
}
```

### Naming rules

- Interface: `{Entity}RepositoryInterface`
- Implementation: `{Entity}Repository`
- Method `paginate()` on child repos is OK — **do not** add generic `paginate()` on `BaseRepository` (signature clash)
- Method `update($model, $data)` on child repos is OK — base uses `updateModel()`

---

## 12. Services vs Controllers

Use **Services** when logic involves:

- PDF generation (manifest, pre-alert, combined PO)
- Mail preview + send (ManifestMailService, PreAlertMailService)
- Multi-model transactions with side effects
- External API calls (currency rates)
- Change log snapshots

Use **Repository** when logic is:

- Filtered lists
- CRUD
- Simple lookups / existence checks

**Never** put 200+ lines of query logic in controllers — extract to repository.

---

## 13. Validation & Mass Assignment

### Always validate before save

```php
$validated = $request->validate([
    'hub_name'       => 'required|string|max:255',
    'contact_person' => 'required|string|max:255',
    'email'          => ['nullable', 'string', 'max:255', $this->multipleEmailsValidator()],
]);
```

### Mass assignment

```php
// ✅ Correct
$this->repository->create($request->validated());
$this->repository->update($model, $request->only([...]));

// ❌ Wrong
Model::create($request->all());
```

### File uploads

Always validate `mimes` and `max` size:

```php
'files.*' => 'nullable|file|max:20480|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp,eml,msg',
```

### Blade output

User-controlled strings in views: use `{{ }}` (escaped). PDF/HTML builders: use `e()` for dynamic labels.

---

## 14. Database & Query Rules

### ListSearch helper

```php
use App\Support\ListSearch;

// Large tables (shipments, stocks) — prefix search (uses index)
->when($term, fn ($q) => $q->where('shipment_number', 'like', ListSearch::prefix($term)))

// Small master data — contains search OK
->when($term, fn ($q) => $q->where('agent_name', 'like', ListSearch::contains($term)))
```

### `is_active` / booleans

```php
->where('is_active', 1)   // preferred
$request->boolean('hide_inactive', false)  // filter default false
```

### N+1 prevention

List and edit queries must `->with([...])` required relations in repository.

### Indexes

New filterable/sortable column → add DB index in migration.

### Transactions

Multi-table writes (Shipment + CRR attach, CRR + packages) → `DB::transaction()` or `$repo->transaction()`.

---

## 15. File Storage

```php
use App\Support\PrivateDisk;

// Store
$path = $file->store('folder/subfolder', 'private');

// Delete (always when removing DB record)
PrivateDisk::delete($document->file_path);

// Download
PrivateDisk::downloadResponse($path, $filename);
```

**Never** use raw `Storage::delete()` for private documents.

Applies to: hub/agent/customer documents, shipment documents, manifests, pre-alerts, CRR documents.

---

## 16. Mail Flows

### Shipment mail (browser-triggered, AJAX)

| Action | Route pattern |
|---|---|
| Manifest prepare | `POST /shipments/{id}/manifest-mail/prepare` |
| Manifest send | `POST /shipments/{id}/manifest-mail/send` |
| Pre-alert prepare | `POST /shipments/{id}/pre-alert-mail/prepare` |
| Pre-alert send | `POST /shipments/{id}/pre-alert-mail/send` |
| Pre-alert reminder | `POST /shipments/{id}/pre-alert-reminder-mail/dispatch` |
| Delivery status reminder | `POST /shipments/{id}/delivery-status-reminder-mail/send` |
| Invoice request | `POST /shipments/{id}/invoice-request-mail/send` |

Send endpoints require: `to`, `subject`; optional `cc`, `bcc`, `body`, `files[]`.

### Login OTP mail

- Sent on login via `OtpController::issueOtp()`
- Local env may show OTP on screen when mail delivery unavailable

### Scheduled mail/commands

- `currency:update-rates` — hourly via scheduler
- Scheduler: `* * * * * php artisan schedule:run`

---

## 17. Security Rules

| Rule | Implementation |
|---|---|
| Auth on all app routes | `auth` + `otp.verified` middleware |
| Admin routes protected | `admin` middleware on `/users` |
| Ops read-only on admin modules | `DenyOperationsAdministrationWrite` middleware |
| Mass assignment | `$request->validated()` / explicit `$request->only()` |
| File upload validation | `mimes` + `max` on all upload endpoints |
| Private files | `PrivateDisk` + auth-checked download routes |
| CSRF | All forms + AJAX POST with `X-CSRF-TOKEN` |
| Sensitive routes | e.g. `/update-currency-rates` inside `auth` group |
| `.env` secrets | Never commit; rotate if exposed |
| Production OTP | Always required; `LOCAL_OTP_BYPASS` production mein ignored |
| XSS in PDF/views | Escape dynamic strings with `e()` |

---

## 18. Performance Rules

### CountryCache (mandatory)

```php
use App\Support\CountryCache;

$countries  = CountryCache::active();      // Eloquent collection
$countries  = CountryCache::activeRaw();   // stdClass rows (Hub views)
$currencies = CountryCache::currencies();

CountryCache::flush();  // after country CRUD
```

**Never** `Country::where('is_active', true)->get()` directly in controllers.

### Query performance

- Prefix `LIKE` on large tables (`ListSearch::prefix`)
- Eager load on list/edit (`->with()`)
- Add indexes for filtered columns
- Avoid queries inside Blade loops

### Production cache (after deploy)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### OPcache

Enable on production PHP for faster bootstrap.

---

## 19. QA & Manual Testing

### Automated regression tests (mandatory before push)

Har non-trivial change ke baad **PHPUnit suite green honi chahiye**:

```bash
composer test
# same as: php artisan test
```

**Suite covers (minimum):**

| Area | Tests |
|---|---|
| Auth | login page, valid/invalid login, guest redirects |
| Security | response security headers |
| Agents | list page + AJAX filter contract `{ html, pagination, total }` |
| API | `/api/ports` lookup + auth required |
| Dashboard | operations KPI calculations |

**Rules:**

- Naya module / route / filter add karo → us module ka Feature test bhi add karo
- Test DB = SQLite in-memory (`phpunit.xml`) — production DB kabhi use mat karo
- OTP in tests = `LOCAL_OTP_BYPASS=true` (phpunit env only; production pe ignored)
- Shared schema helpers: `tests/RegressionTestCase.php`, `tests/Concerns/*`

**When tests fail:** fix code or update test — push mat karo jab tak green na ho.

### Minimum smoke test (every change)

1. Open affected page on `http://localhost/laravel`
2. Test list load + at least one filter
3. Test create OR update (whichever changed)
4. Test delete/AJAX action if applicable
5. Check Laravel log for errors: `storage/logs/laravel.log`

### Never say "verified" based only on

- `php -l` syntax check
- grep/search without running the page
- Assuming AJAX works without clicking filters

### Local QA scripts (optional)

```bash
php scripts/check_db.php          # core tables exist
php scripts/seed_ops_qa_data.php  # seed test stock + shipment
node scripts/browser-qa.js        # automated browser smoke (needs Playwright)
```

Requires `LOCAL_OTP_BYPASS=true` in local `.env` for headless login.

### Import/model removal safety

Before removing any class/import, search **all** usages:

```
Country::
new Country
use App\Models\Country
type-hints referencing the class
```

---

## 20. Git Rules

- **Bina user ke kehne `git push` mat karo**
- Commit message clear aur descriptive (why, not just what)
- `.env`, credentials, API keys — **never commit**
- Branch workflow: feature branch → user review → push
- After push, user decides production deploy timing

---

## 21. Production Deploy

### Steps

```bash
cd /home/u887677533/domains/portal.marinecaddie.com/public_html
git pull origin main   # or merged feature branch
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

### Cron (Hostinger hPanel)

```
* * * * * cd /home/u887677533/domains/portal.marinecaddie.com/public_html && /opt/alt/php84/usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

Currency rates log: `grep "Currency rates updated" storage/logs/laravel.log | tail -10`

### Post-deploy verify

- Login + OTP flow
- One list page filter
- One critical operations action (shipment list load)

---

## 22. Pre-Change Checklist

```
[ ] PROJECT_STANDARDS.md (this file) — affected section read?
[ ] Module route + controller + repository identified?
[ ] partials/rows.blade.php td count == thead th count?
[ ] bindAjaxListFilters getParams sends all active filters?
[ ] hide_inactive default = false in controller?
[ ] DataTables columnDefs targets = integer (not array)?
[ ] SweetAlert callback safe (no broken DT calls)?
[ ] PrivateDisk used for file delete?
[ ] CountryCache used (not direct Country query)?
[ ] Repository used for data access (not Model:: in controller)?
[ ] Validation uses validated()/only() — not all()?
[ ] Removed import/model — all usages searched?
[ ] Affected page opened on localhost and manually tested?
[ ] Form submit / AJAX / filter / delete flow tested?
[ ] Only say "verified" after actual browser test
[ ] User asked before git push?
```

---

## 23. Common Mistakes

| Mistake | Fix |
|---|---|
| `hide_inactive` default `true` | Default must be `false` |
| `targets: [8]` in DataTables | Use `targets: 8` |
| `table.rows.add($rows)` | Replace tbody HTML instead |
| SweetAlert stuck | JS error in callback — check console |
| Email in `un_locode` field | Add `autocomplete="off"` on non-email fields |
| `Storage::delete()` for private files | Use `PrivateDisk::delete()` |
| `Country::all()` in controller | Use `CountryCache::active()` |
| `Model::create($request->all())` | Use `$request->validated()` |
| Filter checkbox not in AJAX params | Add to `getParams()` in bindAjaxListFilters |
| Tab fields not saved | Check validation + `$fillable` + active_tab redirect |
| Port code free text | Use `select2-port-code` + `api.ports` |
| OTP bypass on production | Never set `LOCAL_OTP_BYPASS` on production |
| Push without user permission | Wait for explicit "push karo" |

---

*Last updated: automated regression test suite, login throttle, security headers, repository pattern.*
