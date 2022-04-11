<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\HealthFacilityOxygen;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\GoogleSheetService;

class OxygenDataController extends Controller
{
    public function Oxygen(){
        $allInOxygenStatus  =   [];
        return view('oxygen_status')
            ->with('allInOxygenStatus',$allInOxygenStatus);
    }

    public function FetchOxygenData(){
        $gsheet             = new GoogleSheetService();

        $allOxygenData      = $gsheet->readGoogleSheet(config('google.data_for_dashboard'),'BI');
        $listOfFacilities   = Facility::select('facility_name','odas_facility_id')->get();

        //dd($listOfFacilities);
        //dd($listOfFacilities->where('facility_name','Test Hospital')->first());
        //dd($allOxygenData);

        if($allOxygenData == null || count($allOxygenData) <= 1){
            return redirect()->back()->with('error','Source Google Sheet Empty');
        }

        try{
            DB::beginTransaction();

            for($count = 1; $count <= count($allOxygenData)-1; $count++){
                $generatedUUID              = Str::uuid();

                $oxygenDataForHosp          = HealthFacilityOxygen::where('odas_facility_id',$allOxygenData[$count][2])->first();

                $odasFacilityIdToInsert     =   $listOfFacilities->where('facility_name',$allOxygenData[$count][2])->first()
                                                ? $listOfFacilities->where('facility_name',$allOxygenData[$count][2])->first()->odas_facility_id
                                                : null;

                if($odasFacilityIdToInsert !== null){
                    /// Add new into oxygen_data
                    dump($allOxygenData[$count][2] . " --> " . $odasFacilityIdToInsert);
                    if($oxygenDataForHosp == null){

                    }
                    else{
                        /// Update
                    }
                }
            }
            dd("stop");
            DB::commit();
            return redirect()->back()->with('success', 'Oxygen Data Fetched!');
        }
        catch(\Exception $ex){
            DB::rollback();
            return redirect()->back()->withErrors($ex->getMessage())->withInput();
        }
        //Insert into DB if data not already present else update data

    }

    public function GetOxygenDataByHospital($hospital){
        dd($hospital);
    }
}
