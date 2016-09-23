<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaestraFcvTable extends Migration
{
    public function up()
    {
        Schema::create('maestra_fcv', function (Blueprint $table) {
            // PK
            $table->increments('idMaestraFCV');
            // FK
            $table->integer('idArchivoMaestra')->unsigned()->nullable();
            // Referencias
            $table->foreign('idArchivoMaestra')->references('idArchivoMaestra')->on('archivo_maestra_fcv');
            // Otros campos
            $table->text('codigoProducto');
            $table->text('descriptor');
            $table->text('codigo');
            $table->text('laboratorio');
            $table->text('clasificacionTerapeutica');
            $table->timestamps();

        });
    }
    
    public function down(){
        Schema::drop('maestra_fcv');
    }
}
