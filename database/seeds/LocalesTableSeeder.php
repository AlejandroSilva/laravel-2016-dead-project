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
            $this->parseAndInsert( public_path('seedFiles/wom_final.csv') );
        });
    }

    public static function parseAndInsert($file){
        //$data = CSVReader::csv_to_array($file, ',');
        $data = CSVReader::csv_to_array(public_path('seedFiles/wom_final.csv'), ',');

        for($i=1; $i<count($data) ; $i++){
            $row = $data[$i];

            $now = \Carbon\Carbon::now();
            // Local
            $local = [// FK
                'idCliente' => $row[0],
                'idFormatoLocal' => $row[1],
                'idJornadaSugerida' => $row[2],

                // otros campos
                'numero' => $row[3],
                'nombre' => $row[4],
                'horaApertura' => $row[9],
                'horaCierre' => $row[10],
                'horaAperturaSab' => $row[11],
                'horaCierreSab' => $row[12],
                'horaAperturaDom' => $row[13],
                'horaCierreDom' => $row[14],
                'emailContacto' => '',//$row['emailContacto'],
                'codArea1' => '',//$row['codArea1'],
                'codArea2' => '',//$row['codArea2'],
                'telefono1' => '',//$row['telefono1'],
                'telefono2' => '',//$row['telefono2'],
                'stock' => 1, //$row['stock'],
                'fechaStock' => '2016-11-11', //$row['fechaActualizacionStock'],
                'created_at'=>$now,
                'updated_at'=> $now
            ];
            $idLocal = DB::table('locales')->insertGetId($local);

            // Direccion
            $direccion = [// Composite key (PK & FK)
                'idLocal' => $idLocal, // FK
                'cutComuna' => $row[18], // otros campos
                'direccion' => $row[19],
                'referencia' => '',//$row['referencia'],
                'gmapShortUrl' => '',//$row['gmapShortUrl'],
                'gmapIframeUrl' => '',//$row['gmapIframeUrl'],
                'created_at'=>$now,
                'updated_at'=>$now
            ];
            DB::table('direcciones')->insert($direccion);
        }
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
