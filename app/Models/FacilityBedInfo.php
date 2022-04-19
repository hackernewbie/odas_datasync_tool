<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityBedInfo extends Model
{
    use HasFactory;

    protected $table        =   'facility_bed_info';

    protected $fillable     =   [
                                    'oxygen_data_id','odas_facility_id','no_gen_beds','no_hdu_beds','no_icu_beds','no_o2_concentrators',
                                    'no_vent_beds','occupancy_date','requestId'
                                ];

    public function HealthFacilityOxygen(){
        return $this->belongsTo('App\Models\HealthFacility\Oxygen','oxygen_data_id');
    }
}
