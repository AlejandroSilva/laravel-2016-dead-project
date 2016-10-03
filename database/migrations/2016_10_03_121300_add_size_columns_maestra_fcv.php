<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSizeColumnsMaestraFcv extends Migration
{
    public function up(){
        Schema::table('maestra_fcv', function ($table){
           $table->string('codigoProducto', 27)->change(); //El mayor campo fue de 7
           $table->string('descriptor', 80)->change(); // El mayor campo fue de 60
           $table->string('codigo', 35)->change(); // El campo mas largo fue de 15 caracteres
           $table->string('laboratorio', 60)->change(); //El campo mas largo fue de 40 caracteres
           $table->string('clasificacionTerapeutica', 60)->change();
        });
    }

    public function down(){
        Schema::table('maestra_fcv', function ($table){
            $table->text('codigoProducto')->change();
            $table->text('descriptor')->change();
            $table->text('codigo')->change();
            $table->text('laboratorio')->change();
            $table->text('clasificacionTerapeutica')->change();
        });
    }
}
