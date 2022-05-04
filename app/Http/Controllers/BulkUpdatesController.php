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
        //dd($allFacilityNames);
        //app('App\Http\Controllers\FacilityController')->GetFacilities();
        foreach($allFacilityNames as $facility){
            Log::debug('Running Facility Information Fetch To Local DB!');
            Log::debug('+++++++++++++++++++++++++++++++++++++++++++++++++');

            Log::debug('Running Bulk Data Push for: ' . $facility->facility_name . ' - ' . $facility->odas_facility_id);
            if($facility->odas_facility_id != null){
                app('App\Http\Controllers\OxygenDataController')->FetchOxygenData();
                app('App\Http\Controllers\OxygenDataController')->UpdateOxygenDataByHospital($facility->facility_name);

                app('App\Http\Controllers\OxygenDataController')->UpdateFacilityBedOccupancyData($facility->odas_facility_id);

                app('App\Http\Controllers\OxygenDataController')->UpdateFacilityO2ConsumptionData($facility->odas_facility_id);
            }
            else{
                //Log::debug('Skipping: ' . $facility->facility_name . '. No facility Id assigned!');
                Log::debug('No facility Id assigned for ' . $facility->facility_name.'. Generating ODAS FacilityID.');

                app('App\Http\Controllers\FacilityController')->GenerateFacilityId($facility->facility_name);

                Log::debug('+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_');
            }
        }

        //Log::debug('Completed BulkUpdateFacilityO2Infra');
    }
}
