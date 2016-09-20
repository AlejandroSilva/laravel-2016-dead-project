<?php

namespace App\Http\Controllers;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Auth;
// Carbon
use Carbon\Carbon;
// Models
use App\user;
use App\Nominas;

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

            // buscar los inventarios desde hoy hasta el domingo
            $fechaActual = Carbon::now();
            $terminoSemana = new Carbon('next sunday');
            $annoMesDiaInicio = $fechaActual->format('Y-m-d');
            $annoMesDiaFin = $terminoSemana->format('Y-m-d');

            $user11 = User::find(11);
            $nominasTitular = $user11->nominasComoTitular($annoMesDiaInicio, $annoMesDiaFin );

            return view('home.dashboard',[
                'user' => User::formatearMinimo($user),
                'perms' => collect($perms)->unique(),
                'fechaHoy' => \Carbon\Carbon::now()->format("Y-m-d"),
                // "mis nominas"
                'nominas' => $nominasTitular->todas
            ]);
        }else{
            return view('home.landing');
        }
    }
}
