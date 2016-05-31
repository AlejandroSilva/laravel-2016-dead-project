<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDotacionTotalDotacionOperadoresToNominasTable extends Migration {
    public function up() {
        Schema::table('nominas', function (Blueprint $table) {
            $table->renameColumn('dotacionCaptador1', 'dotacionTotal');
            $table->renameColumn('dotacionCaptador2', 'dotacionOperadores');
        });

        \App\Inventarios::all()->each(function($inventario){
            // se asignan las dotacion a las nuevas dos columnas de la tabla
            $inventario->nominaDia->dotacionTotal = $inventario->dotacionAsignadaTotal;
            $inventario->nominaDia->dotacionOperadores = $inventario->nominaDia->dotacionAsignada;
            $inventario->nominaDia->save();

            $inventario->nominaNoche->dotacionTotal = $inventario->dotacionAsignadaTotal;
            $inventario->nominaNoche->dotacionOperadores = $inventario->nominaNoche->dotacionAsignada;
            $inventario->nominaNoche->save();
        });

        // "Quitar" los cambios no utilizados
        Schema::table('nominas', function (Blueprint $table) {
            $table->renameColumn('dotacionAsignada', '__dotacionAsignada__');
        });
        Schema::table('inventarios', function (Blueprint $table) {
            $table->renameColumn('dotacionAsignadaTotal', '__dotacionAsignadaTotal__');
        });
    }

    public function down() {
        Schema::table('nominas', function (Blueprint $table) {
            $table->renameColumn('dotacionTotal', 'dotacionCaptador1');
            $table->renameColumn('dotacionOperadores', 'dotacionCaptador2');
            
            // "Agregar" el campo eliminado
            $table->renameColumn('__dotacionAsignada__', 'dotacionAsignada');
        });
        Schema::table('inventarios', function (Blueprint $table) {
            // "Agregar" el campo eliminado
            $table->renameColumn('__dotacionAsignadaTotal__', 'dotacionAsignadaTotal');
        });
    }
}
