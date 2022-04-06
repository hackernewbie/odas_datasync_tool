<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;


class BackendController extends Controller
{
    public function dashboard(){
        return view('dashboard');
    }
}
