<?php

namespace App;

// DB
use DB;
use Illuminate\Database\Eloquent\Model;

class MaestraFCV extends Model{
    public $table = 'maestra_fcv';
    // PK
    public $primaryKey = 'idMaestraFCV';
    public $timestamps = true;
    // Campos asignables
    protected $fillable = ['idArchivoMaestra',  'barra', 'descriptor', 'sku', 'laboratorio', 'clasificacionTerapeutica'];
    //Relaciones
    public function archivoMaestraFCV(){
        return $this->belongsTo('App\ArchivoMaestraFCV', 'idArchivoMaestra', 'idArchivoMaestra');
    }
    static function skuDuplicados(){

        $skuDuplicados = MaestraFCV::whereIn('sku', function ( $query ) {
            $query->select('sku')->from('maestra_fcv')->groupBy('sku')->havingRaw('count(*) > 1');
        })->get();

        return $skuDuplicados;
    }
    static function dumpMaestra(){
        $maestra = DB::table('maestra_fcv')
            ->select('barra','descriptor','sku','laboratorio','clasificacionTerapeutica')
            ->get();
        return $maestra;
    }
}