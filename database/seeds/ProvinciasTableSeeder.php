<?php

use Illuminate\Database\Seeder;

class ProvinciasTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('provincias')->insert([
            ['cutRegion'=>15, 'cutProvincia'=>151, 'nombre'=>'Arica'],
            ['cutRegion'=>15, 'cutProvincia'=>152, 'nombre'=>'Parinacota'],
            ['cutRegion'=> 1, 'cutProvincia'=> 11, 'nombre'=>'Iquique'],
            ['cutRegion'=> 1, 'cutProvincia'=> 14, 'nombre'=>'Tamarugal'],
            ['cutRegion'=> 2, 'cutProvincia'=> 21, 'nombre'=>'Antofagasta'],
            ['cutRegion'=> 2, 'cutProvincia'=> 22, 'nombre'=>'El Loa'],
            ['cutRegion'=> 2, 'cutProvincia'=> 23, 'nombre'=>'Tocopilla'],
            ['cutRegion'=> 3, 'cutProvincia'=> 31, 'nombre'=>'Copiapó'],
            ['cutRegion'=> 3, 'cutProvincia'=> 32, 'nombre'=>'Chañaral'],
            ['cutRegion'=> 3, 'cutProvincia'=> 33, 'nombre'=>'Huasco'],
            ['cutRegion'=> 4, 'cutProvincia'=> 41, 'nombre'=>'Elqui'],
            ['cutRegion'=> 4, 'cutProvincia'=> 42, 'nombre'=>'Choapa'],
            ['cutRegion'=> 4, 'cutProvincia'=> 43, 'nombre'=>'Limarí'],
            ['cutRegion'=> 5, 'cutProvincia'=> 51, 'nombre'=>'Valparaíso'],
            ['cutRegion'=> 5, 'cutProvincia'=> 52, 'nombre'=>'Isla de Pascua'],
            ['cutRegion'=> 5, 'cutProvincia'=> 53, 'nombre'=>'Los Andes'],
            ['cutRegion'=> 5, 'cutProvincia'=> 54, 'nombre'=>'Petorca'],
            ['cutRegion'=> 5, 'cutProvincia'=> 55, 'nombre'=>'Quillota'],
            ['cutRegion'=> 5, 'cutProvincia'=> 56, 'nombre'=>'San Antonio'],
            ['cutRegion'=> 5, 'cutProvincia'=> 57, 'nombre'=>'San Felipe de Aconcagua'],
            ['cutRegion'=> 5, 'cutProvincia'=> 58, 'nombre'=>'Marga Marga'],
            ['cutRegion'=> 6, 'cutProvincia'=> 61, 'nombre'=>'Cachapoal'],
            ['cutRegion'=> 6, 'cutProvincia'=> 62, 'nombre'=>'Cardenal Caro'],
            ['cutRegion'=> 6, 'cutProvincia'=> 63, 'nombre'=>'Colchagua'],
            ['cutRegion'=> 7, 'cutProvincia'=> 71, 'nombre'=>'Talca'],
            ['cutRegion'=> 7, 'cutProvincia'=> 72, 'nombre'=>'Cauquenes'],
            ['cutRegion'=> 7, 'cutProvincia'=> 73, 'nombre'=>'Curicó'],
            ['cutRegion'=> 7, 'cutProvincia'=> 74, 'nombre'=>'Linares'],
            ['cutRegion'=> 8, 'cutProvincia'=> 81, 'nombre'=>'Concepción'],
            ['cutRegion'=> 8, 'cutProvincia'=> 82, 'nombre'=>'Arauco'],
            ['cutRegion'=> 8, 'cutProvincia'=> 83, 'nombre'=>'Biobío'],
            ['cutRegion'=> 8, 'cutProvincia'=> 84, 'nombre'=>'Ñuble'],
            ['cutRegion'=> 9, 'cutProvincia'=> 91, 'nombre'=>'Cautín'],
            ['cutRegion'=> 9, 'cutProvincia'=> 92, 'nombre'=>'Malleco'],
            ['cutRegion'=>14, 'cutProvincia'=>141, 'nombre'=>'Valdivia'],
            ['cutRegion'=>14, 'cutProvincia'=>142, 'nombre'=>'Ranco'],
            ['cutRegion'=>10, 'cutProvincia'=>101, 'nombre'=>'Llanquihue'],
            ['cutRegion'=>10, 'cutProvincia'=>102, 'nombre'=>'Chiloé'],
            ['cutRegion'=>10, 'cutProvincia'=>103, 'nombre'=>'Osorno'],
            ['cutRegion'=>10, 'cutProvincia'=>104, 'nombre'=>'Palena'],
            ['cutRegion'=>11, 'cutProvincia'=>111, 'nombre'=>'Coyhaique'],
            ['cutRegion'=>11, 'cutProvincia'=>112, 'nombre'=>'Aysén'],
            ['cutRegion'=>11, 'cutProvincia'=>113, 'nombre'=>'Capitán Prat'],
            ['cutRegion'=>11, 'cutProvincia'=>114, 'nombre'=>'General Carrera'],
            ['cutRegion'=>12, 'cutProvincia'=>121, 'nombre'=>'Magallanes'],
            ['cutRegion'=>12, 'cutProvincia'=>122, 'nombre'=>'Antártica Chilena'],
            ['cutRegion'=>12, 'cutProvincia'=>123, 'nombre'=>'Tierra del Fuego'],
            ['cutRegion'=>12, 'cutProvincia'=>124, 'nombre'=>'Última Esperanza'],
            ['cutRegion'=>13, 'cutProvincia'=>131, 'nombre'=>'Santiago'],
            ['cutRegion'=>13, 'cutProvincia'=>132, 'nombre'=>'Cordillera'],
            ['cutRegion'=>13, 'cutProvincia'=>133, 'nombre'=>'Chacabuco'],
            ['cutRegion'=>13, 'cutProvincia'=>134, 'nombre'=>'Maipo'],
            ['cutRegion'=>13, 'cutProvincia'=>135, 'nombre'=>'Melipilla'],
            ['cutRegion'=>13, 'cutProvincia'=>136, 'nombre'=>'San Antonio']
        ]);
    }
}
