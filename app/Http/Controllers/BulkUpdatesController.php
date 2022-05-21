<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;
use App\Models\ProcessesRun;
use Illuminate\Support\Facades\Log;
use PDO;

class BulkUpdatesController extends Controller
{
    public function BulkUpdateFacilityO2Infra(){
        Log::debug('Intiating BulkUpdateFacilityO2Infra');

        try{
            $allFacilityNames   =   Facility::all();
            //dd($allFacilityNames);
            app('App\Http\Controllers\FacilityController')->GetFacilities();
            /// Update the Log table
            ProcessesRun::create(['description' => 'Facility Information Updated.','status' => 'Success']);

            foreach($allFacilityNames as $facility){
                Log::debug('Running Facility Information Fetch To Local DB!');
                Log::debug('+++++++++++++++++++++++++++++++++++++++++++++++++');

                Log::debug('Running Bulk Data Push for: ' . $facility->facility_name . ' - ' . $facility->odas_facility_id);
                if($facility->odas_facility_id != null){
                    app('App\Http\Controllers\OxygenDataController')->FetchOxygenData();
                    /// Update the Log table
                    ProcessesRun::create(['description' => 'Oxygen Data Fetched from Master Sheet.','status' => 'Success']);

                    app('App\Http\Controllers\OxygenDataController')->UpdateOxygenDataByHospital($facility->facility_name);
                    /// Update the Log table
                    ProcessesRun::create(['description' => 'Oxygen Data Pushed To ODAS.','status' => 'Success']);

                    app('App\Http\Controllers\OxygenDataController')->UpdateFacilityBedOccupancyData($facility->odas_facility_id);
                    /// Update the Log table
                    ProcessesRun::create(['description' => 'Facility Bed Occupancy pushed to ODAS.','status' => 'Success']);

                    app('App\Http\Controllers\OxygenDataController')->UpdateFacilityO2ConsumptionData($facility->odas_facility_id);
                    /// Update the Log table
                    ProcessesRun::create(['description' => 'Oxygen Consumption Data pushed to ODAS.','status' => 'Success']);

                    app('App\Http\Controllers\OxygenDataController')->UpdateOxygenDemand($facility->odas_facility_id);
                    /// Update the Log table
                    ProcessesRun::create(['description' => 'Oxygen Demand Data pushed to ODAS.','status' => 'Success']);
                }
                else{
                    //Log::debug('Skipping: ' . $facility->facility_name . '. No facility Id assigned!');
                    Log::debug('No facility Id assigned for ' . $facility->facility_name.'. Generating ODAS FacilityID.');

                    app('App\Http\Controllers\FacilityController')->GenerateFacilityId($facility->facility_name);
                    /// Update the Log table
                    ProcessesRun::create(['description' => 'Facility Id Generation Process Run.','status' => 'Success']);
                    Log::debug('+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_');
                }
            }
        }
        catch(\Exception $ex){
            Log::debug('Exception in BulkUpdateFacilityO2Infra. ' . $ex->getMessage());
            return redirect()->back()->with('error',$ex->getMessage());
        }


        //Log::debug('Completed BulkUpdateFacilityO2Infra');
    }
}
