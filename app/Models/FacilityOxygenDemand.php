<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityOxygenDemand extends Model
{
    use HasFactory;

    protected $table  =       'facility_oxygen_demand';

    protected $fillable  =       [
        'oxygen_data_id',
        'accuracy_remarks',
        'demand_accuracy_flag',
        'demand_for_date',
        'demand_raised_date',
        'over_estimated_by',
        'total_estimated_demand',
        'under_estimated_by',
        'odas_facility_id',
        'requestId',
        'status',
        'odas_reference_number',
    ];

    public function HealthFacilityOxygen(){
        return $this->belongsTo('App\Models\HealthFacilityOxygen');
    }
}
