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
        setlocale(LC_TIME, 'es_CL.utf-8');

        $user = Auth::user();
        //$user11 = User::find(11);

        if($user){
            // buscar los inventarios desde hoy hasta el domingo
            $hoy = Carbon::now()->format('Y-m-d');
            $diaHabilHoy = DiasHabiles::find($hoy);

            // Dashboard "Mis proximos inventarios"
            $mostrar_misProximosInventarios = $user->hasRole('Lider') || $user->hasRole('Supervisor');
            $misNominas_desde = $diaHabilHoy->diasHabilesAntes(2)->fecha;
            $misNominas_hasta = $diaHabilHoy->diasHabilesDespues(3)->fecha;
            $proximasNominas = $mostrar_misProximosInventarios?
                $user->nominasComoTitular($misNominas_desde, $misNominas_hasta)->todas : [];

            // Dashboard "Indicadores de gestión de inventarios"
            $mostrar_indicadoresDeInventarios = true;
            $indicadoresGestion_desde = $diaHabilHoy->diasHabilesAntes(1)->fecha;
            $indicadoresGestion_hasta = $diaHabilHoy->fecha;
            $inventariosPeriodo = Inventarios::buscar((object)[
                'idCliente' => 2, // FCV
                'fechaInicio' => $indicadoresGestion_desde,
                'fechaFin' => $indicadoresGestion_hasta
            ]);
            $totalIndicadores = ActasInventariosFCV::calcularTotales($inventariosPeriodo);

            return view('home.dashboard',[
                'usuario' => $user,

                // panel "mis proximos inventarios"
                'mostrar_misProximosInventarios' => $mostrar_misProximosInventarios,
                'nominas' => $proximasNominas,
                'misNominasDesde' => Carbon::parse($misNominas_desde)->formatLocalized('%A %e de %B'),
                'misNominasHasta' => Carbon::parse($misNominas_hasta)->formatLocalized('%A %e de %B'),

                // panel "Indicadores de gestión de Inventarios"
                'mostrar_indicadoresDeInventarios' => $mostrar_indicadoresDeInventarios,
                'indicadoresGestion_desde' => Carbon::parse($indicadoresGestion_desde)->formatLocalized('%A %e de %B'),
                'indicadoresGestion_hasta' => Carbon::parse($indicadoresGestion_hasta)->formatLocalized('%A %e de %B'),
                'inventariosPeriodo' => $inventariosPeriodo,
                'totalIndicadores' => $totalIndicadores
            ]);
        }else{
            return view('home.landing');
        }
    }
}
