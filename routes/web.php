<?php

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
});
// Route::get('/', [App\Http\Controllers\HomeController::class, 'root'])->name('root');
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//Update User Details
Route::post('/update-profile/{id}', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('updateProfile');
Route::post('/update-password/{id}', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('updatePassword');


Route::get('data',function(GoogleSheetService $gsheet){
    $gsheet->readGoogleSheet(config('google.sheet_name'));
});




Route::get('facilities-get',[App\Http\Controllers\FacilityController::class, 'GetFacilities'])->name('facilities.get');         /// Fetch data from Google Sheet
Route::get('facilityid-get/{hospital_name}',[App\Http\Controllers\FacilityController::class, 'GenerateFacilityId'])->name('odas.facilityid.get');         /// Fetch data from Google Sheet

//Language Translation
Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);

Route::get('date-check', function(){
    $dateToCheck        =   ODASToken::latest()->first();
    dd($dateToCheck ? $dateToCheck->updated_at : 'null');
    //dd($dateToCheck);
});
