<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $table = 'facility_information';
    protected $fillable = [
                            'facility_name', 'odas_facility_id', 'address_line_1', 'address_line_2', 'city_lgd_code', 'district_lgd_code', 'pincode', 'state_lgd_code', 'subdistrict_lgd_code',
                            'ownership_type','ownership_subtype', 'facility_type', 'facility_type_code','longitude', 'latitude'
                            ];


    public function FacilityNodalOfficer(){
        return $this->hasOne('App\Models\FacilityNodalOfficer');
    }

    public function FacilityInfrastructure(){
        return $this->hasOne('App\Models\FacilityInfrastructure');
    }

}
