<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuditorias extends Migration {
    public function up(){
        DB::transaction(function() {
            Schema::create('auditorias', function (Blueprint $table) {
                // ### PK
                $table->increments('idAuditoria');

                // ### FK
                // Local
                $table->integer('idLocal')->unsigned();
//                $table->foreign('idLocal')->references('idLocal')->on('locales');

                // Auditor (el auditor asignado puede ser null)
                $table->integer('idAuditor')->unsigned()->nullable();
//                $table->foreign('idAuditor')->references('id')->on('users');

                // ### Otros campos
                $table->date('fechaProgramada');
                $table->timestamps();
            });
        });
    }

    public function down() {
        Schema::dropIfExists('auditorias');
    }
}
