<?php

namespace App\Http\Controllers;
use App\ActasInventariosFCV;
use App\DiasHabiles;
use App\Inventarios;
use Doctrine\DBAL\Exception\InvalidFieldNameException;
use Illuminate\Support\Facades\App;
use Auth;
// Carbon
use Carbon\Carbon;
// Models
use App\User;

class HomeController extends Controller {
//    public function __construct() {
//        $this->middleware('auth');
//    }

    public function index() {
        $user = Auth::user();
        $user11 = User::find(11);

        if($user){
            // buscar los inventarios desde hoy hasta el domingo
            $hoy = Carbon::now()->format('Y-m-d');
            $esteDomingo = (new Carbon('next sunday'))->format('Y-m-d');

            // Dashboard "Mis proximos inventarios"
            $mostrar_misProximosInventarios = true;
            $proximasNominas = $mostrar_misProximosInventarios?
                $user11->nominasComoTitular($hoy, $esteDomingo)
                :
                [];

            // Dashboard "Indicadores de gestión de inventarios"
            $mostrar_indicadoresDeInventarios = true;
            $diaHabilHoy = DiasHabiles::find($hoy);
            $diaHabilAnterior = $diaHabilHoy->diasHabilesAntes(1)->fecha;
            $inventariosAyer = Inventarios::buscar((object)[
                'idCliente' => 2, // FCV
                'fechaInicio' => $diaHabilAnterior,
                'fechaFin' => $diaHabilAnterior
            ]);
            $totalIndicadores = ActasInventariosFCV::calcularTotales($inventariosAyer);

            //return response()->json($inventariosAyer[0]->actaFCV);
            return view('home.dashboard',[
                'hoy' => $diaHabilAnterior,
                'usuario' => User::formatearMinimo($user),
                // panel "mis proximos inventarios"
                'mostrar_misProximosInventarios' => $mostrar_misProximosInventarios,
                'nominas' => $proximasNominas->todas,
                // panel "Indicadores de gestión de Inventarios"
                'diaHabilAnterior' => $diaHabilAnterior,
                'mostrar_indicadoresDeInventarios' => $mostrar_indicadoresDeInventarios,
                'inventariosAyer' => $inventariosAyer,
                'totalIndicadores' => $totalIndicadores
            ]);
        }else{
            return view('home.landing');
        }
    }
}
