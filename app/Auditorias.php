<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Auditorias extends Model {
    // llave primaria
    public $primaryKey = 'idAuditoria';
    // este modelo tiene timestamps
    public $timestamps = true;

    // #### Relaciones
    public function local(){
        //return $this->belongsTo('App\Model', 'foreign_key', 'other_key');
        return $this->belongsTo('App\Locales', 'idLocal', 'idLocal');
    }
    public function auditor(){
        //     $this->hasOne('App\Model', 'foreign_key', 'local_key');
        return $this->hasOne('App\User', 'id', 'idAuditor');
    }

    // #### Helpers
    function tieneTopeFechaConInventario(){
        // verifica si existe un inventario programado cerca de esta auditoria

        // si el dia no esta seleccionado, entonces no buscar el tope de fecha con otras auditorias
        $fecha = explode('-', $this->fechaProgramada);
        $dia = $fecha[2];
        if(!isset($dia) || $dia=='00')
            return null;

        // no se puede hacer un inventario 4 dias habiles antes de una auditoria, o el dia despues
        $fecha_3diasHabilesAntes = DiasHabiles::find($this->fechaProgramada)->diasHabilesAntes(3);
        $fecha_0diaHabilDespuse = DiasHabiles::find($this->fechaProgramada)->diasHabilesDespues(0);
        $inventariosCercanos = Inventarios::whereRaw("idLocal = $this->idLocal")
            ->whereRaw("fechaProgramada >= '$fecha_3diasHabilesAntes->fecha'")
            ->whereRaw("fechaProgramada <= '$fecha_0diaHabilDespuse->fecha'")
            ->get();

        if($inventariosCercanos->count()>0){
            $fechas = $inventariosCercanos->map(function($inventario){
                return $inventario->fechaProgramada;
            })->toArray();
            $fechas = implode(", ", $fechas);
            return "Inventario programado para el dia: $fechas ";
        } else
            return null;
    }
    // ####  Getters
    function fechaProgramadaF(){
        setlocale(LC_TIME, 'es_CL.utf-8');
        $fecha = explode('-', $this->fechaProgramada);
        $anno = $fecha[0];
        $mes  = $fecha[1];
        $dia  = $fecha[2];

        // si no esta seleccionado el dia, se muestra con otro formado
        if($dia==0){
            // fecha con formato: ejemplo: "2016-05-00" -> "mayo, 2016"
            // FIX: si se entrega 2016-02-01, el formato indica "enero, 2016, por eso se genera una fecha con dia "1"
            return Carbon::parse( "$anno-$mes-01")->formatLocalized('%B, %Y');
        }else{
            // fecha con formato: ejemplo: "2016-05-30" -> "lunes 30 de mayo, 2016"
            return Carbon::parse($this->fechaProgramada)->formatLocalized('%A %e de %B, %Y');
        }
    }
    function fechaProgramadaFbreve(){
        setlocale(LC_TIME, 'es_CL.utf-8');
        $fecha = explode('-', $this->fechaProgramada);
        $anno = $fecha[0];
        $mes  = $fecha[1];
        $dia  = $fecha[2];

        // si no esta seleccionado el dia, se muestra con otro formado
        if($dia==0){
            // fecha con formato: ejemplo: "2016-05-00" -> "mayo, 2016"
            // FIX: si se entrega 2016-02-01, el formato indica "enero, 2016, por eso se genera una fecha con dia "1"
            return Carbon::parse( "$anno-$mes-01")->formatLocalized('%B, %Y');
        }else{
            // fecha con formato: ejemplo: "2016-05-30" -> "lun 30 de may"
            return Carbon::parse($this->fechaProgramada)->formatLocalized('%a %e de %b');
        }
    }

    // ####  Setters
    //

    // #### Formatear respuestas
    // utilizado por: VistaGeneralController@api_vista
    static function formatear_vistaGeneral($auditoria){
        return (object) [
            'id' => $auditoria->idAuditoria,
            'fechaProgramada' => $auditoria->fechaProgramada,
            'idAuditor' => $auditoria->idAuditor,
            // Local
            'local' => $auditoria->local->numero,
            // Cliente
            'cliente' => $auditoria->local->cliente->nombreCorto,
            // Comuna
            'comuna' => $auditoria->local->direccion->comuna->nombre,
        ];
        // para optimizar, se puede guardar en "cache" la relacion auditoria->local->cliente->nombreCorto
    }

    static function formato_programacionIGSemanalMensual($auditoria){
        return [
            'aud_idAuditoria'=> $auditoria->idAuditoria,
            'aud_fechaProgramada'=> $auditoria->fechaProgramada,
            'aud_fechaProgramadaF'=> $auditoria->fechaProgramadaF(),
            'aud_fechaProgramadaFbreve'=> $auditoria->fechaProgramadaFbreve(),
            'aud_fechaProgramadaDOW' => DiasHabiles::diaDeLaSemana($auditoria->fechaProgramada),
            'aud_fechaAuditoria' => $auditoria->fechaAuditoria,
            'aud_aprobada' => $auditoria->aprovada,
            'aud_idAuditor' => $auditoria->idAuditor,
            'aud_auditor' => $auditoria->auditor? $auditoria->auditor->nombreCorto() : '--',

            'cliente_idCliente' => $auditoria->local->idCliente,
            'cliente_nombreCorto' => $auditoria->local->cliente->nombreCorto,

            'local_idLocal' => $auditoria->local->idLocal,
            'local_ceco' => $auditoria->local->numero,
            'local_nombre' => $auditoria->local->nombre,
            'local_comuna' => $auditoria->local->direccion->comuna->nombre,
            'local_cutComuna' => $auditoria->local->direccion->cutComuna,
            'local_region' => $auditoria->local->direccion->comuna->provincia->region->numero,
            'local_cutRegion' => $auditoria->local->direccion->comuna->provincia->cutRegion,
            'local_direccion' => $auditoria->local->direccion->direccion,
            'local_stock' => $auditoria->local->stock,
            'local_stockF' => $auditoria->local->stockF(),
            'local_fechaStock' => $auditoria->local->fechaStock,
            'local_horaApertura' => $auditoria->local->horaApertura,
            'local_horaCierre' => $auditoria->local->horaCierre,
            'local_topeFechaConInventario' => $auditoria->tieneTopeFechaConInventario(),
        ];
    }

    // #### Scopes para hacer Querys
    static function buscar($peticion){
        $query = Auditorias::with([]);

        // Cliente (solo si existe y es dintito a 0)
        $idCliente = $peticion->idCliente;
        if( isset($idCliente) && $idCliente!=0) {
            $query->whereHas('local', function ($q) use ($idCliente) {
                $q->where('idCliente', '=', $idCliente);
            });
        }

        // OPCIONAL: Fecha desde
        if(isset($peticion->fechaInicio))
            $query->where('fechaProgramada', '>=', $peticion->fechaInicio);

        // OPCIONAL: Fecha hasta
        if(isset($peticion->fechaFin))
            $query->where('fechaProgramada', '<=', $peticion->fechaFin);

        // OPCIONAL: Mes
        if(isset($peticion->mes)){
            $_fecha = explode('-', $peticion->mes);
            $anno = $_fecha[0];
            $mes  = $_fecha[1];
            $query
                ->whereRaw("extract(year from fechaProgramada) = ?", [$anno])
                ->whereRaw("extract(month from fechaProgramada) = ?", [$mes]);
        }

        // OPCIONAL: Incluir con "fecha pendiente" en el resultado, solo si se indica explicitamente
        $incluirConFechaPendiente = isset($peticion->incluirConFechaPendiente) && $peticion->incluirConFechaPendiente=='true';
        if( $incluirConFechaPendiente==false )
            $query->whereRaw("extract(day from fechaProgramada) != 0");

        // OPCIONAL: por Zona
        if(isset($peticion->idZona)) {
            $idZona = $peticion->idZona;
            $query->whereHas('local.direccion.comuna.provincia.region', function ($q) use ($idZona) {
                $q->where('idZona', '=', $idZona);
            });
        }

        // OPCIONAL: REALIZADA?
        if(isset($peticion->realizada)) {
            $query->where('realizadaInformada', $peticion->realizada? true : false);
        }

        $query->orderBy('fechaProgramada', 'asc');
        return $query->get();
    }

    public function scopeSoloFechasValidas($query){
        // si se selecciona un rango de dias, este podria llegar a incluir fechas sin el dia fijado, Ej: 2016-06-00
        // este query remove todas las fechas que no tengan el dia fijado
        $query->whereRaw("extract(day from fechaProgramada) != 0");
    }
    public function scopeFechaProgramadaEntre($query, $fechaInicio, $fechaFin){
        // al parecer funciona, hacer mas pruebas
        $query->where('fechaProgramada', '>=', $fechaInicio);
        $query->where('fechaProgramada', '<=', $fechaFin);
    }
}
