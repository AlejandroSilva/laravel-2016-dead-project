<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
// Modelos
use App\Comunas;

class AddSubgeotoComunasTable extends Migration {
    
    public function up() {
        // Agregar el campo idSubgeo
        Schema::table('comunas', function(Blueprint $table){
            $table->integer('idSubgeo')->unsigned();
        });
        
        // Asignar un Subgeo por defecto
        Schema::table('comunas', function(Blueprint $table){
            foreach(Comunas::all() as $comuna) {
                // Luego de agregar el campo idSubgeo, asignar un subegeo por defecto
                // y hacer la referencia a la Tabla Subgeos
                $comuna->idSubgeo = 1;  // '-SIN GEO-'
                $comuna->save();
            }
            $table->foreign('idSubgeo')->references('idSubgeo')->on('subgeos');
        });
    }
    
    public function down() {
        Schema::table('comunas', function(Blueprint $table) {
            $table->dropColumn('idSubgeo');
        });
    }
}
