<?php

namespace App;

use Carbon\Carbon;
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
    // #### Acciones
    function eliminar(){
        $this->capturas()->delete();
        $this->delete();
    }

    // #### Getters
    static function getPathCarpeta($nombreCliente){
        return public_path()."/$nombreCliente/archivos-respuesta/";
    }
    function getFullPath(){
        $cliente  = Clientes::find(9)->nombreCorto;
        return self::getPathCarpeta($cliente).$this->nombreArchivo;
    }
    function getPatentesTotal(){
        $res = DB::table('capturas_respuesta_wom')
            ->select(DB::raw('count(distinct(ptt)) as total'))
            ->where('idArchivoRespuestaWOM', $this->idArchivoRespuestaWOM)
            ->get();
        return $res[0]->total;
    }
    function getFechaF(){
        setlocale(LC_TIME, 'es_CL.utf-8');
        return Carbon::parse($this->created_at)->formatLocalized('%d de %B, %Y');
    }

    // #### Setters
    function setResultado($mensaje, $archivoValido){
        $this->resultado = $mensaje;
        $this->archivoValido = $archivoValido;
        $this->save();
    }

    // #### Formatear respuestas
    // #### Scopes para hacer Querys/Busquedas
    // #### Buscar / Filtrar Nominas
}
