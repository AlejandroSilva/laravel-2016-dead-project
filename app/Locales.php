<?php

namespace App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Locales extends Model{
    // llave primaria
    public $primaryKey = 'idLocal';
    // este modelo TIENE timestamps
    public $timestamps = true;

    protected $fillable = [
        'idCliente', 'idFormatoLocal', 'idJornadaSugerida', 'numero', 'nombre', 'horaApertura', 'horaCierre',
        'emailContacto', 'telefono1', 'telefono2', 'stock', 'fechaStock'
    ];
    protected $guarded = ['idLocal'];

    // #### Relaciones
    public function cliente(){
        // belongsTo(modelo, this.fogeignKey, parent.otherKey)
        return $this->belongsTo('App\Clientes', 'idCliente', 'idCliente');
    }
    public function formatoLocal(){
        // belongsTo(modelo, this.fogeignKey, parent.otherKey)
        return $this->belongsTo('App\FormatoLocales', 'idFormatoLocal', 'idFormatoLocal');
    }
    public function direccion(){
        //return $this->hasOne('App\Model', 'foreign_key', 'local_key');
        return $this->hasOne('App\Direcciones', 'idLocal', 'idLocal');
    }
    public function inventarios(){
        //return $this->hasMany('App\Comment', 'foreign_key', 'local_key');
        return $this->hasMany('App\Inventarios', 'idLocal', 'idLocal');
    }
    public function auditorias(){
        //return $this->hasMany('App\Comment', 'foreign_key', 'local_key');
        return $this->hasMany('App\Auditorias', 'idLocal', 'idLocal');
    }
    public function jornada(){
        // belongsTo(modelo, this.fogeignKey, parent.otherKey)
        return $this->belongsTo('App\Jornadas', 'idJornadaSugerida', 'idJornada');
    }
    public function almacenesAF(){
        return $this->hasMany('App\AlmacenAF', 'idLocal', 'idLocal');
    }
    
    // #### Helpers
    public function llegadaSugeridaLiderDia(){
        // La hora de llegada sugerida para el lider corresponde a 1:30hrs antes de la apertura de local
        if($this->horaApertura=='00:00:00')
            return '00:00:00';
        else{
            if($this->idCliente==4){
                // en CID, el lider debe llegar 1:00hrs hora antes de la apertura
                return date('H:i:s', strtotime($this->horaApertura)-3600); // 3600 = 60min * 60seg
            }else{
                // en los otros clientes, el lider debe llegar 1:30hrs antes de la apertura
                return date('H:i:s', strtotime($this->horaApertura)-5400); // 5400 = 90min * 60seg
            }
        }
    }
    public function llegadaSugeridaLiderNoche(){
        // La hora de llegada sugerida para el lider corresponde a 1:30hrs antes del cierre de local
        if($this->horaCierre=='00:00:00')
            return '00:00:00';
        else{
            if($this->idCliente==4){
                // en CID, el lider debe llegar 1 hora antes
                return date('H:i:s', strtotime($this->horaCierre)-3600); // 3600 = 60min * 60seg
            }else{
                // en los otros clientes, el lider debe llegar 1:30 antes
                return date('H:i:s', strtotime($this->horaCierre)-5400); // 5400 = 90min * 60seg
            }
        }
    }
    public function llegadaSugeridaPersonalDia(){
        // La hora de llegada sugerida para el equipo corresponde a 1:30hrs antes de la apertura del local (junto con el lider)
        if($this->horaApertura=='00:00:00')
            return '00:00:00';
        else{
            if($this->idCliente==4){
                // en CID, el equipo debe llegar 01:00hrs antes de la apertura del local (junto con el lider)
                return date('H:i:s', strtotime($this->horaApertura)-3600); // 3600 = 60min * 60seg
            }else{
                // en los otros clientes, el equipo debe llegar 01:00hrs antes
                return date('H:i:s', strtotime($this->horaApertura)-5400); // 5400 = 90min * 60seg
            }
        }
    }
    public function llegadaSugeridaPersonalNoche(){
        // La hora de llegada sugerida para el equipo corresponde a 1 hora antes del cierre de local
        if($this->horaCierre=='00:00:00')
            return '00:00:00';
        else{
            if($this->idCliente==4){
                // en CID, el equipo debe llegar 00:30hrs antes del cierre del local
                return date('H:i:s', strtotime($this->horaCierre)-1800); // 3600 = 30min * 60seg
            }else{
                // en los otros clientes, el equipo debe llegar 01:00hrs antes
                return date('H:i:s', strtotime($this->horaCierre)-3600); // 3600 = 60min * 60seg
            }
        }
    }

