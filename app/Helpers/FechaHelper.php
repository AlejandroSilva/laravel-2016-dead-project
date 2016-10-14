<?php

class FechaHelper{

    static function ultimosMeses($fecha, $cantMeses, $incluirAnno){
        setlocale(LC_TIME, 'es_CL.utf-8');
        $mes = $fecha->copy()->startofMonth();

        $meses = [];
        for($i=0; $i<=$cantMeses; $i++){
            $meses[] = [
                'text'=>$mes->formatLocalized('%B, %Y'), 'value'=>$mes->toDateString(),  // YYYY-MM-DD
            ];
            $mes->subMonth();

            // si el mes es enero, incluir tambien el "año"
            if($mes->month==1)
                $meses[] = [
                    'text'=>$mes->formatLocalized($mes->year), 'value'=>$mes->format('Y-00-00'),
                ];
        }
        // si el ultimo registro en la lista de meses, ES es un mes, entonces incluir el año
        // si el ultimo registro es un año, no incluir el año
        $ultimoRegistro = end($meses)['text'];
        if(strpos($ultimoRegistro, ', ')!==false )
            $meses[] = [
                'text'=>$mes->formatLocalized($mes->year), 'value'=>$mes->format('Y-00-00'),
            ];
        return $meses;
    }

}