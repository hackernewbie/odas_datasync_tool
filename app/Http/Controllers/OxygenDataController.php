<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\Facility;
use App\Models\ODASToken;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\FacilityBedInfo;
use CreateHealthFacilityAnalysis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\HealthFacilityOxygen;
use App\Services\GoogleSheetService;
use App\Models\HealthFacilityAnalysis;

class OxygenDataController extends Controller
{
    public function Oxygen(){
        $allInOxygenStatus  =   HealthFacilityOxygen::all();
        return view('oxygen_status')
            ->with('allInOxygenStatus',$allInOxygenStatus);
    }

    public function FetchOxygenData(){
        $gsheet             = new GoogleSheetService();

        $allOxygenData      = $gsheet->readGoogleSheet(config('google.data_for_dashboard'),'BM');
        $listOfFacilities   = Facility::select('id','facility_name','odas_facility_id')->get();

        if($allOxygenData == null || count($allOxygenData) <= 1){
            return redirect()->back()->with('error','Source Google Sheet Empty');
        }

        try{
            DB::beginTransaction();

            $occupancyDate      =   $allOxygenData[0][2];

            for($count = 2; $count <= count($allOxygenData)-1; $count++){
                $generatedUUID              = Str::uuid();

                $oxygenDataForHosp          = HealthFacilityOxygen::where('facility_name',$allOxygenData[$count][2])->first();

                $odasFacilityIdToInsert     =   $listOfFacilities->where('facility_name',$allOxygenData[$count][2])->first()
                                                ? $listOfFacilities->where('facility_name',$allOxygenData[$count][2])->first()->odas_facility_id
                                                : null;

                $facilityInfoIdToInsert     =   $listOfFacilities->where('facility_name',$allOxygenData[$count][2])->first()
                                                ? $listOfFacilities->where('facility_name',$allOxygenData[$count][2])->first()->id
                                                : null;

                //dd($facilityInfoIdToInsert);
                /// dump($allOxygenData[$count][2] . " --> " . $facilityInfoIdToInsert);
                /// dump($allOxygenData[$count][2] . " --> " . $odasFacilityIdToInsert);
                if($odasFacilityIdToInsert !== null && $facilityInfoIdToInsert !==null){
                    $facilityNameForDB                          =   isset($allOxygenData[$count][2]) == false ?  'Empty' : $allOxygenData[$count][2];
                    $supplySourceForDB                          =   isset($allOxygenData[$count][3]) == false ?  'Empty' : $allOxygenData[$count][3];
                    $timeOfUpdateForDB                          =   isset($allOxygenData[$count][4]) == false ?  'Empty' : $allOxygenData[$count][4];
                    $noOfPatientsOnO2                           =   isset($allOxygenData[$count][5]) == false ?  'Empty' : $allOxygenData[$count][5];
                    $o2SupportedBedsForDB                       =   isset($allOxygenData[$count][6]) == false ?  'Empty' : $allOxygenData[$count][6];
                    $icuBedsForDB                               =   isset($allOxygenData[$count][7]) == false ?  'Empty' : $allOxygenData[$count][7];
                    $noOfOxygenatedBedsIncludingICUForDB        =   isset($allOxygenData[$count][8]) == false ?  'Empty' : $allOxygenData[$count][8];
                    $psaInLmuForDB                              =   isset($allOxygenData[$count][9]) == false ?  'Empty' : $allOxygenData[$count][9];
                    $isActiveForDB                              =   isset($allOxygenData[$count][10]) == false ?  'Empty' : $allOxygenData[$count][10];
                    $plannedPSACapacityInCumForDB               =   isset($allOxygenData[$count][11]) == false ?  'Empty' : $allOxygenData[$count][11];
                    $psaCapacityInCumForDB                      =   isset($allOxygenData[$count][12]) == false ?  'Empty' : $allOxygenData[$count][12];
                    $psaGenerationCapacityInMT                  =   ConvertCuMToMT($psaCapacityInCumForDB);
                    $psaStorageCapacityInMT                     =   ConvertCuMToMT($psaCapacityInCumForDB);
                    $cryogenicPlantInLtrForDB                   =   isset($allOxygenData[$count][13]) == false ?  'Empty' : $allOxygenData[$count][13];
                    $lmoCurrentStorageCapacityInMT              =   ConvertCuMToMT($cryogenicPlantInLtrForDB);
                    $plannedCryoCapacityInCumForDB              =   isset($allOxygenData[$count][14]) == false ?  'Empty' : $allOxygenData[$count][14];
                    $cryogenicPlantCapacityInCumForDB           =   isset($allOxygenData[$count][15]) == false ?  'Empty' : $allOxygenData[$count][15];
                    $lmoCurrentStockInMT                        =   ConvertCuMToMT($cryogenicPlantCapacityInCumForDB);
                    $lmoStorageCapacityStockInMT                =   ConvertCuMToMT($cryogenicPlantCapacityInCumForDB);

                    $noOfEmptyTypeBCylindersForDB               =   isset($allOxygenData[$count][16]) == false ?  'Empty' : $allOxygenData[$count][16];
                    $noOfTypeBCylindersInTransitForDB           =   isset($allOxygenData[$count][17]) == false ?  'Empty' : $allOxygenData[$count][17];
                    $noOfFilledTypeBCylindersForDB              =   isset($allOxygenData[$count][18]) == false ?  'Empty' : $allOxygenData[$count][18];
                    $totalTypeBCylindersAvailableForDB          =   isset($allOxygenData[$count][19]) == false ?  'Empty' : $allOxygenData[$count][19];
                    $typeBConsumedIn24HoursForDB                =   isset($allOxygenData[$count][20]) == false ?  'Empty' : $allOxygenData[$count][20];

                    $noOfEmptyTypeDCylindersForDB               =   isset($allOxygenData[$count][21]) == false ?  'Empty' : $allOxygenData[$count][21];
                    $noOfTypeDCylindersInTransitForDB           =   isset($allOxygenData[$count][22]) == false ?  'Empty' : $allOxygenData[$count][22];
                    $noOfFilledTypeDCylindersForDB              =   isset($allOxygenData[$count][23]) == false ?  'Empty' : $allOxygenData[$count][23];
                    $totalTypeDCylindersAvailableForDB          =   isset($allOxygenData[$count][24]) == false ?  'Empty' : $allOxygenData[$count][24];
                    $typeDConsumedIn24HoursForDB                =   isset($allOxygenData[$count][25]) == false ?  'Empty' : $allOxygenData[$count][25];

                    $o2TypeDAndTypeBCapacityInCumForDB          =   isset($allOxygenData[$count][26]) == false ?  'Empty' : $allOxygenData[$count][26];
                    $overallO2AvailabilityInCumForDB            =   isset($allOxygenData[$count][27]) == false ?  'Empty' : $allOxygenData[$count][27];
                    $actualO2AvailabilityInCumForDB             =   isset($allOxygenData[$count][28]) == false ?  'Empty' : $allOxygenData[$count][28];
                    $noOfBipapMachinesForDB                     =   isset($allOxygenData[$count][30]) == false ?  'Empty' : $allOxygenData[$count][30];
                    $noOfO2ConcentratorsForDB                   =   isset($allOxygenData[$count][31]) == false ?  'Empty' : $allOxygenData[$count][31];
                    //dump($noOfO2ConcentratorsForDB);
                    $unaccountedTypeBForDb                      =   isset($allOxygenData[$count][32]) == false ?  'Empty' : $allOxygenData[$count][32];
                    $unaccountedTypeDForDb                      =   isset($allOxygenData[$count][33]) == false ?  'Empty' : $allOxygenData[$count][33];

                    $appxDemandWithCurrLoadInHrsForDB           =   isset($allOxygenData[$count][34]) == false ?  'Empty' : $allOxygenData[$count][34];
                    $appxDemandWithCurrNoOfPatientsInCumForDB   =   isset($allOxygenData[$count][35]) == false ?  'Empty' : $allOxygenData[$count][35];
                    $appDemandWithAllBedsFullInHrsForDB         =   isset($allOxygenData[$count][36]) == false ?  'Empty' : $allOxygenData[$count][36];
                    $appDemandWithAllBedsFullInCumForDB         =   isset($allOxygenData[$count][37]) == false ?  'Empty' : $allOxygenData[$count][37];

                    //dd($appDemandWithAllBedsFullInCumForDB);


                    /// Facility Bed Occupancy Info
                    $noOfGenBedsForDB                                                   =   isset($allOxygenData[$count][49]) == false ?  0 : $allOxygenData[$count][49];
                    $noOfHDUBedsForDB                                                   =   isset($allOxygenData[$count][50]) == false ?  0: $allOxygenData[$count][50];
                    $noOfICUBedsForDB                                                   =   isset($allOxygenData[$count][51]) == false ?  0 : $allOxygenData[$count][51];
                    $noOfO2ConcentratorsBedInfoForDB                                    =   $noOfO2ConcentratorsForDB;
                    $noOfVentBedsForDB                                                  =   isset($allOxygenData[$count][52]) == false ?  0 : $allOxygenData[$count][52];
                    $requestIdForDB                                                     =   $generatedUUID;


                    /// Analysis Data
                    $demandForDB                                                        =   isset($allOxygenData[$count][38]) == false ?  'Empty' : $allOxygenData[$count][38];
                    $availableSupplyForDB                                               =   isset($allOxygenData[$count][39]) == false ?  'Empty' : $allOxygenData[$count][39];
                    $remainingDemandForDB                                               =   isset($allOxygenData[$count][40]) == false ?  'Empty' : $allOxygenData[$count][40];
                    $supplyInTransitForDB                                               =   isset($allOxygenData[$count][41]) == false ?  'Empty' : $allOxygenData[$count][41];
                    $remainingDemandAfterFactoringCylindersInTransitForDB               =   isset($allOxygenData[$count][42]) == false ?  'Empty' : $allOxygenData[$count][42];
                    $capacityOfTypeDEmptyCylindersForDB                                 =   isset($allOxygenData[$count][43]) == false ?  'Empty' : $allOxygenData[$count][43];
                    $capacityOfTypeBEmptyCylindersAtFacilityForDB                       =   isset($allOxygenData[$count][44]) == false ?  'Empty' : $allOxygenData[$count][44];
                    $noOfTypeDEmptyCylindersToBeSentForRefillingForDB                   =   isset($allOxygenData[$count][45]) == false ?  'Empty' : $allOxygenData[$count][45];
                    $noOfTypeBEmptyCylindersToBeSentForRefillingForDB                   =   isset($allOxygenData[$count][46]) == false ?  'Empty' : $allOxygenData[$count][46];
                    $noOfTypeBEmptyCylindersToBeReturnedForDB                           =   isset($allOxygenData[$count][47]) == false ?  'Empty' : $allOxygenData[$count][47];
                    $noOfTypeDEmptyCylindersToBeReturnedForDB                           =   isset($allOxygenData[$count][48]) == false ?  'Empty' : $allOxygenData[$count][48];


                    if($oxygenDataForHosp == null && $facilityInfoIdToInsert !==null){         /// Add new into oxygen_data
                        $createdOxygenData              =   HealthFacilityOxygen::create([
                            'facility_information_id'                           =>  $facilityInfoIdToInsert,
                            'odas_facility_id'                                  =>  $odasFacilityIdToInsert,
                            'facility_name'                                     =>  $facilityNameForDB,
                            'supply_source'                                     =>  $supplySourceForDB,
                            'time_of_update'                                    =>  $timeOfUpdateForDB,
                            'no_of_patients_on_o2'                              =>  $noOfPatientsOnO2,
                            'no_of_o2_supported_beds'                           =>  $o2SupportedBedsForDB,
                            'no_of_ICU_beds'                                    =>  $icuBedsForDB,
                            'no_of_oxygenated_beds_including_ICU'               =>  $noOfOxygenatedBedsIncludingICUForDB,
                            'psa_in_lpm'                                        =>  $psaInLmuForDB,
                            'is_active'                                         =>  $isActiveForDB,
                            'planned_psa_capacity_in_cum'                       =>  $plannedPSACapacityInCumForDB,
                            'psa_capacity_in_cum'                               =>  $psaCapacityInCumForDB,
                            'psa_gen_capacity_in_MT'                            =>  $psaGenerationCapacityInMT,
                            'psa_storage_capacity_in_MT'                        =>  $psaStorageCapacityInMT,
                            'cryogenic_plant_in_ltr'                            =>  $cryogenicPlantInLtrForDB,
                            'lmo_current_storage_capacity_in_MT'                =>  $lmoCurrentStorageCapacityInMT,
                            'planned_cryogenic_capacity_in_cum'                 =>  $plannedCryoCapacityInCumForDB,
                            'lmo_current_stock_in_MT'                           =>  $lmoCurrentStockInMT,
                            'psa_storage_capacity_in_MT'                        =>  $lmoStorageCapacityStockInMT,
                            'cryogenic_capacity_in_cum'                         =>  $cryogenicPlantCapacityInCumForDB,
                            'no_of_empty_typeB_cylinders'                       =>  $noOfEmptyTypeBCylindersForDB,
                            'no_typeB_cylinders_in_transit'                     =>  $noOfTypeBCylindersInTransitForDB,
                            'no_filled_typeB_cylinders'                         =>  $noOfFilledTypeBCylindersForDB,
                            'total_typeB_cylinders_available'                   =>  $totalTypeBCylindersAvailableForDB,
                            'no_of_consumed_typeB_cylinders_in_last_24_hours'   =>  $typeBConsumedIn24HoursForDB,

                            'no_of_empty_typeD_cylinders'                       =>  $noOfEmptyTypeDCylindersForDB,
                            'no_typeD_cylinders_in_transit'                     =>  $noOfTypeDCylindersInTransitForDB,
                            'no_filled_typeD_cylinders'                         =>  $noOfFilledTypeDCylindersForDB,
                            'total_typeD_cylinders'                             =>  $totalTypeDCylindersAvailableForDB,
                            'no_of_consumed_typeD_cylinders_in_last_24_hours'   =>  $typeDConsumedIn24HoursForDB,

                            'o2_typeD_and_typeB_capacity_in_cum'                =>  $o2TypeDAndTypeBCapacityInCumForDB,
                            'overall_o2_availability_in_cum'                    =>  $overallO2AvailabilityInCumForDB,
                            'actual_o2_availability_in_cum'                     =>  $actualO2AvailabilityInCumForDB,
                            'no_of_BiPAP_machines'                              =>  $noOfBipapMachinesForDB,
                            'no_of_o2_concentrators'                            =>  $noOfO2ConcentratorsForDB,

                            'unaccounted_typeB'                                 =>  $unaccountedTypeBForDb,
                            'unaccounted_typeD'                                 =>  $unaccountedTypeDForDb,
                            'appx_o2_demand_with_current_load_in_hrs'           =>  $appxDemandWithCurrLoadInHrsForDB,
                            'appx_o2_demand_with_current_no_of_patients_in_cum' =>  $appxDemandWithCurrNoOfPatientsInCumForDB,
                            'appx_o2_demand_with_all_beds_full'                 =>  $appDemandWithAllBedsFullInHrsForDB,

                            'requestId'                                         =>  $generatedUUID,
                        ]);

                        $createdFacilityBedInfo             = FacilityBedInfo::create([
                            'oxygen_data_id'                                =>  $createdOxygenData->id,
                            'odas_facility_id'                              =>  $odasFacilityIdToInsert,
                            'no_gen_beds'                                   =>  $noOfGenBedsForDB ? $noOfGenBedsForDB : 0,
                            'no_hdu_beds'                                   =>  $noOfHDUBedsForDB ? $noOfHDUBedsForDB : 0,
                            'no_icu_beds'                                   =>  $noOfICUBedsForDB ? $noOfICUBedsForDB : 0,
                            'no_o2_concentrators'                           =>  $noOfO2ConcentratorsBedInfoForDB ? $noOfO2ConcentratorsBedInfoForDB : 0,
                            'no_vent_beds'                                  =>  $noOfVentBedsForDB ? $noOfVentBedsForDB : 0,
                            'occupancy_date'                                =>  $occupancyDate,
                            'requestId'                                     =>  $requestIdForDB,
                        ]);


                        //dd($createdOxygenData->id);
                        /// Health Facility Analysis Table
                        $createdHealthFacilityAnalysis      = HealthFacilityAnalysis::create([
                            'oxygen_data_id'                                                =>  $createdOxygenData->id,
                            'demand'                                                        =>  $demandForDB,
                            'available_supply_at_facility'                                  =>  $availableSupplyForDB,
                            'remaining_demand_after_exhausting_filled_cylinders'            =>  $remainingDemandForDB,
                            'supply_in_transit_of_typeB'                                    =>  $supplyInTransitForDB,
                            'remaining_demand_after_factoring_in_transit_cylinders'         =>  $remainingDemandAfterFactoringCylindersInTransitForDB,
                            'capacity_of_empty_cylinders_typeD'                             =>  $capacityOfTypeDEmptyCylindersForDB,
                            'capacity_of_empty_cylinders_typeB'                             =>  $capacityOfTypeBEmptyCylindersAtFacilityForDB,
                            'no_of_typeD_cylinders_to_refill'                               =>  $noOfTypeDEmptyCylindersToBeSentForRefillingForDB,
                            'no_of_typeB_cylinders_to_refill_if_demand_unmet_typeB'         =>  $noOfTypeBEmptyCylindersToBeSentForRefillingForDB,
                            'typeB_empty_cylinders_to_be_returned'                          =>  $noOfTypeBEmptyCylindersToBeReturnedForDB,
                            'typeD_empty_cylinders_to_be_returned'                          =>  $noOfTypeDEmptyCylindersToBeReturnedForDB,
                        ]);
                    }
                    else{
                        /// Update
                    }
                }
                else{
                    /// No ODAS Facility ID in DB, generate ID first.
                }
            }
            //dd("stop");
            DB::commit();
            return redirect()->back()->with('success', 'Oxygen Data Fetched!');
        }
        catch(\Exception $ex){
            DB::rollback();
            return redirect()->back()->withErrors($ex->getMessage())->withInput();
        }
        //Insert into DB if data not already present else update data

    }

