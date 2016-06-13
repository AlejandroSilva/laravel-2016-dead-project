<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NominaLog extends Model {
    public $primaryKey = 'idNomina';    // llave primaria
    public $timestamps = true;         // este modelo tiene timestamps

    protected $fillable = ['idNomina', 'titulo', 'texto', 'importancia', 'mostrarAlerta'];

    public function nomina(){
        return $this->belongsTo('App\Nominas', 'idNomina', 'idNomina');
    }
}
