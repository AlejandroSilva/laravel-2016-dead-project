<?php

use Illuminate\Database\Seeder;

class JornadasTableSeeder extends Seeder {
    public function run() {
        DB::table('jornadas')->insert([
            ['idJornada'=> 1, 'nombre'=>'no definido',  'dia'=>false, 'noche'=>false, 'descripcion'=>'no se ha definido una jornada por defecto'],
            ['idJornada'=> 2, 'nombre'=>'día',          'dia'=>true,  'noche'=>false, 'descripcion'=>'el inventario se realizara dentro del dia.'],
            ['idJornada'=> 3, 'nombre'=>'noche',        'dia'=>false, 'noche'=>true,  'descripcion'=>'el inventario se realizara en la tarde/noche.'],
            ['idJornada'=> 4, 'nombre'=>'día y noche',  'dia'=>true,  'noche'=>true,  'descripcion'=>'el inventario se realizara en dos turnos.']
        ]);
    }
}
