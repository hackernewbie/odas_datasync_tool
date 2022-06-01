<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\Facility;
use App\Models\ODASToken;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\FacilityNodalOfficer;
use App\Services\GoogleSheetService;
use App\Models\FacilityInfrastructure;
use App\Models\ProcessesRun;

class FacilityController extends Controller
{
    public function facilities(){
        try{
            Log::debug("Loading Facilities Page...");
            $allFacilities      = Facility::all();
        }
        catch(\Exception $ex){
            if($ex->getCode() == '42S02'){
                Log::debug("ERROR!! Base Table Missing!");
                return redirect('dashboard')->with('Error', "Base Table Missing!");
            }
        }

        //dd($allFacilities);
        return view('facilities')
                ->with('allFacilities',$allFacilities);
    }

    public function GetFacilities(){
        Log::debug("Attempting to read data from Facility Information Sheet.");
        $gsheet             = new GoogleSheetService();
        $listOfFacilities   =   $gsheet->readGoogleSheet(config('google.facility_sheet_name'),'AL');
        //dd($listOfFacilities);
        if($listOfFacilities == null || count($listOfFacilities) <= 2){
            Log::error("No Data in aFacility Information Sheet.");
            return redirect()->back()->with('error','Source Google Sheet Empty');
        }

        try{
            Log::debug("Reading Facility Information Sheet Data For Insertion");
            DB::beginTransaction();
            /// First Row is info header. Second Row is table Header
            for($count = 2; $count <= count($listOfFacilities)-1; $count++){
                $generatedUUID           = Str::uuid();
                $tempFacilityName        = Facility::where('facility_name',$listOfFacilities[$count][0])->latest()->first();

                Log::debug("Processing Facility: " . $tempFacilityName);

                if(isset($listOfFacilities[$count][1]) == true && isset($listOfFacilities[$count][19]) == true){
                    $odas_facility_id               = null;
                    $facilityName                   = $listOfFacilities[$count][0];
                    $address_line1                  = isset($listOfFacilities[$count][1]) == false ?  'Empty' : $listOfFacilities[$count][1];
                    $address_line2                  = isset($listOfFacilities[$count][2]) == false ?  'Empty' : $listOfFacilities[$count][2];
                    $city_lgd                       = isset($listOfFacilities[$count][4]) == false ?  'Empty' : $listOfFacilities[$count][4];
                    $district_lgd                   = isset($listOfFacilities[$count][6]) == false ?  'Empty' : $listOfFacilities[$count][6];
                    $pincode                        = isset($listOfFacilities[$count][7]) == false ?  'Empty' : $listOfFacilities[$count][7];
                    $state_lgd                      = isset($listOfFacilities[$count][8]) == false ?  'Empty' : $listOfFacilities[$count][8];
                    $sub_district_lgd               = isset($listOfFacilities[$count][9]) == false ?  0 : $listOfFacilities[$count][9];
                    //$ownership_type                 = isset($listOfFacilities[$count][10]) == false ?  'Empty' : $listOfFacilities[$count][10];
                    $ownership_type_code            = isset($listOfFacilities[$count][11]) == false ?  'Empty' : $listOfFacilities[$count][11];
                    $ownership_sub_type_code        = isset($listOfFacilities[$count][13]) == false ?  'Empty' : $listOfFacilities[$count][13];
                    $facility_type                  = isset($listOfFacilities[$count][14]) == false ?  'Empty' : $listOfFacilities[$count][14];
                    $facility_type_code             = isset($listOfFacilities[$count][15]) == false ?  'Empty' : $listOfFacilities[$count][15];
                    /// FacilityId is 16
                    $longitude                      = isset($listOfFacilities[$count][17]) == false ?  'Empty' : $listOfFacilities[$count][17];
                    $longitude                      = isset($listOfFacilities[$count][18]) == false ?  'Empty' : $listOfFacilities[$count][18];

                    $nodalOfficerName               = isset($listOfFacilities[$count][19]) == false ?  'Empty' : $listOfFacilities[$count][19];
                    $nodalOfficerSalutation         = isset($listOfFacilities[$count][20]) == false ?  'Empty' : $listOfFacilities[$count][20];
                    $nodalOfficerFirstName          = isset($listOfFacilities[$count][21]) == false ?  'Empty' : SanitizeString($listOfFacilities[$count][21]);
                    $nodalOfficerMiddleName         = isset($listOfFacilities[$count][22]) == false ?  'Empty' : SanitizeString($listOfFacilities[$count][22]);
                    $nodalOfficerLastName           = isset($listOfFacilities[$count][23]) == false ?  'Empty' : SanitizeString($listOfFacilities[$count][23]);
                    $nodalOfficerDesignation        = $listOfFacilities[$count][24]        == ""    ?  'Empty' : $listOfFacilities[$count][24];
                    $nodalOfficerCountryCode        = isset($listOfFacilities[$count][25]) == false ?  'Empty' : $listOfFacilities[$count][25];
                    $nodalOfficerMobileNumber       = isset($listOfFacilities[$count][26]) == false ?  'Empty' : $listOfFacilities[$count][26];
                    $nodalOfficerEmail              = isset($listOfFacilities[$count][27]) == false ?  'Empty' : $listOfFacilities[$count][27];

                    $general_beds_with_o2           = isset($listOfFacilities[$count][28]) == false ?  0 : $listOfFacilities[$count][28];
                    $hdu_beds                       = isset($listOfFacilities[$count][29]) == false ?  0 : $listOfFacilities[$count][29];
                    $icu_beds                       = isset($listOfFacilities[$count][30]) == false ?  0 : $listOfFacilities[$count][30];
                    $o2_concentrators               = isset($listOfFacilities[$count][31]) == false ?  0 : $listOfFacilities[$count][31];
                    $ventilators                    = isset($listOfFacilities[$count][32]) == false ?  0 : $listOfFacilities[$count][32];


                    $facilityParamsForDB            =   [
                        'facility_name'                     =>  $facilityName,
                        'address_line_1'                    =>  $address_line1,
                        'address_line_2'                    =>  $address_line2,
                        'city_lgd_code'                     =>  $city_lgd,
                        'district_lgd_code'                 =>  $district_lgd,
                        'pincode'                           =>  $pincode,
                        'state_lgd_code'                    =>  $state_lgd,
                        'subdistrict_lgd_code'              =>  $sub_district_lgd,
                        'ownership_type'                    =>  $ownership_type_code,
                        'ownership_subtype'                 =>  $ownership_sub_type_code,
                        'facility_type'                     =>  $facility_type,
                        'facility_type_code'                =>  $facility_type_code,
                        'longitude'                         =>  $longitude,
                        'latitude'                          =>  $longitude,
                        'requestId'                         =>  $generatedUUID,
                    ];
                }

                if($tempFacilityName == null && isset($listOfFacilities[$count][1]) == true && isset($listOfFacilities[$count][17]) && isset($listOfFacilities[$count][18]) && isset($listOfFacilities[$count][19]) == true){
                    $createdFacilityInformation     =   Facility::create($facilityParamsForDB);

                    Log::debug("Facility Information Data Inserted to DB. Facility Name: " . $createdFacilityInformation->id . ' - ' . $facilityName);

                    /// Save Data to the Facility Nodal Officer
                    if(isset($nodalOfficerName) && $nodalOfficerName != null){
                        $generatedUUID                  = Str::uuid();          /// New UUID being Generated
                        $createdFacilityNodalOfficer    =    FacilityNodalOfficer::create([
                            'facility_information_id'           =>  $createdFacilityInformation->id,
                            'officer_name'                      =>  $nodalOfficerName,
                            'officer_salutation'                =>  $nodalOfficerSalutation,
                            'officer_first_name'                =>  $nodalOfficerFirstName,
                            'officer_middle_name'               =>  $nodalOfficerMiddleName,
                            'officer_last_name'                 =>  $nodalOfficerLastName,
                            'officer_designation'               =>  $nodalOfficerDesignation,
                            'officer_country_code'              =>  $nodalOfficerCountryCode,
                            'officer_mobile_number'             =>  $nodalOfficerMobileNumber,
                            'officer_email'                     =>  $nodalOfficerEmail,
                            'requestId'                         =>  $generatedUUID,
                        ]);
                        Log::debug("Nodal Officer Data Inserted to DB. Nodal Officer : " . $createdFacilityNodalOfficer->id . ' - ' . ($nodalOfficerName));
                    }

                    /// Save Data to the Facility Infrastructure
                    if($general_beds_with_o2 !== null){
                        $generatedUUID                  = Str::uuid();          /// New UUID being Generated
                        $createdFacilityInfrastructure  =    FacilityInfrastructure::create([
                            'facility_information_id'           =>  $createdFacilityInformation->id,
                            'general_beds_with_o2'              =>  $general_beds_with_o2 ? $general_beds_with_o2 : 0,
                            'hdu_beds'                          =>  $hdu_beds ? $hdu_beds : 0,
                            'icu_beds'                          =>  $icu_beds ? $icu_beds : 0,
                            'o2_concentrators'                  =>  $o2_concentrators ? $o2_concentrators : 0,
                            'ventilators'                       =>  $ventilators ? $ventilators : 0,
                            'requestId'                         =>  $generatedUUID,
                        ]);
                        Log::debug("Facility Infrastructure Data Inserted to DB. Facility Infra ID : " . $createdFacilityInfrastructure->id);
                    }
                }
                else if($tempFacilityName !== null && isset($listOfFacilities[$count][1]) == true && isset($listOfFacilities[$count][19]) == true){
                    Log::debug('Processing for update: ' . $tempFacilityName);
                    //dd('In update');
                    $facilityToUpdate                =  $tempFacilityName;
                    $facilityToUpdate->update($facilityParamsForDB);
                    /// Update Facility Nodal Officer Data
                    if($facilityToUpdate->FacilityNodalOfficer->id !==null){
                        //dd(FacilityNodalOfficer::find($facilityToUpdate->FacilityNodalOfficer->id));
                        $generatedUUID                  = Str::uuid();

                        $nodalOfficerToUpdate           = FacilityNodalOfficer::find($facilityToUpdate->FacilityNodalOfficer->id)->update([
                            'officer_name'                      =>  $nodalOfficerName,
                            'officer_salutation'                =>  $nodalOfficerSalutation,
                            'officer_first_name'                =>  $nodalOfficerFirstName,
                            'officer_middle_name'               =>  $nodalOfficerMiddleName,
                            'officer_last_name'                 =>  $nodalOfficerLastName,
                            'officer_designation'               =>  $nodalOfficerDesignation,
                            'officer_country_code'              =>  $nodalOfficerCountryCode,
                            'officer_mobile_number'             =>  $nodalOfficerMobileNumber,
                            'officer_email'                     =>  $nodalOfficerEmail,
                            'requestId'                         =>  $generatedUUID,
                        ]);
                        Log::debug("Nodal Officer Data Updated in DB. Nodal Officer : " . $facilityToUpdate->FacilityNodalOfficer->id . ' - ' . ($nodalOfficerName));
                    }

                    /// Save Data to the Facility Infrastructure
                    if($general_beds_with_o2 !== null && $facilityToUpdate->FacilityInfrastructure->id !=null ){
                        $generatedUUID                  = Str::uuid();          /// New UUID being Generated

                        $facilityInfraToUpdate  =    FacilityInfrastructure::find($facilityToUpdate->FacilityInfrastructure->id)->update([
                            'general_beds_with_o2'              =>  $general_beds_with_o2 ? $general_beds_with_o2 : 0,
                            'hdu_beds'                          =>  $hdu_beds ? $hdu_beds : 0,
                            'icu_beds'                          =>  $icu_beds ? $icu_beds : 0,
                            'o2_concentrators'                  =>  $o2_concentrators ? $o2_concentrators : 0,
                            'ventilators'                       =>  $ventilators ? $ventilators : 0,
                            'requestId'                         =>  $generatedUUID,
                        ]);
                        Log::debug("Facility Infrastructure Data Updated in DB. Facility Infra ID : " . $facilityToUpdate->FacilityInfrastructure->id);
                    }

                    Log::debug("Facility Information Data Updated in DB. Facility Name: " . $tempFacilityName->id . ' - ' . $facilityName);
                }

                DB::commit();
            }
            //DB::commit();
            Log::debug("Facility Information Fetched!");
            return redirect()->back()->with('success', 'Facility Information Fetched!');
        }

        catch(\GuzzleHttp\Exception\ClientException $ex){
            DB::rollback();
            Log::error($ex->getResponse()->getBody()->getContents());
            return redirect()->back()->withErrors($ex->getMessage())->withInput();
        }
    }

