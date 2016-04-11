<?php

namespace App\Http\Controllers;
use App\Http\Requests;
use Illuminate\Http\Request;
use Auth;

class HomeController extends Controller {
//    public function __construct() {
//        $this->middleware('auth');
//    }

    public function index() {
        if(Auth::user()){
            return view('home.dashboard');
        }else{
            return view('home.landing');
        }
    }
}
