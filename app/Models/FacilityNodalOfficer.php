<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityNodalOfficer extends Model
{
    use HasFactory;

    protected $table        =   'facility_nodal_officer_details';

    protected $fillable     =   [
                        'facility_information_id','officer_name','officer_designation','officer_salutation','officer_country_code',
                        'officer_mobile_number', 'officer_email','requestId',
    ];

    public function Facility(){
        return $this->belongsTo('App\Models\Facility','facility_information_id');
    }
}
