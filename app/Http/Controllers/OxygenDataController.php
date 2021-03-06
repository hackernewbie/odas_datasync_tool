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
use App\Models\oxygenConsumptionInfo;
use App\Models\HealthFacilityAnalysis;
use App\Models\FacilityOxygenConsumption;
use App\Models\FacilityOxygenDemand;
use PhpParser\Parser\Multiple;

class OxygenDataController extends Controller
{
    public function Oxygen(){
        $allInOxygenStatus  =   HealthFacilityOxygen::all();
        //dd($allInOxygenStatus[2]->FacilityBedInfo);
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
        //dd($allOxygenData);
        try{
            DB::beginTransaction();

            $occupancyDate          =   $allOxygenData[0][2];
            $yesterdayDate          =   Carbon::parse($occupancyDate)->addDays(-1)->format("Y-m-d");

            $demandForDate          =   $allOxygenData[0][2];
            $demandRaisedDate       =   Carbon::parse($occupancyDate)->addDays(1)->format("Y-m-d");

            $typeBCylinderCapacity  =   1.5;            ///CuM
            $typeDCylinderCapacity  =   7;              ///CuM

            for($count = 2; $count <= count($allOxygenData)-1; $count++){
                Log::debug('Fetching data for - ' .$allOxygenData[$count][2]);

                $generatedUUID              = Str::uuid();

                $oxygenDataForHosp          = HealthFacilityOxygen::where('facility_name',$allOxygenData[$count][2])->first();

                $odasFacilityIdToInsert     =   $listOfFacilities->where('facility_name',$allOxygenData[$count][2])->first()
                                                ? $listOfFacilities->where('facility_name',$allOxygenData[$count][2])->first()->odas_facility_id
                                                : null;
                //Log::debug('-->' . $allOxygenData[$count][2]);
                $facilityInfoIdToInsert     =   $listOfFacilities->where('facility_name',$allOxygenData[$count][2])->first()
                                                ? $listOfFacilities->where('facility_name',$allOxygenData[$count][2])->first()->id
                                                : null;

                //dd($allOxygenData[$count][5]);
                /// dump($allOxygenData[$count][2][$count][2] . " --> " . $odasFacilityIdToInsert);
                //dd($allOxygenData[$count][4]);
                if($odasFacilityIdToInsert !== null && $facilityInfoIdToInsert !==null && $allOxygenData[$count][4] !=""){
                    $facilityNameForDB                          =   isset($allOxygenData[$count][2]) == false ?  'Empty' : $allOxygenData[$count][2];
                    $facilityNameForDB                          =   $facilityNameForDB != "" ? $facilityNameForDB : 0;

                    $supplySourceForDB                          =   isset($allOxygenData[$count][3]) == false ?  'Empty' : $allOxygenData[$count][3];
                    $supplySourceForDB                          =   $supplySourceForDB != "" ? $supplySourceForDB : 0;

                    $timeOfUpdateForDB                          =   isset($allOxygenData[$count][4]) == false ?  'Empty' : $allOxygenData[$count][4];
                    $timeOfUpdateForDB                          =   $timeOfUpdateForDB != "" ? $timeOfUpdateForDB : 0;

                    $noOfPatientsOnO2                           =   isset($allOxygenData[$count][5]) == false ?  'Empty' : $allOxygenData[$count][5];
                    $noOfPatientsOnO2                           =   $noOfPatientsOnO2 != "" ? $noOfPatientsOnO2 : 0;

                    $o2SupportedBedsForDB                       =   isset($allOxygenData[$count][6]) == false ?  'Empty' : $allOxygenData[$count][6];
                    $o2SupportedBedsForDB                       =   $o2SupportedBedsForDB != "" ? $o2SupportedBedsForDB : 0;

                    $icuBedsForDB                               =   isset($allOxygenData[$count][7]) == false ?  'Empty' : $allOxygenData[$count][7];
                    $icuBedsForDB                               =   $icuBedsForDB != "" ? $icuBedsForDB : 0;

                    $noOfOxygenatedBedsIncludingICUForDB        =   isset($allOxygenData[$count][8]) == false ?  'Empty' : $allOxygenData[$count][8];
                    $noOfOxygenatedBedsIncludingICUForDB        =   $noOfOxygenatedBedsIncludingICUForDB != "" ? $noOfOxygenatedBedsIncludingICUForDB : 0;

                    $psaInLmuForDB                              =   isset($allOxygenData[$count][9]) == false ?  'Empty' : $allOxygenData[$count][9];
                    $psaInLmuForDB                              =   $psaInLmuForDB != "" ? $psaInLmuForDB : 0;

                    //$isActiveForDB                              =   isset($allOxygenData[$count][10]) == false ?  'Empty' : $allOxygenData[$count][10];
                    $isActiveForDB                              =   isset($allOxygenData[$count][10]) == false ?  'FALSE' : 'TRUE';
                    $isActiveForDB                              =   $isActiveForDB != "" ? $isActiveForDB : 'FALSE';

                    $plannedPSACapacityInCumForDB               =   isset($allOxygenData[$count][11]) == false ?  'Empty' : $allOxygenData[$count][11];
                    $plannedPSACapacityInCumForDB               =   $plannedPSACapacityInCumForDB != "" ? $plannedPSACapacityInCumForDB : 0;

                    $psaCapacityInCumForDB                      =   isset($allOxygenData[$count][12]) == false ?  'Empty' : $allOxygenData[$count][12];
                    $psaCapacityInCumForDB                      =   $psaCapacityInCumForDB != "" ? $psaCapacityInCumForDB : 0;

                    $psaGenerationCapacityInMT                  =   ConvertCuMToMT($psaCapacityInCumForDB);
                    $psaStorageCapacityInMT                     =   ConvertCuMToMT($psaCapacityInCumForDB);

                    $cryogenicPlantInLtrForDB                   =   isset($allOxygenData[$count][13]) == false ?  'Empty' : $allOxygenData[$count][13];
                    $cryogenicPlantInLtrForDB                   =   $cryogenicPlantInLtrForDB != "" ? $cryogenicPlantInLtrForDB : 0;

                    $lmoCurrentStorageCapacityInMT              =   ConvertCuMToMT($cryogenicPlantInLtrForDB);

                    $plannedCryoCapacityInCumForDB              =   isset($allOxygenData[$count][14]) == false ?  'Empty' : $allOxygenData[$count][14];
                    $plannedCryoCapacityInCumForDB              =   $plannedCryoCapacityInCumForDB != "" ? $plannedCryoCapacityInCumForDB : 0;

                    $cryogenicPlantCapacityInCumForDB           =   isset($allOxygenData[$count][15]) == false ?  'Empty' : $allOxygenData[$count][15];
                    $cryogenicPlantCapacityInCumForDB           =   $cryogenicPlantCapacityInCumForDB !="" ? $cryogenicPlantCapacityInCumForDB : 0;

                    $lmoCurrentStockInMT                        =   ConvertCuMToMT($cryogenicPlantCapacityInCumForDB);
                    $lmoStorageCapacityStockInMT                =   ConvertCuMToMT($cryogenicPlantCapacityInCumForDB);

                    $noOfEmptyTypeBCylindersForDB               =   isset($allOxygenData[$count][16]) == false ?  0 : $allOxygenData[$count][16];
                    $noOfEmptyTypeBCylindersForDB               =   $noOfEmptyTypeBCylindersForDB != "" ? $noOfEmptyTypeBCylindersForDB : 0;

                    $noOfTypeBCylindersInTransitForDB           =   isset($allOxygenData[$count][17]) == false ?  0 : $allOxygenData[$count][17];
                    $noOfTypeBCylindersInTransitForDB           =   $noOfTypeBCylindersInTransitForDB !="" ? $noOfTypeBCylindersInTransitForDB : 0;

                    $noOfFilledTypeBCylindersForDB              =   isset($allOxygenData[$count][18]) == false ?  0 : $allOxygenData[$count][18];
                    $noOfFilledTypeBCylindersForDB              =   $noOfFilledTypeBCylindersForDB !="" ? $noOfFilledTypeBCylindersForDB : 0;

                    $totalTypeBCylindersAvailableForDB          =   isset($allOxygenData[$count][19]) == false ?  0 : $allOxygenData[$count][19];
                    $totalTypeBCylindersAvailableForDB          =   $totalTypeBCylindersAvailableForDB != "" ? $totalTypeBCylindersAvailableForDB : 0;

                    $typeBConsumedIn24HoursForDB                =   isset($allOxygenData[$count][20]) == false ?  0 : $allOxygenData[$count][20];
                    $typeBConsumedIn24HoursForDB                =   $typeBConsumedIn24HoursForDB !="" ? $typeBConsumedIn24HoursForDB : 0;

                    //dd($typeBConsumedIn24HoursForDB);
                    $noOfEmptyTypeDCylindersForDB               =   isset($allOxygenData[$count][21]) == false ?  0 : $allOxygenData[$count][21];
                    $noOfEmptyTypeDCylindersForDB               =   $noOfEmptyTypeDCylindersForDB !="" ? $noOfEmptyTypeDCylindersForDB : 0;

                    $noOfTypeDCylindersInTransitForDB           =   isset($allOxygenData[$count][22]) == false ?  0 : $allOxygenData[$count][22];
                    $noOfTypeDCylindersInTransitForDB           =   $noOfTypeDCylindersInTransitForDB != "" ? $noOfTypeDCylindersInTransitForDB : 0;

                    $noOfFilledTypeDCylindersForDB              =   isset($allOxygenData[$count][23]) == false ?  0 : $allOxygenData[$count][23];
                    $noOfFilledTypeDCylindersForDB              =   $noOfFilledTypeDCylindersForDB != "" ? $noOfFilledTypeDCylindersForDB : 0;

                    $totalTypeDCylindersAvailableForDB          =   isset($allOxygenData[$count][24]) == false ?  0 : $allOxygenData[$count][24];
                    $totalTypeDCylindersAvailableForDB          =   $totalTypeDCylindersAvailableForDB != "" ? $totalTypeDCylindersAvailableForDB : 0;

                    $typeDConsumedIn24HoursForDB                =   isset($allOxygenData[$count][25]) == false ?  0 : $allOxygenData[$count][25];
                    $typeDConsumedIn24HoursForDB                =   $typeDConsumedIn24HoursForDB !="" ? $typeDConsumedIn24HoursForDB : 0;

                    $o2TypeDAndTypeBCapacityInCumForDB          =   isset($allOxygenData[$count][26]) == false ?  0 : $allOxygenData[$count][26];
                    $o2TypeDAndTypeBCapacityInCumForDB          =   $o2TypeDAndTypeBCapacityInCumForDB != "" ? $o2TypeDAndTypeBCapacityInCumForDB : 0;

                    $overallO2AvailabilityInCumForDB            =   isset($allOxygenData[$count][27]) == false ?  0 : $allOxygenData[$count][27];
                    $overallO2AvailabilityInCumForDB            =   $overallO2AvailabilityInCumForDB != "" ? $overallO2AvailabilityInCumForDB : 0;

                    $actualO2AvailabilityInCumForDB             =   isset($allOxygenData[$count][28]) == false ?  0 : $allOxygenData[$count][28];
                    $actualO2AvailabilityInCumForDB             =   $actualO2AvailabilityInCumForDB !="" ? $actualO2AvailabilityInCumForDB : 0;

                    $noOfBipapMachinesForDB                     =   isset($allOxygenData[$count][30]) == false ?  0 : $allOxygenData[$count][30];
                    $noOfBipapMachinesForDB                     =   $noOfBipapMachinesForDB != "" ? $noOfBipapMachinesForDB : 0;

                    $noOfO2ConcentratorsForDB                   =   isset($allOxygenData[$count][31]) == false ?  0 : $allOxygenData[$count][31];
                    $noOfO2ConcentratorsForDB                   =   $noOfO2ConcentratorsForDB != "" ? $noOfO2ConcentratorsForDB : 0;
                    //dump($noOfO2ConcentratorsForDB);
                    $unaccountedTypeBForDb                      =   isset($allOxygenData[$count][32]) == false ?  0 : $allOxygenData[$count][32];
                    $unaccountedTypeBForDb                      =   $unaccountedTypeBForDb != "" ? $unaccountedTypeBForDb : 0;

                    $unaccountedTypeDForDb                      =   isset($allOxygenData[$count][33]) == false ?  0 : $allOxygenData[$count][33];
                    $unaccountedTypeDForDb                      =   $unaccountedTypeDForDb != "" ? $unaccountedTypeDForDb : 0;

                    $appxDemandWithCurrLoadInHrsForDB           =   isset($allOxygenData[$count][34]) == false ?  0 : $allOxygenData[$count][34];
                    $appxDemandWithCurrLoadInHrsForDB           =   $appxDemandWithCurrLoadInHrsForDB != "" ? $appxDemandWithCurrLoadInHrsForDB : 0;

                    $appxDemandWithCurrNoOfPatientsInCumForDB   =   isset($allOxygenData[$count][35]) == false ?  0 : $allOxygenData[$count][35];
                    $appxDemandWithCurrNoOfPatientsInCumForDB   =   $appxDemandWithCurrNoOfPatientsInCumForDB != "" ? $appxDemandWithCurrNoOfPatientsInCumForDB : 0;

                    $appDemandWithAllBedsFullInHrsForDB         =   isset($allOxygenData[$count][36]) == false ?  0 : $allOxygenData[$count][36];
                    $appDemandWithAllBedsFullInHrsForDB         =   $appDemandWithAllBedsFullInHrsForDB !="" ? $appDemandWithAllBedsFullInHrsForDB : 0;

                    $appDemandWithAllBedsFullInCumForDB         =   isset($allOxygenData[$count][37]) == false ?  0 : $allOxygenData[$count][37];
                    $appDemandWithAllBedsFullInCumForDB         =   $appDemandWithAllBedsFullInCumForDB != "" ? $appDemandWithAllBedsFullInCumForDB : 0;

                    /// Facility Bed Occupancy Info
                    $noOfGenBedsForDB                           =   isset($allOxygenData[$count][49]) == false ?  0 : $allOxygenData[$count][49];
                    $noOfGenBedsForDB                           =   $noOfGenBedsForDB != "" ? $noOfGenBedsForDB : 0;

                    $noOfHDUBedsForDB                           =   isset($allOxygenData[$count][50]) == false ?  0: $allOxygenData[$count][50];
                    $noOfHDUBedsForDB                           =   $noOfHDUBedsForDB !="" ? $noOfHDUBedsForDB : 0;

                    $noOfICUBedsForDB                           =   isset($allOxygenData[$count][51]) == false ?  0 : $allOxygenData[$count][51];
                    $noOfICUBedsForDB                           =   $noOfICUBedsForDB !="" ? $noOfICUBedsForDB : 0;

                    $noOfO2ConcentratorsBedInfoForDB            =   $noOfO2ConcentratorsForDB;
                    $noOfVentBedsForDB                          =   isset($allOxygenData[$count][52]) == false ?  0 : $allOxygenData[$count][52];
                    $noOfVentBedsForDB                          =   $noOfVentBedsForDB != "" ? $noOfVentBedsForDB : 0;

                    $requestIdForDB                             =   $generatedUUID;


                    /// Oxygen Demand Estimation
                    $demandEstimationForDB                     =    isset($allOxygenData[$count][53]) == false ?  0 : $allOxygenData[$count][53];
                    $demandEstimationForDB                     =    $demandEstimationForDB !="" ? $demandEstimationForDB : 0;

                    $demandEstimationRemarksForDB              =    isset($allOxygenData[$count][54]) == false ?  'No Remarks' : $allOxygenData[$count][54];
                    $demandEstimationRemarksForDB              =    $demandEstimationRemarksForDB !="" ? $demandEstimationRemarksForDB : 0;

                    /// Analysis Data
                    // $demandForDB                                                        =   isset($allOxygenData[$count][38]) == false ?  'Empty' : $allOxygenData[$count][38];
                    // $availableSupplyForDB                                               =   isset($allOxygenData[$count][39]) == false ?  'Empty' : $allOxygenData[$count][39];
                    // $remainingDemandForDB                                               =   isset($allOxygenData[$count][40]) == false ?  'Empty' : $allOxygenData[$count][40];
                    // $supplyInTransitForDB                                               =   isset($allOxygenData[$count][41]) == false ?  'Empty' : $allOxygenData[$count][41];
                    // $remainingDemandAfterFactoringCylindersInTransitForDB               =   isset($allOxygenData[$count][42]) == false ?  'Empty' : $allOxygenData[$count][42];
                    // $capacityOfTypeDEmptyCylindersForDB                                 =   isset($allOxygenData[$count][43]) == false ?  'Empty' : $allOxygenData[$count][43];
                    // $capacityOfTypeBEmptyCylindersAtFacilityForDB                       =   isset($allOxygenData[$count][44]) == false ?  'Empty' : $allOxygenData[$count][44];
                    // $noOfTypeDEmptyCylindersToBeSentForRefillingForDB                   =   isset($allOxygenData[$count][45]) == false ?  'Empty' : $allOxygenData[$count][45];
                    // $noOfTypeBEmptyCylindersToBeSentForRefillingForDB                   =   isset($allOxygenData[$count][46]) == false ?  'Empty' : $allOxygenData[$count][46];
                    // $noOfTypeBEmptyCylindersToBeReturnedForDB                           =   isset($allOxygenData[$count][47]) == false ?  'Empty' : $allOxygenData[$count][47];
                    // $noOfTypeDEmptyCylindersToBeReturnedForDB                           =   isset($allOxygenData[$count][48]) == false ?  'Empty' : $allOxygenData[$count][48];

                    // dd($typeBConsumedIn24HoursForDB . ' : ' . $typeDConsumedIn24HoursForDB);

                    /// OxygenFacilityConsuption Data
                    $typeBConsumedIn24HoursForDB        =  $typeBConsumedIn24HoursForDB ? $typeBConsumedIn24HoursForDB : 0;
                    $typeBCylinderCapacity              =  $typeBCylinderCapacity ? $typeBCylinderCapacity : 0;

                    $typeDConsumedIn24HoursForDB        =  $typeDConsumedIn24HoursForDB ? $typeDConsumedIn24HoursForDB : 0;
                    $typeDCylinderCapacity              =  $typeDCylinderCapacity ? $typeDCylinderCapacity : 0;

                    $totalOxygenConsumedForDB        =  ConvertCuMToMT(($typeBConsumedIn24HoursForDB*$typeBCylinderCapacity) + ($typeDConsumedIn24HoursForDB*$typeDCylinderCapacity));
                    $totalOxygenDeliveredForDB       =  ConvertCuMToMT(($noOfFilledTypeBCylindersForDB*$typeBCylinderCapacity) + ($noOfFilledTypeDCylindersForDB*$typeDCylinderCapacity));
                    $totalOxygenGeneratedForDB       =  ConvertCuMToMT($psaCapacityInCumForDB);
                    //dd($totalOxygenGeneratedForDB);

                    $oxygenParamsForDB                    =   [
                        'facility_information_id'                           =>  $facilityInfoIdToInsert,
                        'odas_facility_id'                                  =>  $odasFacilityIdToInsert,
                        'facility_name'                                     =>  $facilityNameForDB,
                        'supply_source'                                     =>  $supplySourceForDB,
                        'time_of_update'                                    =>  $timeOfUpdateForDB ? $timeOfUpdateForDB : '0',
                        'no_of_patients_on_o2'                              =>  $noOfPatientsOnO2 ? $noOfPatientsOnO2 : 0,
                        'no_of_o2_supported_beds'                           =>  $o2SupportedBedsForDB ? $o2SupportedBedsForDB : 0,
                        'no_of_ICU_beds'                                    =>  $icuBedsForDB ? $icuBedsForDB : 0,
                        'no_of_oxygenated_beds_including_ICU'               =>  $noOfOxygenatedBedsIncludingICUForDB ? $noOfOxygenatedBedsIncludingICUForDB : 0,
                        'psa_in_lpm'                                        =>  $psaInLmuForDB ? $psaInLmuForDB : 0,
                        'is_active'                                         =>  $isActiveForDB,
                        'planned_psa_capacity_in_cum'                       =>  $plannedPSACapacityInCumForDB ? $plannedPSACapacityInCumForDB : 0,
                        'psa_capacity_in_cum'                               =>  $psaCapacityInCumForDB ? $psaCapacityInCumForDB : 0,
                        'psa_gen_capacity_in_MT'                            =>  $psaGenerationCapacityInMT ? $psaGenerationCapacityInMT : 0,
                        'psa_storage_capacity_in_MT'                        =>  $psaStorageCapacityInMT ? $psaStorageCapacityInMT : 0,
                        'cryogenic_plant_in_ltr'                            =>  $cryogenicPlantInLtrForDB ? $cryogenicPlantInLtrForDB : 0,
                        'lmo_current_storage_capacity_in_MT'                =>  $lmoCurrentStorageCapacityInMT ? $lmoCurrentStorageCapacityInMT : 0,
                        'planned_cryogenic_capacity_in_cum'                 =>  $plannedCryoCapacityInCumForDB ? $plannedCryoCapacityInCumForDB : 0,
                        'lmo_current_stock_in_MT'                           =>  $lmoCurrentStockInMT ? $lmoCurrentStockInMT : 0,
                        'psa_storage_capacity_in_MT'                        =>  $lmoStorageCapacityStockInMT ? $lmoStorageCapacityStockInMT : 0,
                        'cryogenic_capacity_in_cum'                         =>  $cryogenicPlantCapacityInCumForDB ? $cryogenicPlantCapacityInCumForDB : 0,
                        'no_of_empty_typeB_cylinders'                       =>  $noOfEmptyTypeBCylindersForDB ? $noOfEmptyTypeBCylindersForDB : 0,
                        'no_typeB_cylinders_in_transit'                     =>  $noOfTypeBCylindersInTransitForDB ? $noOfTypeBCylindersInTransitForDB : 0,
                        'no_filled_typeB_cylinders'                         =>  $noOfFilledTypeBCylindersForDB ? $noOfFilledTypeBCylindersForDB : 0,
                        'total_typeB_cylinders_available'                   =>  $totalTypeBCylindersAvailableForDB ? $totalTypeBCylindersAvailableForDB : 0,
                        'no_of_consumed_typeB_cylinders_in_last_24_hours'   =>  $typeBConsumedIn24HoursForDB ? $typeBConsumedIn24HoursForDB : 0,

                        'no_of_empty_typeD_cylinders'                       =>  $noOfEmptyTypeDCylindersForDB ? $noOfEmptyTypeDCylindersForDB : 0,
                        'no_typeD_cylinders_in_transit'                     =>  $noOfTypeDCylindersInTransitForDB ? $noOfTypeDCylindersInTransitForDB : 0,
                        'no_filled_typeD_cylinders'                         =>  $noOfFilledTypeDCylindersForDB ? $noOfFilledTypeDCylindersForDB : 0 ,
                        'total_typeD_cylinders'                             =>  $totalTypeDCylindersAvailableForDB ? $totalTypeDCylindersAvailableForDB : 0,
                        'no_of_consumed_typeD_cylinders_in_last_24_hours'   =>  $typeDConsumedIn24HoursForDB ? $typeDConsumedIn24HoursForDB : 0,

                        'o2_typeD_and_typeB_capacity_in_cum'                =>  $o2TypeDAndTypeBCapacityInCumForDB ? $o2TypeDAndTypeBCapacityInCumForDB : 0,
                        'overall_o2_availability_in_cum'                    =>  $overallO2AvailabilityInCumForDB ? $overallO2AvailabilityInCumForDB : 0,
                        'actual_o2_availability_in_cum'                     =>  $actualO2AvailabilityInCumForDB ? $actualO2AvailabilityInCumForDB : 0,
                        'no_of_BiPAP_machines'                              =>  $noOfBipapMachinesForDB ? $noOfBipapMachinesForDB : 0,
                        'no_of_o2_concentrators'                            =>  $noOfO2ConcentratorsForDB ? $noOfO2ConcentratorsForDB : 0,

                        'unaccounted_typeB'                                 =>  $unaccountedTypeBForDb ? $unaccountedTypeBForDb : 0,
                        'unaccounted_typeD'                                 =>  $unaccountedTypeDForDb ? $unaccountedTypeDForDb : 0,
                        'appx_o2_demand_with_current_load_in_hrs'           =>  $appxDemandWithCurrLoadInHrsForDB ? $appxDemandWithCurrLoadInHrsForDB : 0 ,
                        'appx_o2_demand_with_current_no_of_patients_in_cum' =>  $appxDemandWithCurrNoOfPatientsInCumForDB ? $appxDemandWithCurrNoOfPatientsInCumForDB : 0,
                        'appx_o2_demand_with_all_beds_full'                 =>  $appDemandWithAllBedsFullInHrsForDB ? $appDemandWithAllBedsFullInHrsForDB : 0,

                        'demand_estimation'                                 =>  $demandEstimationForDB,
                        'accuracy_remarks'                                  =>  $demandEstimationRemarksForDB,

                        'requestId'                                         =>  $generatedUUID,
                    ];

                    /// Demand Estimation calculation
                    $tempDemandAccuracyFlagForDB        =   '';
                    $overEstimatedByForDB               =   null;
                    $totalEstimatedDemandForDB          =   '0';
                    $underEstimatedByForDB              =   null;

                    if(isset($allOxygenData[$count][49]) && $allOxygenData[$count][49] != null){
                        $totalEstimatedDemandForDB          = ConvertLPMToMT(($noOfGenBedsForDB * 7.4) + ($noOfHDUBedsForDB * 10) + ($noOfICUBedsForDB * 10) + ($noOfVentBedsForDB * 48));

                        if($demandEstimationForDB < 0){
                            /// Over-estimated
                            $tempDemandAccuracyFlagForDB    = 'Over';
                            if(str_contains($demandEstimationForDB, '-')){
                                $overEstimatedByForDB   = ltrim($demandEstimationForDB, $demandEstimationForDB[0]);
                            }
                        }
                        elseif($demandEstimationForDB == 0){
                            /// Accurate
                            $tempDemandAccuracyFlagForDB    = 'Accurate';
                        }
                        else{
                            /// Under-estimated
                            $tempDemandAccuracyFlagForDB    = 'Under';
                            $underEstimatedByForDB          = $demandEstimationForDB;
                        }
                    }

                    //dd($tempDemandAccuracyFlagForDB . ' : ' . 'Over-> ' . $overEstimatedByForDB . ' : Under-> ' . $underEstimatedByForDB . ' : Total-> ' . $totalEstimatedDemandForDB);
                    //Log::debug($oxygenParamsForDB);

                    if($oxygenDataForHosp == null && $facilityInfoIdToInsert !==null){         /// Add new into oxygen_data
                        $createdOxygenData              =   HealthFacilityOxygen::create($oxygenParamsForDB);

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
                        Log::debug('Created Facility Bed Info data ' . $createdFacilityBedInfo->id);
                        if(isset($allOxygenData[$count][49]) && $allOxygenData[$count][49] != null){
                            /// Add Facility Oxygen Demand Here
                            $createdFacilityOxygenDemand        =   FacilityOxygenDemand::create([
                                'oxygen_data_id'                        => $createdOxygenData->id,
                                'odas_facility_id'                      => $odasFacilityIdToInsert,
                                'accuracy_remarks'                      => $demandEstimationRemarksForDB,
                                'demand_accuracy_flag'                  => $tempDemandAccuracyFlagForDB,
                                'demand_for_date'                       => $demandForDate,
                                'demand_raised_date'                    => $demandRaisedDate,
                                'over_estimated_by'                     => $overEstimatedByForDB,
                                'total_estimated_demand'                => $totalEstimatedDemandForDB,
                                'under_estimated_by'                    => $underEstimatedByForDB,
                                'requestId'                             => $generatedUUID
                            ]);

                            Log::debug('Created Oxygen demand data ' . $createdFacilityOxygenDemand->id);
                        }
                        //dd($totalOxygenDeliveredForDB ? $totalOxygenDeliveredForDB : 0);

                        $createdFacilityOxygenConsumption   =   FacilityOxygenConsumption::create([
                            'oxygen_data_id'                    =>  $createdOxygenData->id,
                            'consumption_for_date'              =>  $yesterdayDate,
                            'consumption_updated_date'          =>  $occupancyDate,
                            'total_oxygen_consumed'             =>  $totalOxygenConsumedForDB ? $totalOxygenConsumedForDB : 0,
                            'total_oxygen_delivered'            =>  $totalOxygenDeliveredForDB ? $totalOxygenDeliveredForDB : 0,
                            'total_oxygen_generated'            =>  $totalOxygenGeneratedForDB ? $totalOxygenGeneratedForDB : 0,
                            'odas_facility_id'                  =>  $odasFacilityIdToInsert,
                            'requestId'                         =>  $generatedUUID
                        ]);
                        Log::debug('Created Oxygen consumption data ' . $createdFacilityOxygenConsumption->id);

                        //dd($createdOxygenData->id);
                        /// Health Facility Analysis Table
                        // $createdHealthFacilityAnalysis      = HealthFacilityAnalysis::create([
                        //     'oxygen_data_id'                                                =>  $createdOxygenData->id,
                        //     'demand'                                                        =>  $demandForDB,
                        //     'available_supply_at_facility'                                  =>  $availableSupplyForDB,
                        //     'remaining_demand_after_exhausting_filled_cylinders'            =>  $remainingDemandForDB,
                        //     'supply_in_transit_of_typeB'                                    =>  $supplyInTransitForDB,
                        //     'remaining_demand_after_factoring_in_transit_cylinders'         =>  $remainingDemandAfterFactoringCylindersInTransitForDB,
                        //     'capacity_of_empty_cylinders_typeD'                             =>  $capacityOfTypeDEmptyCylindersForDB,
                        //     'capacity_of_empty_cylinders_typeB'                             =>  $capacityOfTypeBEmptyCylindersAtFacilityForDB,
                        //     'no_of_typeD_cylinders_to_refill'                               =>  $noOfTypeDEmptyCylindersToBeSentForRefillingForDB,
                        //     'no_of_typeB_cylinders_to_refill_if_demand_unmet_typeB'         =>  $noOfTypeBEmptyCylindersToBeSentForRefillingForDB,
                        //     'typeB_empty_cylinders_to_be_returned'                          =>  $noOfTypeBEmptyCylindersToBeReturnedForDB,
                        //     'typeD_empty_cylinders_to_be_returned'                          =>  $noOfTypeDEmptyCylindersToBeReturnedForDB,
                        // ]);
                    }
                    elseif($oxygenDataForHosp !== null) {
                        /// Update
                        Log::debug('Processing for update: ' . $oxygenDataForHosp);
                        $OxygenDataForHospToUpdate                  =   $oxygenDataForHosp;
                        $OxygenDataForHospToUpdate->update($oxygenParamsForDB);

                        Log::debug('Oxygen data updated successfully!');

                        $facilityBedInfoUpdating             = FacilityBedInfo::find($OxygenDataForHospToUpdate->FacilityBedInfo->id)->update([
                            'odas_facility_id'                              =>  $odasFacilityIdToInsert,
                            'no_gen_beds'                                   =>  $noOfGenBedsForDB ? $noOfGenBedsForDB : 0,
                            'no_hdu_beds'                                   =>  $noOfHDUBedsForDB ? $noOfHDUBedsForDB : 0,
                            'no_icu_beds'                                   =>  $noOfICUBedsForDB ? $noOfICUBedsForDB : 0,
                            'no_o2_concentrators'                           =>  $noOfO2ConcentratorsBedInfoForDB ? $noOfO2ConcentratorsBedInfoForDB : 0,
                            'no_vent_beds'                                  =>  $noOfVentBedsForDB ? $noOfVentBedsForDB : 0,
                            'occupancy_date'                                =>  $occupancyDate,
                            'requestId'                                     =>  $requestIdForDB,
                        ]);

                        Log::debug('Facility Bed Info Updated: ' . $OxygenDataForHospToUpdate->FacilityBedInfo->id);
                        ///dd($OxygenDataForHospToUpdate->FacilityOxygenDemand);
                        if(FacilityOxygenDemand::find($OxygenDataForHospToUpdate->FacilityOxygenDemand) !== null){
                            /// Update Facility Oxygen Demand Here
                            $facilityOxygenDemandUpdating        =   FacilityOxygenDemand::find($OxygenDataForHospToUpdate->FacilityOxygenDemand->id)->update([
                                'accuracy_remarks'                      => $demandEstimationRemarksForDB,
                                'demand_accuracy_flag'                  => $tempDemandAccuracyFlagForDB,
                                'demand_for_date'                       => $demandForDate,
                                'demand_raised_date'                    => $demandRaisedDate,
                                'over_estimated_by'                     => $overEstimatedByForDB,
                                'total_estimated_demand'                => $totalEstimatedDemandForDB,
                                'under_estimated_by'                    => $underEstimatedByForDB,
                                'requestId'                             => $generatedUUID
                            ]);
                            Log::debug('Facility Oxygen Demand Info Updated: ' . $OxygenDataForHospToUpdate->FacilityOxygenDemand->id);
                        }

                        $facilityOxygenConsumptionUpdating   =   FacilityOxygenConsumption::find($OxygenDataForHospToUpdate->FacilityOxygenConsumption->id)->update([
                            'consumption_for_date'              =>  $yesterdayDate,
                            'consumption_updated_date'          =>  $occupancyDate,
                            'total_oxygen_consumed'             =>  $totalOxygenConsumedForDB ? $totalOxygenConsumedForDB : 0,
                            'total_oxygen_delivered'            =>  $totalOxygenDeliveredForDB ? $totalOxygenDeliveredForDB : 0,
                            'total_oxygen_generated'            =>  $totalOxygenGeneratedForDB ? $totalOxygenGeneratedForDB : 0,
                            'odas_facility_id'                  =>  $odasFacilityIdToInsert,
                            'requestId'                         =>  $generatedUUID
                        ]);
                        Log::debug('Facility Oxygen Consumption Info Updated: ' . $OxygenDataForHospToUpdate->FacilityOxygenConsumption->id);
                    }
                    DB::commit();
                }
                // else{
                //     /// No ODAS Facility ID in DB, generate ID first.
                //     dd('Else');
                // }
            }
            //dd("stop");
            //DB::commit();
            return redirect()->back()->with('success', 'Oxygen Data Fetched!');
        }
        catch(\Exception $ex){
            // if($ex->getMessage() == 'A non-numeric value encountered'){
            //     dd($allOxygenData[$count]);
            // }
            ///dd($ex->getMessage());
            DB::rollback();
            return redirect()->back()->withErrors($ex->getMessage())->withInput();
        }
        //Insert into DB if data not already present else update data

    }


