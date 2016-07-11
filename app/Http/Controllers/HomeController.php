<?php

namespace App\Http\Controllers;
use App\Http\Requests;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\App;
// Models
use App\user;

class HomeController extends Controller {
//    public function __construct() {
//        $this->middleware('auth');
//    }

    public function index() {
        $user = Auth::user();
        if($user){
            // buscar cada uno de los permisos que tiene el usuario
            $perms = [];
            foreach ($user->roles as $role) {
                foreach ($role->perms as $perm){
                    array_push($perms, $perm->name);
                }
            }
            return view('home.dashboard',[
                'user' => User::formatearMinimo($user),
                'perms' => collect($perms)->unique(),
                'fechaHoy' => \Carbon\Carbon::now()->format("Y-m-d"),
            ]);
        }else{
            return view('home.landing');
        }
    }
}
