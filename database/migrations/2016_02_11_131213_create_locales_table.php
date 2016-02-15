<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('locales', function (Blueprint $table) {
            // PK
            $table->increments('idLocal');

            // FK
            // Cada local, pertenece a un cliente
            $table->integer('idCliente')
                  ->unsigned();
            $table->foreign('idCliente')
                  ->references('idCliente')
                  ->on('clientes');
            // Cada local, tiene un "formato de local"
            $table->integer('idFormatoLocal')
                  ->unsigned();
            $table->foreign('idFormatoLocal')
                  ->references('idFormatoLocal')
                  ->on('formato_locales');

            // Otros campos
            $table->integer('numero');
            $table->string('nombre', 35);
            $table->time('horaApertura')->nullable();
            $table->time('horaCierre')->nullable();
            $table->string('emailContacto', 50)->default('');
            $table->string('codArea1', 10)->default('');
            $table->string('codArea2', 10)->default('');
            $table->string('telefono1', 20)->default('');
            $table->string('telefono2', 20)->default('');
            $table->integer('stock')->default(0);
            $table->timestamp('fechaStock')->nullable();

            // No puede haber dos locales con el mismo 'nombre' para el mismo cliente
            $table->unique(['idCliente', 'nombre']);
            // No puede haber dos locales con el mismo 'numero de local' para el mismo cliente
            $table->unique(['idCliente', 'numero']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('locales');
    }
}
