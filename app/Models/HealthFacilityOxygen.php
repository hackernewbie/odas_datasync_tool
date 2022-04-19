<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthFacilityOxygen extends Model
{
    use HasFactory;

    protected $table    =   'oxygen_data';

    protected $fillable =   [
                                'facility_information_id',
                                'odas_facility_id',
                                'facility_name',
                                'supply_source',
                                'time_of_update',
                                'no_of_patients_on_o2',
                                'no_of_o2_supported_beds',
                                'no_of_ICU_beds',
                                'no_of_oxygenated_beds_including_ICU',
                                'psa_in_lpm',
                                'is_active',
                                'planned_psa_capacity_in_cum',
                                'psa_capacity_in_cum',
                                'cryogenic_plant_in_ltr',
                                'planned_cryogenic_capacity_in_cum',
                                'cryogenic_capacity_in_cum',
                                'no_of_empty_typeB_cylinders',
                                'no_typeB_cylinders_in_transit',
                                'no_filled_typeB_cylinders','total_typeB_cylinders_available',
                                'no_of_consumed_typeB_cylinders_in_last_24_hours',
                                'no_of_empty_typeD_cylinders',
                                'no_typeD_cylinders_in_transit',
                                'no_filled_typeD_cylinders',
                                'total_typeD_cylinders',
                                'no_of_consumed_typeD_cylinders_in_last_24_hours',
                                'o2_typeD_and_typeB_capacity_in_cum',
                                'overall_o2_availability_in_cum',
                                'actual_o2_availability_in_cum',
                                'no_of_BiPAP_machines',
                                'no_of_o2_concentrators',
                                'unaccounted_typeB',
                                'unaccounted_typeD',
                                'appx_o2_demand_with_current_load_in_hrs',
                                'appx_o2_demand_with_current_no_of_patients_in_cum',
                                'appx_o2_demand_with_all_beds_full',
                                'requestId'
                            ];

    public function HealthFacilityAnalysis(){
        return $this->hasOne('App\Models\HealthFacilityAnalysis');
    }

    public function Facility(){
        return $this->belongsTo('App\Models\Facility','facility_information_id');
    }
    public function FacilityBedInfo(){
        return $this->hasOne('App\Models\FacilityBedInfo');
    }
}
