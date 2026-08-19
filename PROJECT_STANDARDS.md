# MarineCaddie — Project Standards & Architecture Guide

> **IMPORTANT:** Is file ko har change se PEHLE padho. Koi bhi naya feature ya fix karne se pehle
> yahan likha pattern follow karo — doosri functionality tootne ka risk khatam hoga.

---

## 1. Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12, PHP 8.2 |
| Frontend | Blade templates, jQuery, Bootstrap |
| Database | MySQL (Production: Hostinger `u887677533_globalSaf`) |
| DataTables | **Version 1.10.20** (purani — specific rules hain, neeche dekho) |
| Alerts | SweetAlert v1 (swal function) |
| Multiselect | Bootstrap Multiselect plugin |
| PDF | DomPDF, FPDF/FPDI |

---

## 2. Project Folder Structure

```
app/
  Http/Controllers/       — AgentController, ShipmentController, etc.
  Models/                 — Agent, Shipment, Stock, Hub, Vessel, etc.
  Support/
    ListSearch.php        — LIKE query helpers (prefix/contains)
    PrivateDisk.php       — Private file storage helper
  Services/
    CurrencyRateService.php
    UserNotificationService.php
  Console/Commands/       — UpdateCurrencyRatesCommand

resources/views/
  Agents/
    index.blade.php       — List page
    partials/rows.blade.php — AJAX tbody rows (sirf <tr> tags)
    edit.blade.php
    create.blade.php
  Shipment/               — Same pattern
  Stock/                  — Same pattern
  Vessels/                — Same pattern
  Suppliers/              — Same pattern
  Other Companies/        — Same pattern
  hub/                    — Same pattern
  customers/              — Same pattern
  partials/
    searchable-filter-multiselect-script.blade.php  ← SHARED JS helper
    searchable-filter-multiselect-styles.blade.php  ← SHARED CSS
```

---

## 3. List Page Pattern (CRITICAL — sabse important)

**Har list page ka ek standard pattern hai. Is pattern ko kabhi mat todna.**

### 3a. Controller (index method)
```php
public function index(Request $request)
{
    // 1. Input lena
    $search = trim($request->input('search', ''));
    $hideInactive = $request->boolean('hide_inactive', false); // DEFAULT false

    // 2. Query banana
    $items = Model::query()
        ->when($search !== '', fn($q) => $q->where('name', 'like', '%'.$search.'%'))
        ->when($hideInactive, fn($q) => $q->where('is_active', 1)) // boolean nahi, integer 1
        ->paginate(25);

    // 3. AJAX request check — HTML + pagination return karo
    if ($request->ajax()) {
        return response()->json([
            'html'       => view('Module.partials.rows', compact('items'))->render(),
            'pagination' => (string) $items->links(),
            'total'      => $items->total(),
        ]);
    }

    // 4. Normal request — full view return karo
    return view('Module.index', compact('items'));
}
```

### 3b. Partial rows file (`partials/rows.blade.php`)
```blade
@forelse($items as $item)
    <tr>
        <td>{{ $item->name }}</td>
        {{-- ... exact same number of <td> as <th> in index.blade.php thead --}}
    </tr>
@empty
    <tr>
        <td colspan="N" class="text-center py-4 text-muted">No items found.</td>
    </tr>
@endforelse
```
**RULE:** `colspan` mein wahi number dalo jo `<thead>` mein `<th>` count hai.

### 3c. Index view JS — `bindAjaxListFilters` helper use karo
```javascript
// SHARED helper — partials/searchable-filter-multiselect-script.blade.php
window.myListFilters = bindAjaxListFilters({
    tableSelector:      '#my-table',
    paginationSelector: '#my-pagination',
    indexUrl:           @json(route('module.index')),
    existingTable:      table,       // DataTables instance (agar hai)
    getParams: function(page) {
        return {
            search: $.trim($('#search-input').val() || ''),
            page:   page || 1
        };
    },
    textSelectors:   '#search-input',
    changeSelectors: '#filter-select',
    resetFields: function() {
        $('#search-input').val('');
        $('#filter-select').val(null).trigger('change');
    }
});
```

