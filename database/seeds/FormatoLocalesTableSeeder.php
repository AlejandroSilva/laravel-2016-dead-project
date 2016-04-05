<?php

use Illuminate\Database\Seeder;

class FormatoLocalesTableSeeder extends Seeder {

    public function run() {
        DB::table('formato_locales')->insert([

            // Formatos de Farmacias Cruz Verde
            ['idFormatoLocal'=> 1, 'nombre'=>'Autoservicio',                'siglas'=>'AS', 'produccionSugerida'=>4000, 'descripcion'=>'...'],
            ['idFormatoLocal'=> 2, 'nombre'=>'Multifuncional',              'siglas'=>'MF', 'produccionSugerida'=>4000, 'descripcion'=>'...'],
            ['idFormatoLocal'=> 3, 'nombre'=>'Multifuncional con Góndolas', 'siglas'=>'MG', 'produccionSugerida'=>4000, 'descripcion'=>'...'],

            // Formatos de Bata
            ['idFormatoLocal'=> 4, 'nombre'=>'Calle',  'siglas'=>'CALLE',  'produccionSugerida'=>4000, 'descripcion'=>'...'],
            ['idFormatoLocal'=> 5, 'nombre'=>'Corner', 'siglas'=>'CORNER', 'produccionSugerida'=>4000, 'descripcion'=>'...'],
            ['idFormatoLocal'=> 6, 'nombre'=>'Mall',   'siglas'=>'MALL',   'produccionSugerida'=>4000, 'descripcion'=>'...'],

            // Pre-Unic
            ['idFormatoLocal'=> 7, 'nombre'=>'Peatonales',    'siglas'=>'PEAT',   'produccionSugerida'=>8500, 'descripcion'=>'...'],
            ['idFormatoLocal'=> 8, 'nombre'=>'No peatonales', 'siglas'=>'NOPEAT', 'produccionSugerida'=>7000, 'descripcion'=>'...'],

            // CKY
            ['idFormatoLocal'=> 9, 'nombre'=>'CKY TEMP',      'siglas'=>'CKYTMP', 'produccionSugerida'=>2500, 'descripcion'=>'...'],
            
            // SALCOBRAND
            ['idFormatoLocal'=>10, 'nombre'=>'Botica (SB)',                     'siglas'=>'BOT_SB', 'produccionSugerida'=>1234, 'descripcion'=>'...'],
            ['idFormatoLocal'=>11, 'nombre'=>'Espacio SB',                      'siglas'=>'ESP_SB', 'produccionSugerida'=>1234, 'descripcion'=>'...'],
            ['idFormatoLocal'=>12, 'nombre'=>'Multifuncional con Góndola (SB)', 'siglas'=>'MFG_SB', 'produccionSugerida'=>1234, 'descripcion'=>'...'],
            ['idFormatoLocal'=>13, 'nombre'=>'Multifuncional (SB)',             'siglas'=>'MF_SB',  'produccionSugerida'=>1234, 'descripcion'=>'...'],
            ['idFormatoLocal'=>14, 'nombre'=>'Store (SB)',                      'siglas'=>'ST_SB',  'produccionSugerida'=>1234, 'descripcion'=>'...'],
        ]);
    }
}
