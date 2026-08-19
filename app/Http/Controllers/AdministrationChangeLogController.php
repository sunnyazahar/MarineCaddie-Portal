<?php

namespace App\Http\Controllers;

use App\Models\AdministrationChangeLog;
use App\Models\Agent;
use App\Models\AgentUser;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\CustomerVessel;
use App\Models\Hub;
use App\Models\HubUser;
use App\Models\Office;
use App\Models\OtherCompany;
use App\Models\Supplier;
use App\Repositories\Contracts\AdministrationChangeLogRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;

class AdministrationChangeLogController extends Controller
{
    private const ENTITY_TYPES = [
        Office::class => 'Office',
        Hub::class => 'Hub',
        Agent::class => 'Agent',
        OtherCompany::class => 'Other company',
        Supplier::class => 'Supplier',
        Customer::class => 'Customer',
        CustomerVessel::class => 'Vessel',
        Contact::class => 'Contact / user',
        HubUser::class => 'Hub user',
        AgentUser::class => 'Agent user',
    ];

    private const OFFICE_USER_CATEGORIES = [
        'operations' => 'Operations user',
        'account' => 'Account user',
        'sales' => 'Sales user',
        'manager' => 'Manager user',
    ];

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private AdministrationChangeLogRepositoryInterface $changeLogRepository,
    ) {
    }

    public function index()
    {
        $users = $this->userRepository->usersForChangeLog();
        $entityTypes = self::ENTITY_TYPES;

        return view('administration.change-logs', compact('users', 'entityTypes'));
    }

    public function search(Request $request)
    {
        $filters = $request->all();
        if (! ($request->filled('entity_type') && array_key_exists($request->entity_type, self::ENTITY_TYPES))) {
            unset($filters['entity_type']);
        }

        $logs = $this->changeLogRepository->search($filters, 50);

        $rows = $logs->getCollection()->map(function (AdministrationChangeLog $log) {
            return [
                'date' => optional($log->created_at)->format('d.m.Y H:i'),
                'entity_label' => $this->resolveEntityLabel($log),
                'record_name' => $this->resolveRecordName($log),
                'record_url' => $this->resolveRecordUrl($log),
                'title' => $this->resolveTitle($log),
                'description' => $log->description,
                'user_name' => $log->user?->name ?? 'System',
            ];
        })->values();

        return response()->json([
            'data' => $rows,
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
                'from' => $logs->firstItem(),
                'to' => $logs->lastItem(),
            ],
        ]);
    }

    private function resolveEntityLabel(AdministrationChangeLog $log): string
    {
        $model = $log->loggable;

        if (! $model) {
            return self::ENTITY_TYPES[$log->loggable_type] ?? class_basename((string) $log->loggable_type);
        }

        $context = $this->resolveContext($model);

        if ($context !== null) {
            return $context['role'];
        }

        return self::ENTITY_TYPES[$log->loggable_type] ?? class_basename($model);
    }

    private function resolveTitle(AdministrationChangeLog $log): string
    {
        $title = (string) $log->title;
        $model = $log->loggable;

        if (! $model) {
            return $title;
        }

        $context = $this->resolveContext($model);

        if ($context === null) {
            return $title;
        }

        $role = $context['role'];
        $parent = $context['parent'];

        if (str_ends_with($title, ' edited')) {
            $field = substr($title, 0, -strlen(' edited'));

            return $role . ' · ' . $field . ' edited';
        }

        if (preg_match('/^(Contact|Customer Vessel|Hub User|Agent User) created$/i', $title)) {
            return $parent
                ? $role . ' created · ' . $parent
                : $role . ' created';
        }

        return $role . ' · ' . $title;
    }

    /**
     * @return array{role: string, parent: ?string}|null
     */
    private function resolveContext(mixed $model): ?array
    {
        if ($model instanceof Contact) {
            if ($model->office_id) {
                return [
                    'role' => self::OFFICE_USER_CATEGORIES[$model->category] ?? 'Office user',
                    'parent' => $model->office?->office_name ?: ('Office #' . $model->office_id),
                ];
            }

            if ($model->customer_id) {
                return [
                    'role' => 'Customer contact',
                    'parent' => $model->customer?->customer_name ?: ('Customer #' . $model->customer_id),
                ];
            }

            if ($model->hub_id) {
                return [
                    'role' => 'Hub contact',
                    'parent' => $model->hub?->hub_name ?: ('Hub #' . $model->hub_id),
                ];
            }

            if ($model->agent_id) {
                return [
                    'role' => 'Agent contact',
                    'parent' => $model->agent?->agent_name ?: ('Agent #' . $model->agent_id),
                ];
            }

            if ($model->supplier_id) {
                return [
                    'role' => 'Supplier contact',
                    'parent' => $model->supplier?->supplier_name ?: ('Supplier #' . $model->supplier_id),
                ];
            }

            if ($model->other_company_id) {
                return [
                    'role' => 'Other company contact',
                    'parent' => $model->otherCompany?->company_name ?: ('Company #' . $model->other_company_id),
                ];
            }

            return [
                'role' => 'Contact',
                'parent' => null,
            ];
        }

        if ($model instanceof HubUser) {
            return [
                'role' => 'Hub user',
                'parent' => $model->hub?->hub_name ?: ($model->hub_id ? 'Hub #' . $model->hub_id : null),
            ];
        }

        if ($model instanceof AgentUser) {
            return [
                'role' => 'Agent user',
                'parent' => $model->agent?->agent_name ?: ($model->agent_id ? 'Agent #' . $model->agent_id : null),
            ];
        }

        if ($model instanceof CustomerVessel) {
            return [
                'role' => 'Vessel',
                'parent' => $model->customer?->customer_name ?: ($model->customer_id ? 'Customer #' . $model->customer_id : null),
            ];
        }

        return null;
    }

    private function resolveRecordName(AdministrationChangeLog $log): string
    {
        $model = $log->loggable;

        if (! $model) {
            return '#' . $log->loggable_id . ' (deleted)';
        }

        $context = $this->resolveContext($model);

        if ($context !== null) {
            $name = match (true) {
                $model instanceof CustomerVessel => (string) ($model->vessel ?: 'Vessel #' . $model->id),
                default => (string) ($model->name ?: 'User #' . $model->id),
            };

            $parts = [$name];

            if ($context['parent']) {
                $parts[] = $context['parent'];
            }

            $label = implode(' · ', $parts);

            // Keep role visible on child records (except plain top-level vessel label already in entity).
            if (! in_array($context['role'], ['Vessel'], true)) {
                $label .= ' (' . $context['role'] . ')';
            }

            return $label;
        }

        return match (true) {
            $model instanceof Office => (string) ($model->office_name ?: 'Office #' . $model->id),
            $model instanceof Hub => (string) ($model->hub_name ?: 'Hub #' . $model->id),
            $model instanceof Agent => (string) ($model->agent_name ?: 'Agent #' . $model->id),
            $model instanceof OtherCompany => (string) ($model->company_name ?: 'Company #' . $model->id),
            $model instanceof Supplier => (string) ($model->supplier_name ?: 'Supplier #' . $model->id),
            $model instanceof Customer => (string) ($model->customer_name ?: 'Customer #' . $model->id),
            default => class_basename($model) . ' #' . $model->getKey(),
        };
    }

    private function resolveRecordUrl(AdministrationChangeLog $log): ?string
    {
        $model = $log->loggable;

        if (! $model) {
            return null;
        }

        try {
            return match (true) {
                $model instanceof Office => route('offices.edit', $model->id),
                $model instanceof Hub => route('hub.show', $model->id),
                $model instanceof Agent => route('agents.edit', $model->id),
                $model instanceof OtherCompany => route('other-companies.edit', $model->id),
                $model instanceof Supplier => route('suppliers.edit', $model->id),
                $model instanceof Customer => route('customers.edit', $model->id),
                $model instanceof CustomerVessel => route('customers.vessels.edit', $model->id),
                $model instanceof Contact && $model->office_id && $model->category === 'operations'
                    => route('offices.operations_users.edit', [$model->office_id, $model->id]),
                $model instanceof Contact && $model->office_id && $model->category === 'account'
                    => route('offices.account_users.edit', [$model->office_id, $model->id]),
                $model instanceof Contact && $model->office_id && $model->category === 'sales'
                    => route('offices.sales_users.edit', [$model->office_id, $model->id]),
                $model instanceof Contact && $model->office_id && $model->category === 'manager'
                    => route('offices.manager_users.edit', [$model->office_id, $model->id]),
                $model instanceof Contact && $model->office_id
                    => route('offices.edit', $model->office_id),
                $model instanceof Contact && $model->customer_id => route('contacts.edit', $model->id),
                $model instanceof Contact && $model->hub_id => route('hub.contacts.edit', [$model->hub_id, $model->id]),
                $model instanceof Contact && $model->agent_id => route('agents.contacts.edit', $model->id),
                $model instanceof Contact && $model->supplier_id => route('suppliers.contacts.edit', [$model->supplier_id, $model->id]),
                $model instanceof Contact && $model->other_company_id => route('other-companies.contacts.edit', [$model->other_company_id, $model->id]),
                $model instanceof HubUser => route('hub.users.edit', [$model->hub_id, $model->id]),
                $model instanceof AgentUser => route('agents.users.edit', $model->id),
                default => null,
            };
        } catch (\Throwable) {
            return null;
        }
    }
}
