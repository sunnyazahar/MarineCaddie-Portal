<?php

namespace App\Models;

use App\Traits\LogsFieldChanges;
use App\Traits\TracksUserAudit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes, TracksUserAudit, LogsFieldChanges;

    protected $fillable = [
        'supplier_name', 'phone_number', 'contact_person', 'email', 'remarks', 'special_considerations',
        'supplier_address', 'city', 'district_state', 'zip_code', 'country_id', 'port_code',
        'office_address', 'office_city', 'office_district_state', 'office_zip_code', 'office_country_id',
        'vat_number', 'eori_number', 'currency', 'un_locode',
        'created_by', 'updated_by',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function officeCountry()
    {
        return $this->belongsTo(Country::class, 'office_country_id');
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
