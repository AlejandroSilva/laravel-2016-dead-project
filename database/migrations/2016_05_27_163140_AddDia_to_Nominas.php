<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDiaToNominas extends Migration {

    public function up() {
        Schema::table('nominas', function (Blueprint $table) {
            // dia / noche
            $table->text('turno', 5);
        });

        \App\Inventarios::all()->each(function($inventario){
            // asignar el texto "dia" y "noche" a cada una de las nominas
            $inventario->nominaDia->turno = "Día";
            $inventario->nominaNoche->turno = "Noche";
            $inventario->nominaNoche->save();
            $inventario->nominaDia->save();
        });
    }

    public function down(){
        Schema::table('nominas', function (Blueprint $table) {
            $table->dropColumn('turno');
        });
    }
}
