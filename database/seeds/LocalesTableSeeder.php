<?php

use Illuminate\Database\Seeder;
use App\Locales;

class LocalesTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $parsedData = null;
        DB::transaction(function() {
            $this->parseAndInsert( public_path('seedFiles/localesFCV.csv') );
            $this->parseAndInsert( public_path('seedFiles/localesPreunic.csv') );
        });
    }

    public static function parseAndInsert($file){
        $data = CSVSeeder::csv_to_array($file);

        array_map(function ($row){
            $now = \Carbon\Carbon::now();
            // Local
            $local = [// FK
                'idCliente' => $row['idCliente'],
                'idFormatoLocal' => $row['idFormatoLocal'],
                'idJornadaSugerida' => $row['idJornadaSugerida'],

                // otros campos
                'numero' => $row['numero'],
                'nombre' => $row['nombre'],
                'horaApertura' => $row['horaApertura'],
                'horaCierre' => $row['horaCierre'],
                'emailContacto' => $row['emailContacto'],
                'codArea1' => $row['codArea1'],
                'codArea2' => $row['codArea2'],
                'telefono1' => $row['telefono1'],
                'telefono2' => $row['telefono2'],
                'stock' => $row['stock'],
                'fechaStock' => $row['fechaActualizacionStock'],
                'created_at'=>$now,
                'updated_at'=> $now
            ];
            $idLocal = DB::table('locales')->insertGetId($local);

            // Direccion
            $direccion = [// Composite key (PK & FK)
                'idLocal' => $idLocal, // FK
                'cutComuna' => $row['cutComuna'], // otros campos
                'direccion' => $row['direccion'],
                'referencia' => $row['referencia'],
                'gmapShortUrl' => $row['gmapShortUrl'],
                'gmapIframeUrl' => $row['gmapIframeUrl'],
                'created_at'=>$now,
                'updated_at'=>$now
            ];
            DB::table('direcciones')->insert($direccion);
        }, $data);
    }

    public static function actualizarStock($file){
            $data = CSVSeeder::csv_to_array($file);

            return array_map(function ($row){
                $numero = $row['numero'];
                $stock = $row['stock'];

                // buscar el local (por "numero" y "cliente")
                $local = DB::table('locales')
                    ->where('idCliente','=', '2')
                    ->where('numero','=', $numero)
                    ->first();

                //                if($local==null) throw new \PhpParser\Error("local $numero no encontrado");
                if(!$local)
                    dd($numero);
                
                // BUG WORKAROUND: where->fist no entrega una instancia de eloquent, se debe pedir nuevalemten el elemento 
                $local = Locales::find($local->idLocal);
                $local->stock = $stock;
                $local->fechaStock = '2016-03-30';
                $local->save();
            }, $data);
    }
}