    // #### Acciones
    //

    // ####  Getters
    public function horaAperturaF(){
        // Ejemplo: convertir "21:30:00" -> "21:30 hrs."
        $carbon = Carbon::parse($this->horaApertura);
        $minutes = $carbon->minute < 10? "0$carbon->minute" : $carbon->minute;
        return "$carbon->hour:$minutes hrs.";
    }
    public function horaCierreF(){
        // Ejemplo: convertir "21:30:00" -> "21:30 hrs."
        $carbon = Carbon::parse($this->horaCierre);
        $minutes = $carbon->minute < 10? "0$carbon->minute" : $carbon->minute;
        return "$carbon->hour:$minutes hrs.";
    }
    public function stockF(){
        // agregar un punto en los miles y millones: Ej. de '68431' a '68.431'
        return number_format($this->stock);
    }

    // ####  Setters
    // set_stock se llama para cambiar el Stock de un local, y RECALCULAR LA DOTACION de sus inventarios (y nominas)
    public function set_stock($stock, $fechaStock){
        // actualizar el stock
        $this->stock = $stock;
        $this->fechaStock = $fechaStock;
        $this->save();

        // actualizar los datos de los inventarios del local (solo los pendientes)
        return [
            'cliente' => $this->cliente->nombreCorto,
            'local' => "($this->numero) $this->nombre",
            'error' => '',
            'estado' => "stock y fechaStock actualizado ($stock al $fechaStock)",
            'inventarios' => $this->inventarios->map(function($inv) use($stock, $fechaStock){
                return $inv->set_stock($stock, $fechaStock);
            })
        ];
    }

    // #### Formatear respuestas
    static function formatearSimple($local){
        return [
            'idLocal' => $local->idLocal,
            'nombre' => $local->nombre,
            'numero' => $local->numero,
            // stock
            'stock' => $local->stock,
            'fechaStock' => $local->fechaStock,
            // apertura
            'horaApertura' => $local->horaApertura,
            'horaAperturaF' => $local->horaAperturaF(),
            'horaCierre' => $local->horaCierre,
            'horaCierreF' => $local->horaCierreF(),
            // contacto
            'emailContacto' => $local->emailContacto,
            'telefono1' => "$local->codArea1 $local->telefono1",
            'telefono2' => "$local->codArea2 $local->telefono2",
        ];
    }
    static function formatoLocal_completo($local){
        $_local = Locales::formatearSimple($local);
        $_local['cliente'] = Clientes::formatearSimple($local->cliente);
        // Formato de local
        $_local['idFormatoLocal'] = $local->formatoLocal->idFormatoLocal;
        $_local['formatoLocal_nombre'] = $local->formatoLocal->nombre;
        $_local['formatoLocal_produccionSugerida'] = $local->formatoLocal->produccionSugerida;
        // Jornada Sugerida
        $_local['idJornadaSugerida'] = $local->jornada->idJornada;
        // direcion comuna provincia region
        $_local['direccion'] = $local->direccion->direccion;
        // Comuna
        $_local['cutComuna'] = $local->direccion->comuna->cutComuna;
        $_local['comuna_nombre'] = $local->direccion->comuna->nombre;
        // Region
        $_local['cutRegion'] = $local->direccion->comuna->provincia->region->cutRegion;
        $_local['region_numero'] = $local->direccion->comuna->provincia->region->numero;

        return $_local;
    }

    // #### Scopes para hacer Querys/Busquedas
    public function inventarioRealizadoEn($annoMesDia){
        $fecha = explode('-', $annoMesDia);
        return Inventarios::where('idLocal', '=', $this->idLocal)
            ->whereRaw("extract(year from fechaProgramada) = ?", [$fecha[0]])
            ->whereRaw("extract(month from fechaProgramada) = ?", [$fecha[1]])
            ->first();
    }
}
