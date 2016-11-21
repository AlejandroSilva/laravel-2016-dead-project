<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class WOMOfflineController extends Controller {


    function index(){
        return view('wom-offline.index');
    }
}
