<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Nominas;

class AddEstadoToNominasTabla extends Migration {
    public function up() {
        Schema::table('nominas', function(Blueprint $table){
            // PK estados_nominas
            $table->integer('idEstadoNomina')->unsigned()->default(2);  // pendiente por defecto
            $table->foreign('idEstadoNomina')
                ->references('idEstadoNomina')->on('estado_nominas');
        });

        // Si las nominas tienen fecha de subida, entonces se marcan como recibidas
        foreach(Nominas::where('fechaSubidaNomina', '!=', '0000-00-00')->get() as $nomina){
            $nomina->idEstadoNomina = 5;    // estado: Enviada
            $nomina->save();
        }
    }
    
    public function down() {
        Schema::table('nominas', function(Blueprint $table){
            $table->dropForeign('nominas_idestadonomina_foreign');
            $table->dropColumn('idEstadoNomina');
        });
    }
}
