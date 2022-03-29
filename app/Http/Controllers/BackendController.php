<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BackendController extends Controller
{
    public function dashboard(){
        return view('dashboard');
    }

    public function facilities(){
        return view('facilities');
    }
}