---

## 4. DataTables 1.10.20 — Specific Rules

**Yeh version 2019 ka hai. Newer version ke features kaam nahi karte.**

### ✅ Sahi tarika:
```javascript
$('#my-table').DataTable({
    "dom":          'rt',
    "paging":       false,
    "info":         false,
    "searching":    false,
    "ordering":     true,
    "autoWidth":    false,
    "columnDefs": [
        { "orderable": false, "targets": 8 }  // ← INTEGER, array [8] nahi
    ]
});
```

### ❌ Galat — crash karta hai:
```javascript
"columnDefs": [{ "orderable": false, "targets": [8] }]  // array crash karega 1.10.x mein
"scrollX": true  // bhi crash kar sakta hai agar parent hidden ho
```

### AJAX ke baad rows replace karna:
```javascript
// ✅ Sahi: destroy + tbody replace + reinit
table.destroy(true);
$('#wrapper').html('<table id="my-table" class="..."></table>');
$('#my-table').html(theadHtml + '<tbody>' + html + '</tbody>');
table = $('#my-table').DataTable(dtConfig);

// ❌ Galat: rows.add() kaam nahi karta DOM nodes se DT 1.10 mein
table.rows.add($rows);
```

### table variable stub (jab DataTables nahi use karna):
```javascript
// Agar DataTables nahi use kar rahe but baaki code table.x call karta hai:
var table = { 
    columns: { adjust: function(){} },
    row: function() { return { invalidate: function() { return { draw: function(){} }; } }; }
};
```

---

## 5. SweetAlert v1 Rules

```javascript
// ✅ Confirmation dialog (closeOnConfirm: false — manually close karo)
swal({
    title: 'Are you sure?',
    type: 'warning',
    showCancelButton: true,
    closeOnConfirm: false,   // ← important
    showLoaderOnConfirm: true
}, function(isConfirm) {
    if (!isConfirm) return;

    $.ajax({ ... }).done(function(response) {
        swal('Done!', 'Success message', 'success'); // ← yeh close karega
    });
});

// ❌ Galat — AJAX ke andar koi bhi JS error ho to swal kabhi close nahi hoga
// Isliye AJAX success callback mein koi bhi .invalidate(), .draw() call
// DataTables ke bina mat karo
```

---

## 6. Boolean / is_active DB Field Rules

**Production DB mein `is_active` tinyint(1) hai.**

```php
// ✅ Sahi query
->where('is_active', 1)       // active records
->where('is_active', 0)       // inactive records

// ⚠️ Ye bhi kaam karta hai (Model mein boolean cast ho to)
->where('is_active', true)

// Controller mein default:
$hideInactive = $request->boolean('hide_inactive', false); // DEFAULT false — sab dikhao
```

**Note:** Production mein naye records ka `is_active` migration default se aata hai.
Purane records (migration se pehle ke) manually update karne padenge:
```bash
php artisan tinker --execute="DB::table('agents')->update(['is_active' => 1]);"
```

---

## 7. File Storage Rules

```php
// Private files (manifests, pre-alerts, documents):
use App\Support\PrivateDisk;

$path = $file->store('folder_name', 'private');  // store
PrivateDisk::delete($document->file_path);        // delete (DB record alag hatao)
PrivateDisk::downloadResponse($path, $filename);  // download

// ❌ Storage::delete() seedha mat use karo — PrivateDisk use karo
```

---

## 8. AJAX Filter — ListSearch Helper

```php
use App\Support\ListSearch;

// Large tables (shipments, stocks) — index use karta hai, fast
->when($term, fn($q) => $q->where('shipment_number', 'like', ListSearch::prefix($term)))

// Small master data tables (agents, hubs, vessels) — contains search
->when($term, fn($q) => $q->where('name', 'like', ListSearch::contains($term)))
```

---

## 9. Repository Pattern (New Code Only)

**Existing code touch nahi karna — sirf naye modules/features mein follow karo.**

