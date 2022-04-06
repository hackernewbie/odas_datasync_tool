<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FacilityNodalOfficer;
use App\Services\GoogleSheetService;
use App\Models\FacilityInfrastructure;

class FacilityController extends Controller
{
    public function facilities(){
        $allFacilities      = Facility::all();

        return view('facilities')
                ->with('allFacilities',$allFacilities);
    }

    public function GetFacilities(){
        $gsheet = new GoogleSheetService();

        $listOfFacilities   =   $gsheet->readGoogleSheet(config('google.facility_sheet_name'));

        if($listOfFacilities == null || count($listOfFacilities) <= 2){
            return redirect()->back()->with('error','No Data in source Google Sheet Found in Source Google Scheet');
        }
        // dd(isset($listOfFacilities[9][8]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[9][8]);
        // dd($listOfFacilities[9][8] !== "" ? $listOfFacilities[9][8] : 'No Data in source Google Sheet');
        try{
            DB::beginTransaction();
            /// First Row is info header. Second Row is table Header
            for($count = 2; $count <= count($listOfFacilities)-1; $count++){
                if(isset($listOfFacilities[$count][16]) == true){
                    $facilityName                   = $listOfFacilities[$count][0];
                    $odas_facility_id               = null;
                    $address_line1                  = isset($listOfFacilities[$count][1]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[$count][1];
                    $address_line2                  = isset($listOfFacilities[$count][2]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[$count][2];
                    $city                           = isset($listOfFacilities[$count][3]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[$count][3];
                    $district_lgd                   = isset($listOfFacilities[$count][5]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[$count][5];
                    $pincode                        = isset($listOfFacilities[$count][6]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[$count][6];
                    $state_lgd                      = isset($listOfFacilities[$count][7]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[$count][7];
                    $sub_district_lgd               = isset($listOfFacilities[$count][8]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[$count][8];
                    $ownership_type                 = isset($listOfFacilities[$count][9]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[$count][9];
                    $ownership_sub_type             = isset($listOfFacilities[$count][10]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[$count][10];
                    $facility_type                  = isset($listOfFacilities[$count][11]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[$count][11];
                    $facility_type_code             = isset($listOfFacilities[$count][12]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[$count][12];
                    $longitude                      = isset($listOfFacilities[$count][14]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[$count][14];
                    $longitude                      = isset($listOfFacilities[$count][15]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[$count][15];

                    $nodalOfficerName               = isset($listOfFacilities[$count][16]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[$count][16];
                    $nodalOfficerDesignation        = isset($listOfFacilities[$count][17]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[$count][17];
                    $nodalOfficerSalutation         = isset($listOfFacilities[$count][18]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[$count][18];
                    $nodalOfficerCountryCode        = isset($listOfFacilities[$count][19]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[$count][19];
                    $nodalOfficerMobileNumber       = isset($listOfFacilities[$count][20]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[$count][20];
                    $nodalOfficerEmail              = isset($listOfFacilities[$count][21]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[$count][21];

                    $general_beds_with_o2           = isset($listOfFacilities[$count][22]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[$count][22];
                    $hdu_beds                       = isset($listOfFacilities[$count][23]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[$count][23];
                    $icu_beds                       = isset($listOfFacilities[$count][24]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[$count][24];
                    $o2_concentrators               = isset($listOfFacilities[$count][25]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[$count][25];
                    $ventilators                    = isset($listOfFacilities[$count][26]) == false ?  'No Data in source Google Sheet' : $listOfFacilities[$count][26];

                    /// Save Data to the Facility Table
                    $createdFacilityInformation     =   Facility::create([
                        'facility_name'                     =>  $facilityName,
                        'address_line_1'                    =>  $address_line1,
                        'address_line_2'                    =>  $address_line2,
                        'city_lgd_code'                     =>  $city,
                        'district_lgd_code'                 =>  $district_lgd,
                        'pincode'                           =>  $pincode,
                        'state_lgd_code'                    =>  $state_lgd,
                        'subdistrict_lgd_code'              =>  $sub_district_lgd,
                        'ownership_type'                    =>  $ownership_type,
                        'ownership_subtype'                 =>  $ownership_sub_type,
                        'facility_type'                     =>  $facility_type,
                        'facility_type_code'                =>  $facility_type_code,
                        'longitude'                         =>  $longitude,
                        'latitude'                          =>  $longitude,
                    ]);

                    /// Save Data to the Facility Nodal Officer
                    if(isset($nodalOfficerName) && $nodalOfficerName != null){
                        $createdFacilityNodalOfficer   =    FacilityNodalOfficer::create([
                            'facility_information_id'           =>  $createdFacilityInformation->id,
                            'officer_name'                      =>  $nodalOfficerName,
                            'officer_designation'               =>  $nodalOfficerDesignation,
                            'officer_salutation'                =>  $nodalOfficerSalutation,
                            'officer_country_code'              =>  $nodalOfficerCountryCode,
                            'officer_mobile_number'             =>  $nodalOfficerMobileNumber,
                            'officer_email'                     =>  $nodalOfficerEmail,
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
}
