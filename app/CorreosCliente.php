<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CorreosCliente extends Model {
    public $primaryKey = 'idCorreo';
    public $timestamps = false;

    protected $fillable = ['idCliente', 'correo'];
}
