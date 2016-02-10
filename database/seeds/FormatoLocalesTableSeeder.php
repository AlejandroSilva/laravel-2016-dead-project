<?php

use Illuminate\Database\Seeder;

class FormatoLocalesTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('formato_locales')->insert([
            // Formatos de Bata
            ['idFormatoLocal'=> 1, 'nombre'=>'Calle',           'siglas'=>'CALLE',  'descripcion'=>'(descripcion pendiente)'],
            ['idFormatoLocal'=> 2, 'nombre'=>'Corner',          'siglas'=>'CORNER', 'descripcion'=>'(descripcion pendiente)'],

            // Formatos de Farmacias Cruz Verde
            ['idFormatoLocal'=> 3, 'nombre'=>'Autoservicio',                'siglas'=>'AS', 'descripcion'=>'(descripcion pendiente)'],
            ['idFormatoLocal'=> 4, 'nombre'=>'Multifuncional',              'siglas'=>'MF', 'descripcion'=>'(descripcion pendiente)'],
            ['idFormatoLocal'=> 5, 'nombre'=>'Multifuncional con Góndolas', 'siglas'=>'MG', 'descripcion'=>'(descripcion pendiente)']
        ]);
    }
}
