<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Nominas;

class CreateNominasCaptadoresTable extends Migration {
    public function up() {
        // tabla intermedia entre nominas y ususarios (captadores), la relacion es muchos a muchos
        Schema::create('nominas_captadores', function (Blueprint $table) {
            // PK Compuesta
            $table->integer('idCaptador')->unsigned();
            $table->integer('idNomina')->unsigned();

            // referencias a otras tablas
            $table->foreign('idCaptador')->references('id')->on('users');
            $table->foreign('idNomina')->references('idNomina')->on('nominas');
            // primary compuesta
            $table->primary(['idCaptador', 'idNomina']);

            // Otros campos
            $table->integer('operadoresAsignados')->default(0);
            $table->timestamps();
        });

        // una vez creada la tabla, el "Captador1" y el "Captador2" de la tabla "nominas", pasan
        // a la tabla "nominas_captadores"
        Nominas::all()->each(function($nomina){
            $now = \Carbon\Carbon::now();

            // el operador por defecto, es Bernardita Gamboa (8)
            App\NominasCaptadores::insert([
                'idNomina' => $nomina->idNomina,
                'idCaptador' => 8,
                'operadoresAsignados' => $nomina->dotacion()->count(),
                'created_at' => $now,
                'updated_at' => $now
            ]);
            if($nomina->idCaptador1){
                //$nomina->captadores()
                 App\NominasCaptadores::insert([
                     'idNomina' => $nomina->idNomina,
                     'idCaptador' => $nomina->idCaptador1,
                     'operadoresAsignados' => 0,
                     'created_at' => $now,
                     'updated_at' => $now
                ]);
            }
            if($nomina->idCaptador2){
                //$nomina->captadores()
                App\NominasCaptadores::insert([
                    'idNomina' => $nomina->idNomina,
                    'idCaptador' => $nomina->idCaptador2,
                    'operadoresAsignados' => 0,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        });
    }

    public function down() {
        Schema::drop('nominas_captadores');
    }
}
