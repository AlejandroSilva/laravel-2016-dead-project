<?php

namespace App\Http\Controllers;
use App\Http\Requests;
use App\Inventarios;
use Doctrine\DBAL\Exception\InvalidFieldNameException;
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
        $user11 = User::find(11);

        if($user){
            // buscar los inventarios desde hoy hasta el domingo
            $hoy = Carbon::now();
            $esteDomingo = new Carbon('next sunday');

            // Dashboard "Mis proximos inventarios"
            $mostrar_misProximosInventarios = true;
            $proximasNominas = $mostrar_misProximosInventarios?
                $user11->nominasComoTitular($hoy->format('Y-m-d'), $esteDomingo->format('Y-m-d'))
                :
                [];

            // Dashboard "Indicadores de gestión de inventarios"
            $hoy = '2016-09-29';
            $mostrar_indicadoresDeInventarios = true;
            $inventariosAyer = Inventarios::buscar((object)[
                'idCliente' => 2, // FCV
                'fechaInicio' => $hoy,
                'fechaFin' => $hoy
            ]);

            //return response()->json($inventariosAyer[0]->actaFCV);
            return view('home.dashboard',[
                'hoy' => $hoy,
                'usuario' => User::formatearMinimo($user),
                // panel "mis proximos inventarios"
                'mostrar_misProximosInventarios' => $mostrar_misProximosInventarios,
                'nominas' => $proximasNominas->todas,
                // panel "Indicadores de gestión de Inventarios"
                'mostrar_indicadoresDeInventarios' => $mostrar_indicadoresDeInventarios,
                'inventariosAyer' => $inventariosAyer
            ]);
        }else{
            return view('home.landing');
        }
    }
}
