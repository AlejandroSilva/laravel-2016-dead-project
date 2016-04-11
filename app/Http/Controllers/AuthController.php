<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use Redirect;

class AuthController extends Controller {

    // GET auth/login
    function show_Login(){
        return view('auth.login');
    }

    // GET auth/logout
    function show_Logout(){
        // destruir la session
        Auth::logout();
        // volver a INICIO
        return Redirect::to('/');
    }
}