    /// ******************************************************************************************* ///
    /// ******************************** API Functions ******************************************** ///
    /// ******************************************************************************************* ///

    public function UpdateOxygenDataByHospital($hospitalName){
        Log::debug("Attempting to push Oxygen Data To ODAS for: " . $hospitalName);
        try{
            $odasApiBAseURL                                 =   config('odas.odas_base_url');
            $updateFacilityOxygenInfraEndpointURI           =   'v1.0/odas/update-facility-o2-infra';
            $facilityBeingProcessed                         =   HealthFacilityOxygen::where('facility_name',$hospitalName)->latest()->first();

            $newToken                                       =   getODASAccessToken();
            Log::error($facilityBeingProcessed);
            // Save the authToken to the DB
            $odasToken                =   new ODASToken();
            $odasToken->token         =   $newToken;
            $odasToken->timestamp_utc =   Carbon::now()->toJSON();
            $odasToken->save();
            //dd('success');
            Log::debug("API Auth Token Generated for O2 data push!");

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
                Log::debug('Facility Oxygen Infrastructure Updated to ODAS Successfully!' . ' - ' .$dataRes['referencenumber']);
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
        Log::debug("Attempting to Push Bed Occupancy Data to ODAS for: " . $odasFacilityId);
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
                $oxygenBedOccupancyInfo                           =   FacilityBedInfo::find($facilityBeingProcessed->id);
                $oxygenBedOccupancyInfo->odas_reference_number    =   $dataRes['referencenumber'] ? $dataRes['referencenumber'] : 'No Reference Number';
                $oxygenBedOccupancyInfo->status                   =   $dataRes['status'] ? $dataRes['status'] : 'No Status Number';
                $oxygenBedOccupancyInfo->save();

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

    public function UpdateOxygenDemand($odasFacilityId){
        Log::debug("Attempting to push O2 Demand Data for: " . $odasFacilityId);
        try{
            $odasApiBAseURL                                 =   config('odas.odas_base_url');
            $updateO2DemandEndpointURI                      =   'v1.0/odas/update-facility-oxygen-demand';
            $facilityOxygenBeingProcessed                   =   FacilityOxygenDemand::where('odas_facility_id',$odasFacilityId)->latest()->first();

            if($facilityOxygenBeingProcessed != null){
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
                    "demandInfo" => [
                        "accuracy_remarks"              => $facilityOxygenBeingProcessed->accuracy_remarks,
                        "demand_accuracy_flag"          => $facilityOxygenBeingProcessed->demand_accuracy_flag,
                        "demand_for_date"               => $facilityOxygenBeingProcessed->demand_for_date,
                        "demand_raised_date"            => $facilityOxygenBeingProcessed->demand_raised_date,
                        "over_estimated_by"             => $facilityOxygenBeingProcessed->over_estimated_by,
                        "total_estimated_demand"        => $facilityOxygenBeingProcessed->total_estimated_demand,
                        "under_estimated_by"            => $facilityOxygenBeingProcessed->under_estimated_by,
                    ],
                    "facilityid"                    => $facilityOxygenBeingProcessed->odas_facility_id,
                    "requestId"                     => $facilityOxygenBeingProcessed->requestId,
                    "timestamp"                     => $odasToken->timestamp_utc
                );

                $client = new Client();
                Log::debug("Attempting to push O2 Demand data to API: " . $odasApiBAseURL.$updateO2DemandEndpointURI);
                $response = $client->post($odasApiBAseURL.$updateO2DemandEndpointURI, [
                    'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json','Authorization'=>'Bearer ' .$odasTokenToUse,],
                    'body'    => json_encode($params)
                ]);
                $dataRes =   json_decode($response->getBody(), true);

                if($dataRes !== null){
                    $oxygenDemandInfo                           =   FacilityOxygenDemand::find($facilityOxygenBeingProcessed->id);
                    $oxygenDemandInfo->odas_reference_number    =   $dataRes['referencenumber'] ? $dataRes['referencenumber'] : 'No Reference Number';
                    $oxygenDemandInfo->status                   =   $dataRes['status'] ? $dataRes['status'] : 'No Status Number';
                    $oxygenDemandInfo->save();

                    Log::debug('----------------------------------------');
                    Log::debug('Oxygen Demand Data Updated Successfully!' . ' - ' .$dataRes['referencenumber']);
                    /// Update Demand Data
                    return redirect()->back()->with('success', 'Oxygen Demand Data Updated Successfully!');
                }
            }
            else{
                Log::debug('No Oxygen Demand Data for FacilityId: ' . $odasFacilityId. ' Skipping');
                return redirect()->back()->with('error', 'No Oxygen Demand Data for FacilityId!');
            }

        }
        catch(\Exception $ex){
            Log::error($ex->getMessage());
            return redirect()->back()->with('error', $ex->getMessage());
        }
    }

