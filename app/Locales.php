<?php

namespace App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Locales extends Model{
    // llave primaria
    public $primaryKey = 'idLocal';

    // este modelo tiene timestamps
    public $timestamps = true;

    // #### Relaciones
    public function cliente(){
        // belongsTo(modelo, this.fogeignKey, parent.otherKey)
        return $this->belongsTo('App\Clientes', 'idCliente', 'idCliente');
    }

    public function formatoLocal(){
        // belongsTo(modelo, this.fogeignKey, parent.otherKey)
        return $this->belongsTo('App\FormatoLocales', 'idFormatoLocal', 'idFormatoLocal');
    }

    public function direccion(){
        //return $this->hasOne('App\Model', 'foreign_key', 'local_key');
        return $this->hasOne('App\Direcciones', 'idLocal', 'idLocal');
    }
    
    public function inventarios(){
        //return $this->hasMany('App\Comment', 'foreign_key', 'local_key');
        return $this->hasMany('App\Inventarios', 'idLocal', 'idLocal');
    }

    public function jornada(){
        // belongsTo(modelo, this.fogeignKey, parent.otherKey)
        return $this->belongsTo('App\Jornadas', 'idJornadaSugerida', 'idJornada');
    }

    public function llegadaSugeridaLiderDia(){
        // La hora de llegada sugerida para el lider corresponde a 1 hora y media antes de la apertura de local
        if($this->horaApertura=='00:00:00')
            return '00:00:00';
        else
            return date('H:i:s', strtotime($this->horaApertura)-5400); // 5400 = 90min * 60seg
    }
    public function llegadaSugeridaLiderNoche(){
        // La hora de llegada sugerida para el lider corresponde a 1 hora y media antes del cierre de local
        if($this->horaCierre=='00:00:00')
            return '00:00:00';
        else
            return date('H:i:s', strtotime($this->horaCierre)-5400); // 5400 = 90min * 60seg
    }

    public function llegadaSugeridaPersonalDia(){
        // La hora de llegada sugerida para el lider corresponde a 1 hora y media antes de la apertura del local
        if($this->horaApertura=='00:00:00')
            return '00:00:00';
        else
            return date('H:i:s', strtotime($this->horaApertura)-3600); // 3600 = 60min * 60seg
    }
    public function llegadaSugeridaPersonalNoche(){
        // La hora de llegada sugerida para el lider corresponde a 1 hora y media antes del cierre de local
        if($this->horaCierre=='00:00:00')
            return '00:00:00';
        else
            return date('H:i:s', strtotime($this->horaCierre)-3600); // 3600 = 60min * 60seg
    }

    public function dotacionSugerida(){
        // El ultimo stock actualizado / Produccion del tipo de local
        $producion = $this->formatoLocal->produccionSugerida;
        $stock = $this->stock;

        return round($stock/$producion);
    }

//    public function ultimoInventario(){
    public function inventarioRealizadoEn($annoMesDia){
        $fecha = explode('-', $annoMesDia);
        return Inventarios::where('idLocal', '=', $this->idLocal)
            ->whereRaw("extract(year from fechaProgramada) = ?", [$fecha[0]])
            ->whereRaw("extract(month from fechaProgramada) = ?", [$fecha[1]])
            ->first();
    }

    // #### Consultas
    public function horaAperturaF(){
        // Ejemplo: convertir "21:30:00" -> "21:30 hrs."
        $carbon = Carbon::parse($this->horaApertura);
        $minutes = $carbon->minute < 10? "0$carbon->minute" : $carbon->minute;
        return "$carbon->hour:$minutes hrs.";
    }
    public function horaCierreF(){
        // Ejemplo: convertir "21:30:00" -> "21:30 hrs."
        $carbon = Carbon::parse($this->horaCierre);
        $minutes = $carbon->minute < 10? "0$carbon->minute" : $carbon->minute;
        return "$carbon->hour:$minutes hrs.";
    }


    // #### Formatear
    static function formatearSimple($local){
        return [
            'idLocal' => $local->idLocal,
            'nombre' => $local->nombre,
            'numero' => $local->numero,
            'idLocal' => $local->idLocal,
            // stock
            'stock' => $local->stock,
            'fechaStock' => $local->fechaStock,
            // apertura
            'horaApertura' => $local->horaApertura,
            'horaAperturaF' => $local->horaAperturaF(),
            'horaCierre' => $local->horaCierre,
            'horaCierreF' => $local->horaCierreF(),
            // contacto
            'emailContacto' => $local->emailContacto,
            'telefono1' => "$local->codArea1 $local->telefono1",
            'telefono2' => "$local->codArea2 $local->telefono2",
        ];
    }

    static function formatearConClienteFormatoDireccionRegion($local){
        $localArray = Locales::formatearSimple($local);
        $localArray['cliente'] = Clientes::formatearSimple($local->cliente);
        // Formato de local
        $localArray['idFormatoLocal'] = $local->idFormatoLocal;
        $localArray['formatoLocal_nombre'] = $local->formatoLocal->nombre;
        $localArray['formatoLocal_produccionSugerida'] = $local->formatoLocal->produccionSugerida;

        // direcion comuna provincia region
        $localArray['direccion'] = $local->direccion->direccion;
        // Comuna
        $localArray['cutComuna'] = $local->direccion->comuna->cutComuna;
        $localArray['comuna_nombre'] = $local->direccion->comuna->nombre;
        // Region
        $localArray['cutRegion'] = $local->direccion->comuna->provincia->region->cutRegion;
        $localArray['region_numero'] = $local->direccion->comuna->provincia->region->numero;

        return $localArray;
    }
}
