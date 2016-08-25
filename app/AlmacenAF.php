<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlmacenAF extends Model {
    protected $table = 'almacenes_activo_fijo';
    public $timestamps = false;
    public $primaryKey = 'idAlmacenAF';
    protected $fillable = ['idLocal', 'idUsuarioResponsable', 'nombre'];

    // #### Relaciones
    // muchos almacenes pertenecen a un Local/Ceco
    public function local(){
        return $this->belongsTo('App\Locales', 'idLocal', 'idLocal');
    }
    public function articulos(){
        return $this->belongsToMany('App\ArticuloAF', 'almacenAF_articuloAF', 'idAlmacenAF', 'idArticuloAF')
            ->withPivot('stockActual');
    }

    // #### Helpers
    public function stockArticulo($idArticulo){
        $existenciaEnAlmacen = $this->articulos->find($idArticulo);
        // si no hay existencia, es porque el articulo no esta en el Almacen
        if(!$existenciaEnAlmacen) return null;

        return $existenciaEnAlmacen->pivot->stockActual;
    }

    // #### Acciones
    // aumentar el stock del articulo en el almacenDestino (crearlo si no esta en el almacen)
    public function agregarStockArticulo($idArticulo, $stock){
        // ### ADVERTENCIA: se supone que al llegar aca, se comprobo que el Articulo existe, y tiene stock valido
        // ### desde el origen. Si esto no es cierto, se pueden agregar mas articulos de los que exisen en un origen

        // buscar la existencia del articulo en el almacen
        $existenciaEnAlmacen = $this->articulos->find($idArticulo);
        if($existenciaEnAlmacen){
            // si existe, se aumenta su stock
            $existenciaEnAlmacen->pivot->stockActual += $stock;
            $existenciaEnAlmacen->pivot->save();
        }else{
            // si no existe el articulo, se crea y se asigna el stock
            $this->articulos()->attach($idArticulo, [
                'stockActual' => $stock
            ]);
        }
    }
    public function quitarStockArticulo($idArticulo, $stock){
        // ### ADVERTENCIA: se supone que al llegar aca, se comprobo que el Articulo existe, y tiene stock valido
        // ### esto es, no se puede descontar mas de lo que tiene asignado.
        // ### Si esto no es cierto, se pueden quitar mas articulos de los que exisen en el almacen

        // buscar la existencia del articulo en el almacen
        $existenciaEnAlmacen = $this->articulos->find($idArticulo);
        if($existenciaEnAlmacen){
            // si el stockActual es distinto (mayor) al stock a descontar, se actualiza el stockActual
            if($existenciaEnAlmacen->pivot->stockActual != $stock){
                $existenciaEnAlmacen->pivot->stockActual -= $stock;
                $existenciaEnAlmacen->pivot->save();
            }
            // si el stockActuale es igual al stock a descontar, entonces se elimina del almance
            else{
                $this->articulos()->detach($idArticulo);
            }
            // si el stockActual es menor al stock a descontar, el resultado es negativo, esto NUNCA deberia pasar
        }else{
            // ERROR: nunca se deberia quitar stock de un producto que no esta asociado al almacen, esto es un error
        }
    }
}
