<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EliminarFechaSubidaNominaOnNominasTable extends Migration {
    public function up() {
        Schema::table('nominas', function (Blueprint $table) {
            $table->dropColumn('fechaSubidaNomina');
        });
    }

    public function down() {
        Schema::table('nominas', function (Blueprint $table) {
            $table
                ->timestamp('fechaSubidaNomina')
                ->nullable()
                ->default(null);
        });
    }
}
