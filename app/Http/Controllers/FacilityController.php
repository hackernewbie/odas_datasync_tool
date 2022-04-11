<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\Facility;
use App\Models\ODASToken;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\FacilityNodalOfficer;
use App\Services\GoogleSheetService;
use App\Models\FacilityInfrastructure;

class FacilityController extends Controller
{
    public function Oxygen(){
        $allInOxygenStatus  =   [];
        return view('oxygen_status')
            ->with('allInOxygenStatus',$allInOxygenStatus);
    }

    public function FetchOxygenData(){
        $gsheet             = new GoogleSheetService();

        $allOxygenData      = $gsheet->readGoogleSheet(config('google.data_for_dashboard'),'BI');
        dd($allOxygenData);

        //Insert into DB if data not already present else update data

    }

    public function GetOxygenDataByHospital($hospital){
        dd($hospital);
    }

    public function facilities(){
        //$allFacilities      = Facility::with('FacilityNodalOfficer')->get();
        try{
            Facility::all();
        }
        catch(\Exception $ex){
            if($ex->getCode() == '42S02'){
                return redirect('dashboard')->with('Error', "Base Table Missing!");
            }
        }
        $allFacilities      = Facility::all();
        //dd($allFacilities);
        return view('facilities')
                ->with('allFacilities',$allFacilities);
    }

    public function GetFacilities(){
        $gsheet = new GoogleSheetService();
        $listOfFacilities   =   $gsheet->readGoogleSheet(config('google.facility_sheet_name'),'AL');
        //dd($listOfFacilities);
        if($listOfFacilities == null || count($listOfFacilities) <= 2){
            return redirect()->back()->with('error','Empty Found in Source Google Scheet');
        }

        try{
            DB::beginTransaction();
            /// First Row is info header. Second Row is table Header
            for($count = 2; $count <= count($listOfFacilities)-1; $count++){
                $generatedUUID           = Str::uuid();
                $tempFacilityName        = Facility::where('facility_name',$listOfFacilities[$count][0])->first();

                if($tempFacilityName == null && isset($listOfFacilities[$count][1]) == true && isset($listOfFacilities[$count][19]) == true){
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

                    $general_beds_with_o2           = isset($listOfFacilities[$count][28]) == false ?  'Empty' : $listOfFacilities[$count][28];
                    $hdu_beds                       = isset($listOfFacilities[$count][29]) == false ?  'Empty' : $listOfFacilities[$count][29];
                    $icu_beds                       = isset($listOfFacilities[$count][30]) == false ?  'Empty' : $listOfFacilities[$count][30];
                    $o2_concentrators               = isset($listOfFacilities[$count][31]) == false ?  'Empty' : $listOfFacilities[$count][31];
                    $ventilators                    = isset($listOfFacilities[$count][32]) == false ?  'Empty' : $listOfFacilities[$count][32];

                    /// Save Data to the Facility Table
                    $createdFacilityInformation     =   Facility::create([
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
                    ]);

                    /// Save Data to the Facility Nodal Officer
                    if(isset($nodalOfficerName) && $nodalOfficerName != null){
                        $createdFacilityNodalOfficer   =    FacilityNodalOfficer::create([
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
                    }

                    /// Save Data to the Facility Infrastructure
                    if($general_beds_with_o2 !== null){
                        $createdFacilityInfrastructure =    FacilityInfrastructure::create([
                            'facility_information_id'           =>  $createdFacilityInformation->id,
                            'general_beds_with_o2'              =>  $general_beds_with_o2,
                            'hdu_beds'                          =>  $hdu_beds,
                            'icu_beds'                          =>  $icu_beds,
                            'o2_concentrators'                  =>  $o2_concentrators,
                            'ventilators'                       =>  $ventilators,
                            'requestId'                         =>  $generatedUUID,
                        ]);
                    }
                    else{
                        /// Skipping because no nodal officer name
                    }
                }
            }
            DB::commit();
            return redirect()->back()->with('success', 'Facility Information Fetched!');
        }

        catch(\Exception $ex){
            DB::rollback();
            return redirect()->back()->withErrors($ex->getMessage())->withInput();
        }
    }

    public function GenerateFacilityId($hospitalName){
        try{
            $odasApiBAseURL                     =   config('odas.odas_base_url');
            $updateFacilityIDEndpointURI        =   'v1.0/odas/update-facility-info';
            // $generatedUUID                      =   Str::uuid();

            $facilityBeingProcessed                   =   Facility::where('facility_name',$hospitalName)->first();
            //dd($facilityBeingProcessed);
            //dd($facilityBeingProcessed->FacilityNodalOfficer);
            //dd($facilityBeingProcessed->FacilityNodalOfficer->officer_salutation);

            $newToken                           =   getODASAccessToken();

            // Save the authToken to the DB
            $odasToken                =   new ODASToken();
            $odasToken->token         =   $newToken;
            $odasToken->timestamp_utc =   Carbon::now()->toJSON();
            $odasToken->save();
            //dd('success');

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

            $response = $client->post($odasApiBAseURL.$updateFacilityIDEndpointURI, [
                'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json','Authorization'=>'Bearer ' .$odasTokenToUse,],
                'body'    => json_encode($params)
            ]);
            $dataRes =   json_decode($response->getBody(), true);

            if($dataRes !== null){
                /// Save the reference_number, facilityId and status in the local DB
                $facilityToUpdate                       =   Facility::find($facilityBeingProcessed->id);
                $facilityToUpdate->odas_facility_id     =   $dataRes['odasfacilityid'] ? $dataRes['odasfacilityid'] : 'No Facility Id Received';
                $facilityToUpdate->reference_number     =   $dataRes['referencenumber'] ? $dataRes['referencenumber'] : 'No Reference Number';
                $facilityToUpdate->status               =   $dataRes['status'] ? $dataRes['status'] : 'No Status Number';
                $facilityToUpdate->save();
                //dd($dataRes['referencenumber'] . " : " . $dataRes['odasfacilityid'] . " : " . $dataRes['status']);

                /// Update Facility Infrastructure Data

                return redirect()->back()->with('success', 'Facility Id Fetched and Updated in Database Successfully! Please update the same in the MasterSheet.');
            }
        }
        catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage());
            //dd($ex->getMessage());
        }
    }
}
