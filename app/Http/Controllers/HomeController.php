<?php

namespace App\Http\Controllers;
use App\Nominas;
use Doctrine\DBAL\Exception\InvalidFieldNameException;
use Illuminate\Support\Facades\App;
use Auth;
// Carbon
use Carbon\Carbon;
// Models
use App\User;
use App\ActasInventariosFCV;
use App\DiasHabiles;
use App\Inventarios;
use App\Auditorias;

class HomeController extends Controller {

    public function index() {
        setlocale(LC_TIME, 'es_CL.utf-8');
        $user = Auth::user();

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

            // Dashboard "Mis nominas por completar"
            $captador = User::find(658);
            $mostrar_misNominasAsignadasCaptador = $captador->hasRole('Captador');
            $nominasCaptador = Nominas::buscar((object)[
                'idCaptador' => $captador->id
            ]);

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

            // Dashboard "Estado general de auditorias FCV
            $mostrar_estadoGeneralAuditorias_fcv = $user->can('fcv-verEstadoGeneralAuditorias');
            $estadoGeneralAuditorias_fcv = Auditorias::estadoGeneralCliente(2);

            return view('home.index-usuario',[
                'usuario' => $user,

                // panel "mis proximos inventarios"
                'mostrar_misProximosInventarios' => $mostrar_misProximosInventarios,
                'nominas' => $proximasNominas,
                'misNominasDesde' => Carbon::parse($misNominas_desde)->formatLocalized('%A %e de %B'),
                'misNominasHasta' => Carbon::parse($misNominas_hasta)->formatLocalized('%A %e de %B'),

                // panel "mis nominas asignadas captador"
                'mostrar_misNominasAsignadasCaptador' => $mostrar_misNominasAsignadasCaptador,
                'nominasCaptador' => $nominasCaptador,

                // panel "Indicadores de gestión de Inventarios"
                'mostrar_indicadoresDeInventarios' => $mostrar_indicadoresDeInventarios,
                'indicadoresGestion_desde' => Carbon::parse($indicadoresGestion_desde)->formatLocalized('%A %e de %B'),
                'indicadoresGestion_hasta' => Carbon::parse($indicadoresGestion_hasta)->formatLocalized('%A %e de %B'),
                'inventariosPeriodo' => $inventariosPeriodo,
                'totalIndicadores' => $totalIndicadores,

                // Dashboard "Estado general de auditorias FCV
                'mostrar_estadoGeneralAuditorias_fcv' => $mostrar_estadoGeneralAuditorias_fcv,
                'ega_dia' => $estadoGeneralAuditorias_fcv->dia,
                'ega_zonas' => $estadoGeneralAuditorias_fcv->zonas,
                'ega_totales' => $estadoGeneralAuditorias_fcv->totales,
            ]);
        }else{
            return view('home.index-invitado');
        }
    }
}
