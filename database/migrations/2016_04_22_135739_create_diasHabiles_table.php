<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiasHabilesTable extends Migration {
    public function up() {
        Schema::create('dias_habiles', function (Blueprint $table) {
            // PK, fecha, indicando un dia
            $table->date('fecha');
            $table->primary('fecha');
            $table->unique('fecha');

            // Otros campos
            $table->boolean('habil')->default(true);
            $table->string('comentario', 40)->default('');
        });
    }

    public function down() {
        Schema::drop('dias_habiles');
    }
}