    public function UpdateOxygenDataByHospital($hospitalName){
        Log::debug("Attempting to add Oxygen Data for: " . $hospitalName);
        //dd($hospitalName);
        try{
            $odasApiBAseURL                                 =   config('odas.odas_base_url');
            $updateFacilityOxygenInfraEndpointURI           =   'v1.0/odas/update-facility-o2-infra';
            $facilityBeingProcessed                         =   HealthFacilityOxygen::where('facility_name',$hospitalName)->latest()->first();
            //dd($facilityBeingProcessed);

            $newToken                                       =   getODASAccessToken();

            // Save the authToken to the DB
            $odasToken                =   new ODASToken();
            $odasToken->token         =   $newToken;
            $odasToken->timestamp_utc =   Carbon::now()->toJSON();
            $odasToken->save();
            //dd('success');
            Log::debug("API Auth Token Generated!");

            /// Update Facility O2 Infra API
            $odasTokenToUse           =     $odasToken->token;
            $params = array(
                "facilityid" => $facilityBeingProcessed->odas_facility_id,
                "o2Infra" => [
                    "cylinder_a_type_capacity"          => 0,
                    "cylinder_a_type_yn"                => 'N',
                    "cylinder_b_type_capacity"          => $facilityBeingProcessed->total_typeB_cylinders_available,
                    "cylinder_b_type_yn"                => 'Y',
                    "cylinder_c20_type_capacity"        => 0,
                    "cylinder_c20_type_yn"              => 'N',
                    "cylinder_c35_type_capacity"        => 0,
                    "cylinder_c35_type_yn"              =>  'N',
                    "cylinder_c45_type_capacity"        => 0,
                    "cylinder_c45_type_yn"              =>  'N',
                    "cylinder_d6_type_capacity"         => 0,
                    "cylinder_d6_type_yn"               =>  'N',
                    "cylinder_d7_type_capacity"         => $facilityBeingProcessed->total_typeD_cylinders,
                    "cylinder_d7_type_yn"               => 'Y',
                    "lmo_available_yn"                  => 'Y',
                    "lmo_current_stock"                 => $facilityBeingProcessed->lmo_current_stock_in_MT,
                    "lmo_storage_capacity"              => $facilityBeingProcessed->lmo_current_stock_in_MT,
                    "psa_available_yn"                  => $facilityBeingProcessed->is_active == 'TRUE' ? 'Y' : 'N',
                    "psa_gen_capacity"                  => $facilityBeingProcessed->psa_gen_capacity_in_MT,
                    "psa_has_mgp_option_yn"             => 'Y',
                    "psa_has_refil_option_yn"           => 'N',
                    "psa_storage_capacity"              => $facilityBeingProcessed->psa_storage_capacity_in_MT,
                ],
                "requestId" => $facilityBeingProcessed->requestId,
                "timestamp" => $odasToken->timestamp_utc
            );
            //dd($params);
            $client = new Client();
            Log::debug("Attempting to push Oxygen data to API: " . $odasApiBAseURL.$updateFacilityOxygenInfraEndpointURI);
            $response = $client->post($odasApiBAseURL.$updateFacilityOxygenInfraEndpointURI, [
                'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json','Authorization'=>'Bearer ' .$odasTokenToUse,],
                'body'    => json_encode($params)
            ]);
            $dataRes =   json_decode($response->getBody(), true);

            if($dataRes !== null){
                $healthFacilityO2                           =   HealthFacilityOxygen::find($facilityBeingProcessed->id);
                $healthFacilityO2->odas_reference_number    =   $dataRes['referencenumber'] ? $dataRes['referencenumber'] : 'No Reference Number';
                $healthFacilityO2->status                   =   $dataRes['status'] ? $dataRes['status'] : 'No Status Number';
                $healthFacilityO2->save();

                Log::debug('----------------------------------------');
                Log::debug('Facility Oxygen Infrastructure Updated Successfully!' . ' - ' .$dataRes['referencenumber']);
                /// Update Facility Infrastructure Data
                return redirect()->back()->with('success', 'Facility Oxygen Infrastructure Updated Successfully!');
            }
        }
        catch(\Exception $ex){
            Log::error($ex->getMessage());
            return redirect()->back()->with('error', $ex->getMessage());
        }
    }

    public function UpdateFacilityBedOccupancyData($odasFacilityId){
        Log::debug("Attempting to add Bed Occupancy Data for: " . $odasFacilityId);
        //dd($hospitalName);
        try{
            $odasApiBAseURL                                 =   config('odas.odas_base_url');
            $updateBedOccupancyEndpointURI                  =   'v1.0/odas/update-bed-occupancy-info';
            $facilityBeingProcessed                         =   FacilityBedInfo::where('odas_facility_id',$odasFacilityId)->latest()->first();
            //dd($facilityBeingProcessed);

            $newToken                                       =   getODASAccessToken();

            // Save the authToken to the DB
            $odasToken                =   new ODASToken();
            $odasToken->token         =   $newToken;
            $odasToken->timestamp_utc =   Carbon::now()->toJSON();
            $odasToken->save();
            //dd('success');
            Log::debug("API Auth Token Generated!");

            /// Update Facility O2 Infra API
            $odasTokenToUse                     =     $odasToken->token;
            $params = array(
                "beds" => [
                    "no_gen_beds"               => $facilityBeingProcessed->no_gen_beds,
                    "no_hdu_beds"               => $facilityBeingProcessed->no_hdu_beds,
                    "no_icu_beds"               => $facilityBeingProcessed->no_icu_beds,
                    "no_o2_concentrators"       => $facilityBeingProcessed->no_o2_concentrators,
                    "no_vent_beds"              => $facilityBeingProcessed->no_vent_beds,
                ],
                "facilityid"                    => $facilityBeingProcessed->odas_facility_id,
                "occupancyDate"                 => $facilityBeingProcessed->occupancy_date,
                "requestId"                     => $facilityBeingProcessed->requestId,
                "timestamp"                     => $odasToken->timestamp_utc
            );

            //dd($params);
            $client = new Client();
            Log::debug("Attempting to push Bed Occupancy] data to API: " . $odasApiBAseURL.$updateBedOccupancyEndpointURI);
            $response = $client->post($odasApiBAseURL.$updateBedOccupancyEndpointURI, [
                'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json','Authorization'=>'Bearer ' .$odasTokenToUse,],
                'body'    => json_encode($params)
            ]);
            $dataRes =   json_decode($response->getBody(), true);

            if($dataRes !== null){
                $facilityBedInfo                           =   FacilityBedInfo::find($facilityBeingProcessed->id);
                $facilityBedInfo->odas_reference_number    =   $dataRes['referencenumber'] ? $dataRes['referencenumber'] : 'No Reference Number';
                $facilityBedInfo->status                   =   $dataRes['status'] ? $dataRes['status'] : 'No Status Number';
                $facilityBedInfo->save();

                Log::debug('----------------------------------------');
                Log::debug('Bed Occupany Data Updated Successfully!' . ' - ' .$dataRes['referencenumber']);
                /// Update Facility Infrastructure Data
                return redirect()->back()->with('success', 'Bed Occupany Data Updated Successfully!');
            }
        }
        catch(\Exception $ex){
            Log::error($ex->getMessage());
            return redirect()->back()->with('error', $ex->getMessage());
        }

    }
}