### Structure
```
app/
  Repositories/
    Contracts/
      AgentRepositoryInterface.php   ← Interface
    AgentRepository.php              ← Implementation
  Services/
    AgentService.php                 ← Business logic (optional, heavy logic ke liye)
```

### Interface
```php
namespace App\Repositories\Contracts;

interface AgentRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25);
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id): bool;
}
```

### Implementation
```php
namespace App\Repositories;

use App\Models\Agent;
use App\Repositories\Contracts\AgentRepositoryInterface;

class AgentRepository implements AgentRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25)
    {
        return Agent::query()
            ->when($filters['name'] ?? '', fn($q, $v) => $q->where('agent_name', 'like', '%'.$v.'%'))
            ->paginate($perPage);
    }
    // ...
}
```

### Bind in AppServiceProvider
```php
// app/Providers/AppServiceProvider.php — register() method
$this->app->bind(
    \App\Repositories\Contracts\AgentRepositoryInterface::class,
    \App\Repositories\AgentRepository::class,
);
```

### Controller mein inject karo
```php
class AgentController extends Controller
{
    public function __construct(private AgentRepositoryInterface $agents) {}

    public function index(Request $request)
    {
        $items = $this->agents->paginate($request->only(['name', 'city']));
        // ...
    }
}
```

### Rules
- Naya module → Repository zaroor banao
- Existing controllers (`AgentController`, `ShipmentController`, etc.) → **mat chhedo**
- Business logic Controller mein nahi, Repository ya Service mein
- Direct `Model::query()` sirf existing files mein acceptable hai

---

## 10. Git Rules

- **Bina permission ke `git push` mat karo** — pehle user se confirm karo
- Local changes karo → user ko dikhao → user bole tab push karo
- Commit message clear aur descriptive rakho

---

## 11. Checklist — Koi bhi Change Karne Se Pehle

```
[ ] Is page ka controller index method dekha?
[ ] partials/rows.blade.php mein <td> count = <thead> <th> count?
[ ] DataTables use ho raha hai? columnDefs targets integer hai (array nahi)?
[ ] SweetAlert callback mein koi DataTables call toh nahi jo table destroy ke baad toot jaye?
[ ] is_active filter ka default false hai?
[ ] PrivateDisk use ho raha hai file delete ke liye?
[ ] Agar koi import/model/helper remove kiya hai, file mein us symbol ke saare usages search kiye? (`Country::`, `new Country`, type-hints, static calls, etc.)
[ ] Affected route/page localhost pe open karke manual smoke test kiya? (sirf `php -l` ya grep enough nahi)
[ ] Affected AJAX action / form submit / filter / delete flow bhi manually test kiya?
[ ] "Verified" tabhi bolo jab affected page actual run karke dekh liya ho
[ ] Local pe test kiya? Phir push kiya?
```

---

## 12. Performance Rules

### Country List — Always use CountryCache
```php
use App\Support\CountryCache;

// Eloquent collection (Agent, Supplier, Vessel, Shipment controllers)
$countries = CountryCache::active();

// Plain DB rows (Hub controller — DB::table based)
$countries = CountryCache::activeRaw();

// Currencies list
$currencies = CountryCache::currencies();

// After any country create/update/delete:
CountryCache::flush();
```
**Never** call `Country::where('is_active', true)->get()` directly — always use CountryCache.



- `LIKE '%term%'` (contains) — B-tree index use nahi hota, small tables pe acceptable
- `LIKE 'term%'` (prefix) — index use hota hai, large tables pe use karo (`ListSearch::prefix`)
- Har list controller mein `->with('relation')` hona chahiye (N+1 avoid)
- New filterable column add karo → migration mein index bhi add karo
- Production deploy ke baad: `php artisan config:cache && route:cache && view:cache`

---

## 13. Production Deploy Notes

- **Git push** → Production pe `git pull` karna hoga manually ya deploy hook se
- **Scheduler:** `* * * * *` cron Hostinger hPanel mein set hai (`php artisan schedule:run`)
- **Currency rates:** `currency:update-rates` command har ghante chalti hai
- **Logs:** `storage/logs/laravel.log` aur `storage/logs/scheduler.log`
