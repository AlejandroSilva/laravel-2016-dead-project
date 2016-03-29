<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
// Modelos
use App\Role;
use App\Clientes;
use App\Inventarios;

class ProgramacionController extends Controller {
    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */

    // GET programacion/
    public function showIndex(){
        return view('operacional.programacion.programacion-index');
    }

    // GET programacion/mensual
    public function showMensual(){
        $clientesWithLocales = Clientes::allWithSimpleLocales();
        return view('operacional.programacion.programacion-mensual', [
            'clientes' => $clientesWithLocales
        ]);
    }

    // GET programacion/semanal
    public function showSemanal(){
        // buscar la menor fechaProgramada en los inventarios
        $select = Inventarios::
            selectRaw('min(fechaProgramada) as primerInventario, max(fechaProgramada) as ultimoInventario')
            ->get();
        $minymax = $select[0];

        // Captadores
        $rolCaptador = Role::where('name', 'Captador')->first();
        $captadores = $rolCaptador!=null? $rolCaptador->users : '[]';
        // Supervisores
        $rolSupervisor = Role::where('name', 'Supervisor')->first();
        $supervisores = $rolSupervisor!=null? $rolSupervisor->users : '[]';
        // Lideres
        $rolLider = Role::where('name', 'Lider')->first();
        $lideres = $rolLider!=null? $rolLider->users : '[]';

        // buscar la mayor fechaProgramada en los ivventarios
        return view('operacional.programacion.programacion-semanal', [
            'primerInventario'=> $minymax->primerInventario,
            'ultimoInventario'=> $minymax->ultimoInventario,
            'captadores'=> $captadores,
            'supervisores'=> $supervisores,
            'lideres'=> $lideres
        ]);
    }

    /**
     * ##########################################################
     * Rutas para consumo del API REST
     * ##########################################################
     */
}
