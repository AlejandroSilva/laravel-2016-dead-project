<?php

use Illuminate\Database\Seeder;

class JornadasTableSeeder extends Seeder {
    public function run() {
        DB::table('jornadas')->insert([
            ['idJornada'=> 1, 'nombre'=>'no definido',  'descripcion'=>'no se ha definido una jornada por defecto'],
            ['idJornada'=> 2, 'nombre'=>'dia',          'descripcion'=>'el inventario se realizara dentro del dia.'],
            ['idJornada'=> 3, 'nombre'=>'noche',        'descripcion'=>'el inventario se realizara en la tarde/noche.'],
            ['idJornada'=> 4, 'nombre'=>'dia y noche',  'descripcion'=>'el inventario se realizara en dos turnos.']
        ]);
    }
}
