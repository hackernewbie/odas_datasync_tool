<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthFacilityAnalysis extends Model
{
    use HasFactory;

    protected $table        =   'health_facility_analysis';

    protected $fillable     =   ['oxygen_data_id',
                                'demand',
                                'available_supply_at_facility',
                                'remaining_demand_after_exhausting_filled_cylinders',
                                'supply_in_transit_of_typeB',
                                'remaining_demand_after_factoring_in_transit_cylinders',
                                'capacity_of_empty_cylinders_typeD',
                                'capacity_of_empty_cylinders_typeB',
                                'no_of_typeD_cylinders_to_refill',
                                'no_of_typeB_cylinders_to_refill_if_demand_unmet_typeB',
                                'typeB_empty_cylinders_to_be_returned',
                                'typeD_empty_cylinders_to_be_returned',
                                ];
}
