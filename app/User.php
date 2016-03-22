<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable {
    // This will enable the relation with Role and add the following methods roles(), hasRole($name),
    // can($permission), and ability($roles, $permissions, $options) within your User model.
    use EntrustUserTrait;
    
//    protected $table = 'users';          // table name
//    public $primaryKey = 'id';      // llave primaria
//    public $timestamps = true;              // este modelo tiene timestamps
    
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'nombre1', 'nombre2', 'apellidoPaterno', 'apellidoMaterno', 'fechaNacimiento',
        'telefono1', 'telefono2', 'RUN', 'contratado', 'bloqueado', 'password'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}