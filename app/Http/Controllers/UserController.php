<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Hub;
use App\Models\Office;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureAdmin();

        $search = trim((string) $request->query('search', ''));
        $role = trim((string) $request->query('role', ''));
        $status = $request->query('status');
        $perPage = max(10, min(100, (int) $request->query('per_page', 25)));

        $users = User::query()
            ->with(['offices:id,office_name', 'hubs:id,hub_name,code', 'agents:id,agent_name,code', 'suppliers:id,supplier_name'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone_number', 'like', '%' . $search . '%');
                });
            })
            ->when($role !== '', fn ($query) => $query->where('role', $role))
            ->when($status !== null && $status !== '', fn ($query) => $query->where('is_active', (bool) $status))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        return view('Users.users', [
            'users' => $users,
            'assignmentOffices' => Office::query()->orderBy('office_name')->get(['id', 'office_name']),
            'assignmentHubs' => Hub::query()->orderBy('hub_name')->get(['id', 'hub_name', 'code']),
            'assignmentAgents' => Agent::query()->orderBy('agent_name')->get(['id', 'agent_name', 'code']),
            'assignmentSuppliers' => Supplier::query()->orderBy('supplier_name')->get(['id', 'supplier_name']),
            'userRoles' => ['Admin', 'Operations', 'Agents', 'Accounts', 'Supplier'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'in:Admin,Operations,Agents,Accounts,Supplier'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        User::create($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->ensureAdmin();

        $validated = $request->validateWithBag('editUser', [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'in:Admin,Operations,Agents,Accounts,Supplier'],
            'is_active' => ['required', 'boolean'],
            'office_ids' => ['nullable', 'array'],
            'office_ids.*' => ['integer', 'exists:offices,id'],
            'hub_ids' => ['nullable', 'array'],
            'hub_ids.*' => ['integer', 'exists:hubs,id'],
            'agent_ids' => ['nullable', 'array'],
            'agent_ids.*' => ['integer', 'exists:agents,id'],
            'supplier_ids' => ['nullable', 'array'],
            'supplier_ids.*' => ['integer', 'exists:suppliers,id'],
        ]);

        $assignments = [
            'office_ids' => $validated['office_ids'] ?? [],
            'hub_ids' => $validated['hub_ids'] ?? [],
            'agent_ids' => $validated['agent_ids'] ?? [],
            'supplier_ids' => $validated['supplier_ids'] ?? [],
        ];
        unset($validated['office_ids'], $validated['hub_ids'], $validated['agent_ids'], $validated['supplier_ids']);

        DB::transaction(function () use ($user, $validated, $assignments) {
            $user->update($validated);
            $user->offices()->sync(in_array($user->role, ['Operations', 'Accounts'], true) ? $assignments['office_ids'] : []);
            $user->hubs()->sync(in_array($user->role, ['Operations', 'Accounts'], true) ? $assignments['hub_ids'] : []);
            $user->agents()->sync($user->role === 'Agents' ? $assignments['agent_ids'] : []);
            $user->suppliers()->sync($user->role === 'Supplier' ? $assignments['supplier_ids'] : []);
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function unblock(User $user): RedirectResponse
    {
        $this->ensureAdmin();

        $user->forceFill([
            'otp_failed_attempts' => 0,
            'otp_blocked_until' => null,
            'login_otp_hash' => null,
            'login_otp_expires_at' => null,
            'login_otp_sent_at' => null,
        ])->save();

        return redirect()
            ->route('users.index')
            ->with('success', 'User OTP lock cleared successfully.');
    }

    private function ensureAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }
}
