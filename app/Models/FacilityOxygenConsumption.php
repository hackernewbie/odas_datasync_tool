<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityOxygenConsumption extends Model
{
    use HasFactory;

    protected $table  =       'facility_oxygen_consumptions';

    protected $fillable  =       [
        'facility_information_id',
        'consumption_for_date',
        'consumption_updated_date',
        'total_oxygen_consumed',
        'total_oxygen_generated',
        'odas_facility_id',
        'requestId',
        'status',
        'odas_reference_number',
    ];

    public function HealthFacilityOxygen(){
        return $this->belongsTo('App\Models\HealthFacilityOxygen');
    }
}
