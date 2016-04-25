<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AgregarFechaSubidaNominaOnNominasTable extends Migration {
    public function up() {
        Schema::table('nominas', function (Blueprint $table) {
            $table->date('fechaSubidaNomina');
        });
    }

    public function down() {
        Schema::table('nominas', function (Blueprint $table) {
            $table->dropColumn('fechaSubidaNomina');
        });
    }
}
