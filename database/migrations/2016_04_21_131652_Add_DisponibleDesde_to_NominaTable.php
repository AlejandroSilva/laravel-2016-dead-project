<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDisponibleDesdeToNominaTable extends Migration {
    public function up() {
        Schema::table('nominas', function (Blueprint $table) {
            $table
                ->timestamp('fechaArchivoDisponible')
                ->nullable()
                ->default(null);
        });
    }
    public function down() {
        Schema::table('nominas', function (Blueprint $table) {
            $table->dropColumn('fechaArchivoDisponible');
        });
    }
}