    public function GenerateFacilityId($hospitalName){
        Log::debug('\n -------------------------------------------------------');
        Log::debug("Attempting to generate FacilityId for: " . $hospitalName);
        $response = null;
        try{
            $odasApiBAseURL                     =   config('odas.odas_base_url');
            $updateFacilityIDEndpointURI        =   'v1.0/odas/update-facility-info';
            $facilityBeingProcessed             =   Facility::where('facility_name',$hospitalName)->latest()->first();

            $newToken                           =   getODASAccessToken();

            // Save the authToken to the DB
            $odasToken                =   new ODASToken();
            $odasToken->token         =   $newToken;
            $odasToken->timestamp_utc =   Carbon::now()->toJSON();
            $odasToken->save();
            //dd('success');
            Log::debug("API Auth Token Generated!!");

            /// Update FacilityInfo
            $odasTokenToUse           =     $odasToken->token;
            $params = array(
                'facility' => [
                    'address' => [
                        "addressLine1"              => $facilityBeingProcessed->address_line_1,
                        "addressLine2"              => $facilityBeingProcessed->address_line_2,
                        "city"                      => $facilityBeingProcessed->city_lgd_code,
                        "district"                  => $facilityBeingProcessed->district_lgd_code,
                        "pincode"                   => $facilityBeingProcessed->pincode,
                        "state"                     => $facilityBeingProcessed->state_lgd_code,
                        "subdistrict"               => $facilityBeingProcessed->subdistrict_lgd_code
                    ],
                    "facilitysubtype"               => 0,
                    "facilitytype"                  => $facilityBeingProcessed->facility_type_code,
                    "id"                            =>  " ",
                    "langitude"                     => $facilityBeingProcessed->longitude,
                    "latitude"                      => $facilityBeingProcessed->latitude,
                    "name"                          => $facilityBeingProcessed->facility_name,
                    "ownershipsubtype"              => $facilityBeingProcessed->ownership_subtype,
                    "ownershiptype"                 => $facilityBeingProcessed->ownership_type
                ],
                "nodalcontacts" => [
                    [
                        "countrycode"       => $facilityBeingProcessed->FacilityNodalOfficer->officer_country_code ? $facilityBeingProcessed->FacilityNodalOfficer->officer_country_code : "+91",
                        "designation"       => $facilityBeingProcessed->FacilityNodalOfficer->officer_designation,
                        "email"             => $facilityBeingProcessed->FacilityNodalOfficer->officer_email,
                        "firstname"         => $facilityBeingProcessed->FacilityNodalOfficer->officer_first_name,
                        "lastname"          => $facilityBeingProcessed->FacilityNodalOfficer->officer_last_name,
                        "middlename"        => $facilityBeingProcessed->FacilityNodalOfficer->officer_middle_name,
                        "mobilenumber"      => $facilityBeingProcessed->FacilityNodalOfficer->officer_mobile_number,
                        "salutation"        => $facilityBeingProcessed->FacilityNodalOfficer->officer_salutation
                    ]
                ],
                "requestId" => $facilityBeingProcessed->requestId,
                "timestamp" => $odasToken->timestamp_utc
             );

            //dd($params);

            $client = new Client();
            Log::debug("Attempting to push data to API: " . $odasApiBAseURL.$updateFacilityIDEndpointURI);
            $response = $client->post($odasApiBAseURL.$updateFacilityIDEndpointURI, [
                'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json','Authorization'=>'Bearer ' .$odasTokenToUse,],
                'body'    => json_encode($params)
            ]);

            $dataRes =   json_decode($response->getBody(), true);


            if($dataRes !== null){
                /// Save the reference_number, facilityId and status in the local DB
                $facilityToUpdate                           =   Facility::find($facilityBeingProcessed->id);
                $facilityToUpdate->odas_facility_id         =   $dataRes['odasfacilityid'] ? $dataRes['odasfacilityid'] : 'No Facility Id Received';
                $facilityToUpdate->odas_reference_number    =   $dataRes['referencenumber'] ? $dataRes['referencenumber'] : 'No Reference Number';
                $facilityToUpdate->status                   =   $dataRes['status'] ? $dataRes['status'] : 'No Status Number';
                $facilityToUpdate->save();
                //dd($dataRes['referencenumber'] . " : " . $dataRes['odasfacilityid'] . " : " . $dataRes['status']);

                Log::debug('----------------------------------------');
                Log::debug('Facility Id Fetched and Updated in Database Successfully!' . ' - ' .$dataRes['odasfacilityid']);
                /// Update Facility Infrastructure Data
                return redirect()->back()->with('success', 'Facility Id Fetched and Updated in Database Successfully! Please update the same in the MasterSheet.');
            }
        }
        catch(\GuzzleHttp\Exception\ClientException $ex){
            //dd($ex->getResponse()->getBody()->getContents());
            Log::error($ex->getResponse()->getBody()->getContents());
            return redirect()->back()->with('error', $ex->getMessage());
        }
    }

    public function UpdateFacilityInfrastructure($facilityName){
        Log::debug('Processing ' . $facilityName);
        try{
            $odasApiBAseURL                     =   config('odas.odas_base_url');
            $updateFacilityBedInfoEndpointURI   =   'v1.0/odas/update-facility-bed-info';

            $facilityToUpdate                   =   Facility::where('facility_name',$facilityName)->first();

            $facilityInfra                      =   $facilityToUpdate->FacilityInfrastructure;

            $newToken                           =   getODASAccessToken();

            // Save the authToken to the DB
            $odasToken                =   new ODASToken();
            $odasToken->token         =   $newToken;
            $odasToken->timestamp_utc =   Carbon::now()->toJSON();
            $odasToken->save();

            /// Update Facility Bed Info
            $odasTokenToUse           =     $odasToken->token;
            $params = array(
                'beds' => [
                    "no_gen_beds"                   => $facilityInfra->general_beds_with_o2,
                    "no_hdu_beds"                   => $facilityInfra->hdu_beds,
                    "no_icu_beds"                   => $facilityInfra->icu_beds,
                    "no_o2_concentrators"           => $facilityInfra->o2_concentrators,
                    "no_vent_beds"                  => $facilityInfra->ventilators,
                ],
                "facilityid"    => $facilityToUpdate->odas_facility_id,
                "requestId"     => $facilityInfra->requestId,
                "timestamp"     => $odasToken->timestamp_utc
            );

            //dd($params);
            Log::debug('Data to Facility Infrastructure API ');

            $client = new Client();
            $response = $client->post($odasApiBAseURL.$updateFacilityBedInfoEndpointURI, [
                'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json','Authorization'=>'Bearer ' .$odasTokenToUse,],
                'body'    => json_encode($params)
            ]);
            //dd($response);
            $dataRes =   json_decode($response->getBody(), true);

            if($dataRes !== null){
                /// Save the reference_number, facilityId and status in the local DB
                $facilityInfrastructureToUpdate                           =   FacilityInfrastructure::find($facilityInfra->id);
                $facilityInfrastructureToUpdate->odas_reference_number    =   $dataRes['referencenumber'] ? $dataRes['referencenumber'] : 'No Reference Number';
                $facilityInfrastructureToUpdate->status                   =   $dataRes['status'] ? $dataRes['status'] : 'No Status Number';
                $facilityInfrastructureToUpdate->save();

                //dd($dataRes['referencenumber'] . " : " . $dataRes['status']);
                Log::debug($dataRes['status']);
                return redirect()->back()->with('success', 'Facility Infrastructure Updated Successfully!');
            }
        }
        catch(\GuzzleHttp\Exception\ClientException $ex){
            //dd($ex->getMessage());
            //Log::error('Exception while pushing Facility Infra bed data- ' .$ex->getMessage());
            Log::error('Exception while pushing Facility Infra bed data- ' . $ex->getResponse()->getBody()->getContents());
            return redirect()->back()->with('error', $ex->getMessage());
        }
    }
}
