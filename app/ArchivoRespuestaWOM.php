<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ArchivoRespuestaWOM extends Model {
    public $table = 'archivos_respuesta_wom';
    // llave primaria
    public $primaryKey = 'idArchivoRespuestaWOM';
    // este modelo tiene timestamps
    public $timestamps = true;
    // campos asignables
    protected $fillable = [ 'idSubidoPor', 'idAuditoria', 'nombreArchivo', 'nombreOriginal', 'archivoValido', 'resultado' ];

    // #### Relaciones
    function subidoPor(){
        return $this->hasOne('App\User', 'id', 'idSubidoPor');
    }
    function capturas(){
        return $this->hasMany('App\CapturaRespuestaWOM','idArchivoRespuestaWOM','idArchivoRespuestaWOM');
    }

    // #### Helpers
    function tieneBarra($sku, $serie){
        // existe en el sku?
        if($sku!==null) {
            $res = DB::table('capturas_respuesta_wom')
                ->select('sku')
                ->where('idArchivoRespuestaWOM', $this->idArchivoRespuestaWOM)
                ->where('sku', $sku)
                ->get();
            if(sizeof($res)>0)
                return true;
        }
        // existe en el serie?
        if($serie!==null) {
            $res = DB::table('capturas_respuesta_wom')
                ->select('sku')
                ->where('idArchivoRespuestaWOM', $this->idArchivoRespuestaWOM)
                ->where('serie', $serie)
                ->get();
            if(sizeof($res)>0)
                return true;
        }
        return false;
    }
    // #### Acciones
    // #### Getters
    static function getPathCarpeta($nombreCliente){
        return public_path()."/$nombreCliente/archivos-respuesta/";
    }
    function getFullPath(){
        $cliente  = Clientes::find(9)->nombreCorto;
        return self::getPathCarpeta($cliente).$this->nombreArchivo;
    }
    function getFullPath2(){
        $cliente  = Clientes::find(9)->nombreCorto;
        return self::getPathCarpeta($cliente).$this->nombreArchivoConteo2;
    }
    function getOrganizacionDesdeNombreArchivo(){
        $nombreArchivo = $this->nombreOriginal;
        // quitar extension
        $nombreArchivo = explode('.', $nombreArchivo)[0];
        $normalizado = str_replace(' ', '_', $nombreArchivo);
        $array_palabras = array_reverse( explode('_', $normalizado));
        return $array_palabras[0];
    }
    function getUnidadesNuevo(){
        $capturas = CapturaRespuestaWOM::buscar((object)[
            'idArchivo' => $this->idArchivoRespuestaWOM,
            'estado'   => "NUEVO"
        ]);
        return $capturas->count();
    }
    function getUnidadesUsado(){
        $capturas = CapturaRespuestaWOM::buscar((object)[
            'idArchivo' => $this->idArchivoRespuestaWOM,
            'estado'   => "USADO"
        ]);
        return $capturas->count();
    }
    function getUnidadesEnPrestamo(){
        $capturas = CapturaRespuestaWOM::buscar((object)[
            'idArchivo' => $this->idArchivoRespuestaWOM,
            'estado'   => "PRESTAMO"
        ]);
        return $capturas->count();
    }
    function getUnidadesServicioTecnico(){
        $capturas = CapturaRespuestaWOM::buscar((object)[
            'idArchivo' => $this->idArchivoRespuestaWOM,
            'estado'   => 'SERV. TECN'
        ]);
        return $capturas->count();
    }
    function getUnidadesTotal(){
        $capturas = CapturaRespuestaWOM::buscar((object)[
            'idArchivo' => $this->idArchivoRespuestaWOM,
        ]);
        return $capturas->count();
    }
    function getPatentesTotal(){
        $res = DB::table('capturas_respuesta_wom')
            ->select(DB::raw('count(distinct(ptt)) as total'))
            ->where('idArchivoRespuestaWOM', $this->idArchivoRespuestaWOM)
            ->get();
        return $res[0]->total;
    }
    function getPrimeraCaptura(){
        $res = DB::table('capturas_respuesta_wom')
            ->select('horaCaptura')
            ->where('idArchivoRespuestaWOM', $this->idArchivoRespuestaWOM)
            ->orderBy('fechaCaptura', 'ASC')
            ->orderBy('horaCaptura', 'ASC')
            ->limit(1)
            ->get();
        return $res[0]->horaCaptura;
    }
    function getUltimaCaptura(){

        $res = DB::table('capturas_respuesta_wom')
            ->select('horaCaptura')
            ->where('idArchivoRespuestaWOM', $this->idArchivoRespuestaWOM)
            ->orderBy('fechaCaptura', 'DESC')
            ->orderBy('horaCaptura', 'DESC')
            ->limit(1)
            ->get();
        return $res[0]->horaCaptura;
    }

    // #### Setters
    function setResultado($mensaje, $archivoValido){
        $this->resultado = $mensaje;
        $this->archivoValido = $archivoValido;
        $this->save();
    }

    function agregarOActualizarRegistros($registros){
        // eliminar registros anteriores
        // agregar los nuevos registros
    }

    // #### Formatear respuestas
    // #### Scopes para hacer Querys/Busquedas
    // #### Buscar / Filtrar Nominas
}
