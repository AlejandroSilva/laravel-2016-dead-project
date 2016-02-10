<?php

use Illuminate\Database\Seeder;

class ZonasTableSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('zonas')->insert([
            ['idZona'=>1, 'nombre'=>'Norte Grande'],
            ['idZona'=>2, 'nombre'=>'Norte Chico'],
            ['idZona'=>3, 'nombre'=>'Valparaiso'],
            ['idZona'=>4, 'nombre'=>'Metropolitana'],
            ['idZona'=>5, 'nombre'=>'Sexta Región y Maule'],
            ['idZona'=>6, 'nombre'=>'Octaba y Novena Región'],
            ['idZona'=>7, 'nombre'=>'Sur extremo']
        ]);
    }
}