    public function UpdateFacilityO2ConsumptionData($odasFacilityId){
        Log::debug("Attempting to push O2 Consumtion Data for: " . $odasFacilityId);
        try{
            $odasApiBAseURL                                 =   config('odas.odas_base_url');
            $updateO2ConsumptionEndpointURI                 =   'v1.0/odas/update-facility-oxygen-consumption';
            $facilityOxygenBeingProcessed                   =   FacilityOxygenConsumption::where('odas_facility_id',$odasFacilityId)->latest()->first();

            if($facilityOxygenBeingProcessed != null){
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
                    "consumptionInfo" => [
                        "consumption_for_date"              => $facilityOxygenBeingProcessed->consumption_for_date,
                        "consumption_updated_date"          => $facilityOxygenBeingProcessed->consumption_updated_date,
                        "total_oxygen_consumed"             => $facilityOxygenBeingProcessed->total_oxygen_consumed,
                        "total_oxygen_delivered"            => $facilityOxygenBeingProcessed->total_oxygen_delivered,
                        "total_oxygen_generated"            => $facilityOxygenBeingProcessed->total_oxygen_generated,
                    ],
                    "facilityid"                    => $facilityOxygenBeingProcessed->odas_facility_id,
                    "requestId"                     => $facilityOxygenBeingProcessed->requestId,
                    "timestamp"                     => $odasToken->timestamp_utc
                );

                //dd($params);
                $client = new Client();
                Log::debug("Attempting to push O2 consumption data to API: " . $odasApiBAseURL.$updateO2ConsumptionEndpointURI);
                $response = $client->post($odasApiBAseURL.$updateO2ConsumptionEndpointURI, [
                    'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json','Authorization'=>'Bearer ' .$odasTokenToUse,],
                    'body'    => json_encode($params)
                ]);
                $dataRes =   json_decode($response->getBody(), true);

                if($dataRes !== null){
                    $oxygenConsumptionInfo                           =   FacilityOxygenConsumption::find($facilityOxygenBeingProcessed->id);
                    $oxygenConsumptionInfo->odas_reference_number    =   $dataRes['referencenumber'] ? $dataRes['referencenumber'] : 'No Reference Number';
                    $oxygenConsumptionInfo->status                   =   $dataRes['status'] ? $dataRes['status'] : 'No Status Number';
                    $oxygenConsumptionInfo->save();

                    Log::debug('----------------------------------------');
                    Log::debug('Oxygen Consumption Data Updated Successfully!' . ' - ' .$dataRes['referencenumber']);
                    /// Update Facility Infrastructure Data
                    return redirect()->back()->with('success', 'Oxygen Consumption Data Updated Successfully!');
                }
            }
            else{
                Log::debug('No Data for FacilityId: ' . $odasFacilityId. ' Skipping');
            }
            //dd($facilityBeingProcessed);
        }
        catch(\Exception $ex){
            Log::error($ex->getMessage());
            return redirect()->back()->with('error', $ex->getMessage());
        }
    }
}
