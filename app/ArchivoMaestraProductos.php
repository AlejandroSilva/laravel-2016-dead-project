<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
// DB
use DB;

class ArchivoMaestraProductos extends Model{
    public $table = 'archivos_maestra_productos';
    // PK
    public $primaryKey = 'idArchivoMaestra';

    public $timestamps = true;
    // Campos asignables
    protected $fillable = ['idCliente', 'idSubidoPor',  'nombreArchivo', 'nombreOriginal', 'resultado'];
    
    // #### Relaciones
    function cliente(){
        return $this->belongsTo('App\Clientes', 'idCliente','idCliente');
    }
    function subidoPor(){
        return $this->belongsTo('App\User', 'idSubidoPor', 'id');
    }
    function productos(){
        return $this->hasMany('App\ProductosFCV','idArchivoMaestra','idArchivoMaestra');
    }

    // #### Getters
    static function getPathCarpeta($cliente){
        return public_path()."/$cliente/maestra-productos/";
    }
    function getFullPath(){
        $cliente  = $this->cliente->nombreCorto;
        return self::getPathCarpeta($cliente).$this->nombreArchivo;
    }
    function getProductosDuplicados(){
        // FCV
        if($this->idCliente==2){
            return DB::table('productos_fcv')
                ->select('sku', 'barra', DB::raw('count(barra) as total'))
                ->groupBy('barra')
                ->where('idArchivoMaestra', $this->idArchivoMaestra)
                ->havingRaw('total > 1')
                ->get();
        }else{
            return null;
        }
    }
    function getProductosInvalidos(){
        // FCV
        if($this->idCliente==2){
            return DB::select(DB::raw("
                select * from productos_fcv 
                where idArchivoMaestra = $this->idArchivoMaestra 
                and(
                    sku = '' 
                    or barra = '' 
                    or descriptor = '' 
                    or laboratorio = '' 
                    or clasificacionTerapeutica = ''
                )
            "));
        }else{
            return null;
        }
    }
    // #### Setters
    function setResultado($resultado, $maestraValida){
        $this->resultado = $resultado;
        $this->maestraValida = $maestraValida;
        $this->save();
    }

    // #### Helpers
    function procesarArchivo(){
        // FCV
        if($this->idCliente==2){
            // parsear Excel a Array de productos
            $resultadoProductos = \ArchivoMaestraFCVHelper::parsearExcelAProductos($this->getFullPath(), $this->idArchivoMaestra);
            if(isset($resultadoProductos->error)){
                $this->setResultado($resultadoProductos->error, false);
                return $resultadoProductos;
            }

            // Eliminar los productos anteriores de este mismo idArchivoMaestra
            ProductosFCV::where('idArchivoMaestra', $this->idArchivoMaestra)->delete();

            // Agregar productos a la DB
            $chunks = array_chunk($resultadoProductos->productos, 5000, true);
            DB::transaction(function() use ($chunks){
                foreach($chunks as $chunk) {
                    ProductosFCV::insert($chunk);
                }
            });

            // verificar que no existan duplicados o productos invalidos
            $error = null;
            $totalDuplicados = count($this->getProductosDuplicados());
            $totalInvalidos  = count($this->getProductosInvalidos());
            // Existen duplicados?
            if($totalDuplicados>0 && $totalInvalidos==0){
                $error = "Maestra cargada, con $totalDuplicados barras duplicadas.";
            }
            // Existen Invalidos?
            elseif($totalDuplicados==0 && $totalInvalidos>0) {
                $error = "Maestra cargada, con $totalInvalidos productos invalidos.";
            }
            // Existen Invalidos y Duplicados al mismo tiempo?
            elseif($totalDuplicados>0 && $totalInvalidos>0){
                $error = "Maestra cargada, con $totalInvalidos productos invalidos y $totalDuplicados barras duplicadas.";
            }

            if($error==null){
                $mensajeExito = "Productos cargados correctamente";
                $this->setResultado($mensajeExito, true);
                return (object)['mensajeExito' => $mensajeExito];
            }else{
                $this->setResultado($error, false);
                return (object)['mensajeError' => $error];
            }
        }else{
            $errorClienteNoSoportado = 'Actualmente no se puede cargar los productos de este cliente.';

            $this->setResultado($errorClienteNoSoportado, false);
            return (object)['mensajeError' => $errorClienteNoSoportado];
        }
    }

    // #### buscar
    static function buscar($peticion){
        $query = ArchivoMaestraProductos::with([]);

        // cliente obligatorio
        $query->where('idCliente', $peticion->idCliente);

        // orden, por defecto ASC
        if(isset($peticion->orden) && $peticion->orden=='desc')
            $query->orderBy('created_at', 'desc');
        else
            $query->orderBy('created_at', 'asc');
        return $query->get();
    }
}