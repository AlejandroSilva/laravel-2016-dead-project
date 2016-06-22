<?php

namespace App;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole {
    // este modelo tiene timestamps
    public $timestamps = true;
    
    //name — Unique name for the Role, used for looking up role information in the application layer.
    //For example: "admin", "owner", "employee".

    //display_name — Human readable name for the Role. Not necessarily unique and optional.
    //For example: "User Administrator", "Project Owner", "Widget Co. Employee".

    //description — A more detailed explanation of what the Role does. Also optional.

    static function darFormatoSimple($role){
        return [
            'id' => $role->id,
            'name' => $role->name,
            'description' => $role->description,
        ];
    }
    
    // #### Helpers / Getters
    static function lideres(){
        $rol = Role::where('name', 'Lider')->first();
        return $rol? $rol->users : collect([]);
    }
    static function auditores(){
        $rol = Role::where('name', 'Auditor')->first();
        return $rol? $rol->users : collect([]);
    }
}