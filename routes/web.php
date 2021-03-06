<?php

use App\Models\Facility;
use App\Models\ODASToken;
use App\Services\GoogleSheetService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::middleware(['auth'])->group(function () {
    // Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index'])->name('index');
    Route::get('dashboard',[App\Http\Controllers\BackendController::class, 'dashboard'])->name('dashboard');
    Route::get('facilities',[App\Http\Controllers\FacilityController::class,'facilities'])->name('facilities');

    Route::get('facilities-get',[App\Http\Controllers\FacilityController::class, 'GetFacilities'])->name('facilities.get');         /// Fetch data from Google Sheet
    Route::get('facilityid-get/{hospital_name}',[App\Http\Controllers\FacilityController::class, 'GenerateFacilityId'])->name('odas.facilityid.get');         /// Fetch data from Google Sheet

    Route::get('facility-bed-info-update/{hospital_name}',[App\Http\Controllers\FacilityController::class,'UpdateFacilityInfrastructure'])->name('facility.bedinfo.update');

    Route::get('oxygen',[App\Http\Controllers\OxygenDataController::class,'Oxygen'])->name('oxygen');

    Route::get('FetchOxygenData',[App\Http\Controllers\OxygenDataController::class,'FetchOxygenData'])->name('fetch.oxygen.data');
    //Route::get('oxygen-data-get/{hospital_name}',[App\Http\Controllers\FacilityController::class, 'OxygenDataById'])->name('oxygen.data.hospital.get');
    Route::get('SendOxygenDataToAPI/{hospital_name}',[App\Http\Controllers\OxygenDataController::class,'UpdateOxygenDataByHospital'])->name('update.oxygen.data');
    Route::get('SendOxygenDemandDataToAPI/{odas_facility_id}',[App\Http\Controllers\OxygenDataController::class,'UpdateOxygenDemand'])->name('update.oxygen.demand');
    Route::get('SendBedOccupancyDataToAPI/{odas_facility_id}',[App\Http\Controllers\OxygenDataController::class,'UpdateFacilityBedOccupancyData'])->name('update.facility.bed.occupancy');
    Route::get('SendO2ConsumptionDataToAPI/{odas_facility_id}',[App\Http\Controllers\OxygenDataController::class,'UpdateFacilityO2ConsumptionData'])->name('update.facility.oxygen.consumption');


    /// Bulk Updates
    Route::get('BulkO2InfraPush',[App\Http\Controllers\BulkUpdatesController::class, 'BulkUpdateFacilityO2Infra'])->name('o2.onfra.bulk.update');

});
// Route::get('/', [App\Http\Controllers\HomeController::class, 'root'])->name('root');
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//Update User Details
Route::post('/update-profile/{id}', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('updateProfile');
Route::post('/update-password/{id}', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('updatePassword');


Route::get('data',function(GoogleSheetService $gsheet){
    $gsheet->readGoogleSheet(config('google.sheet_name'),'AL');
});


//Language Translation
Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);

Route::get('date-check', function(){
    $dateToCheck        =   ODASToken::latest()->first();
    dd($dateToCheck ? $dateToCheck->updated_at : 'null');
    //dd($dateToCheck);
});


