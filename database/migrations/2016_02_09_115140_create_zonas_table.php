<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZonasTable extends Migration {
    public function up() {
        Schema::create('zonas', function (Blueprint $table) {
            // PK
            $table->increments('idZona');

            // Otros campos
            $table->string('nombre', 50)
                  ->unique();
        });
    }

    public function down() {
        Schema::drop('zonas');
    }
}
