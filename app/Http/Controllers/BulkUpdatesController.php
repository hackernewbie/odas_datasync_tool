<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BulkUpdatesController extends Controller
{
    public function BulkUpdateFacilityO2Infra(){
        Log::debug('Intiating BulkUpdateFacilityO2Infra');
        $allFacilityNames   =   Facility::all();

        foreach($allFacilityNames as $facility){
            Log::debug('Running Bulk Data Push for: ' . $facility->facility_name . ' - ' . $facility->odas_facility_id);
            app('App\Http\Controllers\OxygenDataController')->UpdateOxygenDataByHospital($facility->facility_name);

            app('App\Http\Controllers\OxygenDataController')->UpdateFacilityBedOccupancyData($facility->odas_facility_id);

            app('App\Http\Controllers\OxygenDataController')->UpdateFacilityO2ConsumptionData($facility->odas_facility_id);
        }

        Log::debug('Completed BulkUpdateFacilityO2Infra');
    }
}
