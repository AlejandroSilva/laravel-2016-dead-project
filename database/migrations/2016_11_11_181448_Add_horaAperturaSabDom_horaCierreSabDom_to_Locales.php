<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHoraAperturaSabDomHoraCierreSabDomToLocales extends Migration {

    public function up() {
        Schema::table('locales', function(Blueprint $table){
            $table->time('horaCierreDom')->nullable()->after('horaCierre');
            $table->time('horaAperturaDom')->nullable()->after('horaCierre');
            $table->time('horaCierreSab')->nullable()->after('horaCierre');
            $table->time('horaAperturaSab')->nullable()->after('horaCierre');
        });
    }

    public function down() {
        Schema::table('locales', function(Blueprint $table){
            $table->dropColumn('horaAperturaSab');
            $table->dropColumn('horaCierreSab');
            $table->dropColumn('horaAperturaDom');
            $table->dropColumn('horaCierreDom');
        });
    }
}
