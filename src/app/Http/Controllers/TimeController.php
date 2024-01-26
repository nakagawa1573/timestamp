<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TimeController extends Controller
{
    public function atte(){
        return view('attendance');
    }
}
