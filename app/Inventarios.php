<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
// Modelos
use App\Locales;

class Inventarios extends Model {
    // llave primaria
    public $primaryKey = 'idInventario';

    // este modelo tiene timestamps
    public $timestamps = true;

    // #### Relaciones
    public function local(){
        //return $this->belongsTo('App\Model', 'foreign_key', 'other_key');
        return $this->belongsTo('App\Locales', 'idLocal', 'idLocal');
    }

//    public function jornada(){
//        //return $this->hasOne('App\Model', 'foreign_key', 'local_key');
//        return $this->hasOne('App\Jornadas', 'idJornada', 'idJornada');
//    }

    public function nominaDia(){
//        return $this->hasOne('App\Nominas', 'idNomina', 'idNominaDia');
        return $this->belongsTo('App\Nominas', 'idNominaDia', 'idNomina');
    }
    public function nominaNoche(){
//        return $this->hasOne('App\Nominas', 'idNomina', 'idNominaNoche');
        return $this->belongsTo('App\Nominas', 'idNominaNoche', 'idNomina');
    }

    // Consultas
    public function fechaProgramadaF(){
        setlocale(LC_TIME, 'es_CL.utf-8');
        // fecha con formato: ejemplo: "2016-05-30" -> "lunes 30 de mayo, 2016"
        return Carbon::parse($this->fechaProgramada)->formatLocalized('%A %e de %B, %Y');
    }

    // #### With scopes
    public function scopeWithTodo($query){
        $query->with([
            'local.cliente',
            'local.formatoLocal',
            'local.direccion.comuna.provincia.region',
            'nominaDia',
            'nominaNoche',
            'nominaDia.lider',
            'nominaNoche.lider',
            'nominaDia.captador',
            'nominaNoche.captador',
        ]);
    }
    public function scopeWithClienteFormatoRegion($query) {
        $query->with([
            'local.cliente',
            'local.formatoLocal',
            'local.direccion.comuna.provincia.region'
        ]);
    }

    // #### Formatear
    static function formatoSimple($inventario){
        return [
            'idInventario' => $inventario->idInventario,
            'idJornada' => $inventario->idJornada,
            'inventario_fechaProgramada' => $inventario->fechaProgramada,
            'inventario_fechaProgramadaF' => $inventario->fechaProgramadaF(),
            'inventario_stockTeorico' => $inventario->stockTeorico,
            'inventario_fechaStock' => $inventario->fechaStock,
            'inventario_dotacionAsignadaTotal' => $inventario->dotacionAsignadaTotal,
        ];
    }
    static function formatoClienteFormatoRegion($inventario) {
        $inventarioArray = Inventarios::formatoSimple($inventario);
        $inventarioArray['local'] = Locales::formatearConClienteFormatoDireccionRegion($inventario->local);
        return $inventarioArray;
    }

    static function formatoClienteFormatoRegion_nominas ($inventario) {
        $_inventario = Inventarios::formatoSimple($inventario);
        $_inventario['local'] = Locales::formatearConClienteFormatoDireccionRegion($inventario->local);
        $_inventario['nominaDia']   = $inventario->nominaDia->habilitada? Nominas::formatearSimple($inventario->nominaDia) : null;
        $_inventario['nominaNoche'] = $inventario->nominaNoche->habilitada? Nominas::formatearSimple($inventario->nominaNoche) : null;
        return $_inventario;
    }
}
