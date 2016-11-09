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
    function getTotalProductos($conFormato=false){
        $res = DB::table('productos_fcv')
            ->select(DB::raw('count(sku) as total'))
            ->where('idArchivoMaestra', $this->idArchivoMaestra)
            ->first();
        return $conFormato? number_format($res->total, 0, ',', '.') : $res->total;
    }
    function getBarrasDuplicadas(){
        $skuDuplicados = DB::table('productos_fcv')
            ->select('sku', 'barra','descriptor', 'laboratorio', 'clasificacionTerapeutica', DB::raw('count(barra) as total'))
            ->groupBy('barra')
            ->where('idArchivoMaestra', $this->idArchivoMaestra)
            ->havingRaw('total > 1')
            ->get();

        // obtener el detalle de los productos duplicados
        $productos = collect();
        foreach ($skuDuplicados as $prod){
            $productosSKU = ProductosFCV::where('barra', $prod->barra)
                ->where('idArchivoMaestra', $this->idArchivoMaestra)
                ->get();
            $productos = $productos->merge( $productosSKU );
        }
        return (object)[
            'total' => count($productos),
            'productos' => $productos
        ];
    }
    function getCamposVacios(){
        $camposVacios = DB::select(DB::raw("
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
        return (object)[
            'total' => count($camposVacios),
            'productos' => $camposVacios
        ];
    }
    function getDescriptoresDistintos(){
        $conDescriptoresDistintos = DB::select( DB::raw("
            select sku, count(sku) as totalDescriptores from 
                (SELECT idProductoFCV, sku, descriptor
                FROM SEI_SIG.productos_fcv
                where idArchivoMaestra=$this->idArchivoMaestra
                group by sku, descriptor) as productoDescriptor
            group by productoDescriptor.sku
            having totalDescriptores>1
        "));

        // buscar todos los productos con problemas
        $productos = collect();
        foreach ($conDescriptoresDistintos as $prod){
            $productosSKU = ProductosFCV::where('sku', $prod->sku)
                ->where('idArchivoMaestra', $this->idArchivoMaestra)
                ->get();
            $productos = $productos->merge( $productosSKU );
        }
        $productosAll = $productos->all();
        return (object)[
            'total' => count($productosAll),
            'productos' => $productosAll
        ];
    }
    function getLaboratoriosDistintos(){
        $conLaboratoriosDistintos = DB::select( DB::raw("
            select sku, count(sku) as totalLaboratorios from 
                (SELECT idProductoFCV, sku, laboratorio
                FROM SEI_SIG.productos_fcv
                where idArchivoMaestra=$this->idArchivoMaestra
                group by sku, laboratorio) as productoLaboratorio
            group by productoLaboratorio.sku
            having totalLaboratorios>1
        "));

        // buscar todos los productos con problemas
        $productos = collect();
        foreach ($conLaboratoriosDistintos as $prod){
            $productosSKU = ProductosFCV::where('sku', $prod->sku)
                ->where('idArchivoMaestra', $this->idArchivoMaestra)
                ->get();
            $productos = $productos->merge( $productosSKU );
        }
        $productosAll = $productos->all();
        return (object)[
            'total' => count($productosAll),
            'productos' => $productosAll
        ];
    }
    function getClasificacionesDistintas(){
        $conClasificacionDistinta = DB::select( DB::raw("
            select sku, count(sku) as totalClasificaciones from 
                (SELECT idProductoFCV, sku, clasificacionTerapeutica
                FROM SEI_SIG.productos_fcv
                where idArchivoMaestra=$this->idArchivoMaestra
                group by sku, clasificacionTerapeutica) as productoClasificacion
            group by productoClasificacion.sku
            having totalClasificaciones>1
        "));

        // buscar todos los productos con problemas
        $productos = collect();
        foreach ($conClasificacionDistinta as $prod){
            $productosSKU = ProductosFCV::where('sku', $prod->sku)
                ->where('idArchivoMaestra', $this->idArchivoMaestra)
                ->get();
            $productos = $productos->merge( $productosSKU );
        }
        $productosAll = $productos->all();
        return (object)[
            'total' => count($productosAll),
            'productos' => $productosAll
        ];
    }


    // #### Setters
    function setResultado($resultado, $maestraValida){
        $this->resultado = $resultado;
        $this->maestraValida = $maestraValida;
        $this->save();
    }

    // #### Helpers

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