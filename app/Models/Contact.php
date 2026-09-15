<?php

namespace App\Models;

use App\Traits\LogsFieldChanges;
use App\Traits\TracksUserAudit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes, TracksUserAudit, LogsFieldChanges;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'description',
        'is_main_contact',
        'customer_id',
        'hub_id',
        'supplier_id',
        'other_company_id',
        'office_id',
        'agent_id',
        'reply_to_email',
        'is_cc_enabled',
        'status',
        'category',
        'created_by',
        'updated_by',
    ];

    /**
     * Activated office/portal contacts only (status truthy). Soft-deleted rows are already excluded.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function (Builder $statusQuery) {
            $statusQuery->where('status', 1)
                ->orWhere('status', true)
                ->orWhere('status', '1');
        });
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function hub()
    {
        return $this->belongsTo(Hub::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function otherCompany()
    {
        return $this->belongsTo(OtherCompany::class, 'other_company_id');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
