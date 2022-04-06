<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class BackendController extends Controller
{
    public function dashboard(){
        //Log::userDailyFiles(storage_path().'/logs/ODAS_Logs/ODAS.log');
        Log::info('Dasboard opened by - ' . auth()->user()->name);
        Log::error('Dasboard opened by - ' . auth()->user()->name);
        return view('dashboard');
    }
}
