<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
// Modelos
use App\Geo;

class CreateGeoTable extends Migration {
    public function up() {
        Schema::create('geos', function (Blueprint $table) {
            // PK
            $table->increments('idGeo');

            // FK
            // idZona de la tabla Zonas
            $table->integer('idZona')
                ->unsigned();
            $table->foreign('idZona')
                ->references('idZona')
                ->on('zonas');
            
            // Otros campos
            $table->string('nombre', 40)->unique();
            $table->integer('min');
            $table->integer('max');
            $table->timestamps();
        });
        
        // Agregar UN GEO por defecto
        $geo = new Geo();
        $geo->nombre = "-SIN GEO-";
        $geo->idZona = 1;
        $geo->save();
    }
    
    public function down() {
        Schema::drop('geos');
    }
}
