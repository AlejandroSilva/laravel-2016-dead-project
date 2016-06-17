<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Carbon\Carbon;
// Modelos
use App\Nominas;
use App\Auditorias;

class VistaGeneralController extends Controller {


    public function api_obtenerNominasAuditorias(Request $request){
        $annoMesDia = $request->query('annoMesDia');
        if(!$annoMesDia)
            return response()->json([
                'query' => $annoMesDia,
                'totalAuditorias' => 0,
                'totalNominas' => 0,
                'nominas' => [],
                'auditorias' => [],
            ], 400);


//        $calendar = $this->build_calendar($annoMesDia);
//        $desde = $calendar->firstDayOfCalendar;
//        $hasta = $calendar->lastDayOfCalendar;
        $mes = Carbon::createFromFormat('Y-m-d', $annoMesDia);
        $desde = $mes->copy()->startOfMonth()->startOfWeek()->format('Y-m-d');
        $hasta = $mes->copy()->lastOfMonth()->endOfWeek()->format('Y-m-d');

        // buscar las nominas 'activas'
        $nominas = Nominas::with([])
            // ... query de nominas
            ->habilitada()
            ->fechaProgramadaEntre($desde, $hasta)
            ->get()
            // ... mapeo de la collection
            ->map('\App\Nominas::formatear_vistaGeneral')
            ->sortBy('fechaProgramada');

        $auditorias = Auditorias::with([])
            // query de las auditorias
            ->soloFechasValidas()
            ->fechaProgramadaEntre($desde, $hasta)
            ->get()
            // ... mapeo del collection
            ->map('\App\Auditorias::formatear_vistaGeneral')
            ->sortBy('fechaProgramada')
            ;
            
        return response()->json([
            'query' => $annoMesDia,
//            'calendar' => $calendar,
            'totalAuditorias' => $auditorias->count(),
            'totalNominas' => $nominas->count(),
            'nominas' => $nominas->values(),
            'auditorias' => $auditorias->values(),
        ], 200, [], JSON_NUMERIC_CHECK); // convertir los campos con numero como INT
    }

    private function build_calendar($annoMesDia){
        // buscar el primer dia de la semana (puede ser del mes anterior)
        $mes = Carbon::createFromFormat('Y-m-d', $annoMesDia);
        $primerDiaCalendario = $mes->copy()->startOfMonth()->startOfWeek();
        $ultimoDiaCalendario = $mes->copy()->lastOfMonth()->endOfWeek();

        // construir las semanas
        $day = $primerDiaCalendario->copy();
        $weeks = [];
        $week = ['weekNumber'=>$day->weekOfYear, 'days'=>[]];
        while($day->between($primerDiaCalendario, $ultimoDiaCalendario)){
            // agregar DAY a la WEEK
            array_push($week['days'], [
                '_day' => $day->copy(),// Objecto para hacer operacione
                'day' => $day->format('Y-m-d'),
                'isWeekend' => $day->dayOfWeek==6 || $day->dayOfWeek==0,
                'number' => $day->day,
                'sameMonth' => $day->month==$mes->month
            ]);

            // si es domingo, pasar a la siguiente semana
            if($day->dayOfWeek==0){
                array_push($weeks, $week);
                $week = ['weekNumber'=>$day->weekOfYear, 'days'=>[]];
            }

            $day->addDay();
        }
        return (object)[
            'firstDayOfCalendar' => $primerDiaCalendario->format('Y-m-d'),
            'lastDayOfCalendar' => $ultimoDiaCalendario->format('Y-m-d'),
            'weeks' => $weeks
        ];
    }
}
