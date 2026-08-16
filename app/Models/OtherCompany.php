<?php

namespace App\Models;

use App\Traits\LogsFieldChanges;
use App\Traits\TracksUserAudit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtherCompany extends Model
{
    use SoftDeletes, TracksUserAudit, LogsFieldChanges;

    protected $fillable = [
        'company_name', 'company_type', 'code', 'code_description', 'phone_number', 'contact_person', 'email', 'remarks', 'special_considerations',
        'street_address', 'city', 'district_state', 'zip_code', 'country_id', 'port_code',
        'office_street_address', 'office_city', 'office_district_state', 'office_zip_code', 'office_country_id',
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
        return $this->hasMany(Contact::class, 'other_company_id');
    }
}
