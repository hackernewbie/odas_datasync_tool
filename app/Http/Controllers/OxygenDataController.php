<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use CreateHealthFacilityAnalysis;
use Illuminate\Support\Facades\DB;
use App\Models\HealthFacilityOxygen;
use App\Services\GoogleSheetService;
use App\Models\HealthFacilityAnalysis;

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

            for($count = 2; $count <= count($allOxygenData)-1; $count++){
                $generatedUUID              = Str::uuid();

                $oxygenDataForHosp          = HealthFacilityOxygen::where('facility_name',$allOxygenData[$count][2])->first();

                $odasFacilityIdToInsert     =   $listOfFacilities->where('facility_name',$allOxygenData[$count][2])->first()
                                                ? $listOfFacilities->where('facility_name',$allOxygenData[$count][2])->first()->odas_facility_id
                                                : null;


                /// dump($allOxygenData[$count][2] . " --> " . $odasFacilityIdToInsert);
                if($odasFacilityIdToInsert !== null){
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
                    $cryogenicPlantInLtrForDB                   =   isset($allOxygenData[$count][14]) == false ?  'Empty' : $allOxygenData[$count][14];
                    $plannedCryoCapacityInCumForDB              =   isset($allOxygenData[$count][15]) == false ?  'Empty' : $allOxygenData[$count][15];
                    $cryogenicPlantCapacityInCumForDB           =   isset($allOxygenData[$count][16]) == false ?  'Empty' : $allOxygenData[$count][16];

                    $noOfEmptyTypeBCylindersForDB               =   isset($allOxygenData[$count][17]) == false ?  'Empty' : $allOxygenData[$count][17];
                    $noOfTypeBCylindersInTransitForDB           =   isset($allOxygenData[$count][18]) == false ?  'Empty' : $allOxygenData[$count][18];
                    $noOfFilledTypeBCylindersForDB              =   isset($allOxygenData[$count][19]) == false ?  'Empty' : $allOxygenData[$count][19];
                    $totalTypeBCylindersAvailableForDB          =   isset($allOxygenData[$count][20]) == false ?  'Empty' : $allOxygenData[$count][20];
                    $typeBConsumedIn24HoursForDB                =   isset($allOxygenData[$count][21]) == false ?  'Empty' : $allOxygenData[$count][21];

                    $noOfEmptyTypeDCylindersForDB               =   isset($allOxygenData[$count][22]) == false ?  'Empty' : $allOxygenData[$count][22];
                    $noOfTypeDCylindersInTransitForDB           =   isset($allOxygenData[$count][23]) == false ?  'Empty' : $allOxygenData[$count][23];
                    $noOfFilledTypeDCylindersForDB              =   isset($allOxygenData[$count][24]) == false ?  'Empty' : $allOxygenData[$count][24];
                    $totalTypeDCylindersAvailableForDB          =   isset($allOxygenData[$count][25]) == false ?  'Empty' : $allOxygenData[$count][25];
                    $typeDConsumedIn24HoursForDB                =   isset($allOxygenData[$count][26]) == false ?  'Empty' : $allOxygenData[$count][26];

                    $o2TypeDAndTypeBCapacityInCumForDB          =   isset($allOxygenData[$count][27]) == false ?  'Empty' : $allOxygenData[$count][27];
                    $overallO2AvailabilityInCumForDB            =   isset($allOxygenData[$count][28]) == false ?  'Empty' : $allOxygenData[$count][28];
                    $actualO2AvailabilityInCumForDB             =   isset($allOxygenData[$count][29]) == false ?  'Empty' : $allOxygenData[$count][29];
                    $noOfBipapMachinesForDB                     =   isset($allOxygenData[$count][30]) == false ?  'Empty' : $allOxygenData[$count][30];
                    $noOfO2ConcentratorsForDB                   =   isset($allOxygenData[$count][31]) == false ?  'Empty' : $allOxygenData[$count][31];

                    $unaccountedTypeBForDb                      =   isset($allOxygenData[$count][32]) == false ?  'Empty' : $allOxygenData[$count][32];
                    $unaccountedTypeDForDb                      =   isset($allOxygenData[$count][33]) == false ?  'Empty' : $allOxygenData[$count][33];

                    $appxDemandWithCurrLoadInHrsForDB           =   isset($allOxygenData[$count][34]) == false ?  'Empty' : $allOxygenData[$count][34];
                    $appxDemandWithCurrNoOfPatientsInCumForDB   =   isset($allOxygenData[$count][35]) == false ?  'Empty' : $allOxygenData[$count][35];
                    $appDemandWithAllBedsFullForDB              =   isset($allOxygenData[$count][36]) == false ?  'Empty' : $allOxygenData[$count][36];


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

                    if($oxygenDataForHosp == null){         /// Add new into oxygen_data

                        $createdOxygenData              =   HealthFacilityOxygen::create([
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
                            'cryogenic_plant_in_ltr'                            =>  $cryogenicPlantInLtrForDB,
                            'planned_cryogenic_capacity_in_cum'                 =>  $plannedCryoCapacityInCumForDB,
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
                            'appx_o2_demand_with_all_beds_full'                 =>  $appDemandWithAllBedsFullForDB,

                            'requestId'                                         =>  $generatedUUID,
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

    public function GetOxygenDataByHospital($hospital){
        dd($hospital);
    }
}
