<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
// PHP Excel
use PHPExcel;
use PHPExcel_IOFactory;
// Modelos
use Auth;
use Redirect;
use App\Role;
use App\Clientes;
use App\Inventarios;

class ProgramacionController extends Controller {
    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */

    // GET programacionIG/
    public function showIndex(){
        return view('operacional.programacionIG.programacionIG-index');
    }

    // GET programacionIG/mensual
    public function showMensual(){
        // validar de que el usuario tenga los permisos
        $user = Auth::user();
        if(!$user || !$user->can('programaInventarios_ver'))
            return view('errors.403');

        $clientesWithLocales = Clientes::allWithSimpleLocales();
        return view('operacional.programacionIG.programacionIG-mensual', [
            'puedeAgregarInventarios'   => $user->can('programaInventarios_agregar')? "true":"false",
            'puedeModificarInventarios' => $user->can('programaInventarios_modificar')? "true":"false",
            'clientes' => $clientesWithLocales,
        ]);

    }

    // GET programacionIG/mensual/pdf/{mes}
    public function descargarProgramaMensual($annoMesDia){
        $fecha = explode('-', $annoMesDia);
        $anno = $fecha[0];
        $mes  = $fecha[1];

        // inventarios que se desean mostrar
        $inventarios = Inventarios::with([
            //'local',
            'local.cliente',
            'local.formatoLocal',
            'local.direccion.comuna.provincia.region',
            'nominaDia',
            'nominaNoche'
        ])
        ->whereRaw("extract(year from fechaProgramada) = ?", [$anno])
        ->whereRaw("extract(month from fechaProgramada) = ?", [$mes])
        ->join('locales', 'locales.idLocal', '=', 'inventarios.idLocal')
        ->orderBy('fechaProgramada', 'ASC')
        ->orderBy('locales.stock', 'DESC')
        ->get();

        $inventariosHeader = ['Fecha', 'Cliente', 'CECO', 'Local', 'Región', 'Comuna', 'Stock', 'Dotación Total'];
        $inventariosArray = array_map(function($inventario){
            return [
                $inventario['fechaProgramada'],
                $inventario['local']['cliente']['nombreCorto'],
                $inventario['local']['numero'],
                $inventario['local']['nombre'],
                $inventario['local']['direccion']['comuna']['provincia']['region']['numero'],
                $inventario['local']['direccion']['comuna']['nombre'],
                $inventario['local']['stock'],
                $inventario['dotacionAsignadaTotal'],
            ];
        }, $inventarios->toArray());

        // Nuevo archivo
        $workbook = new PHPExcel();  // workbook
        $sheet = $workbook->getActiveSheet();
        // agregar datos
        $sheet->setCellValue('A1', 'Programación mensual');
        $sheet->fromArray($inventariosHeader, NULL, 'A4');
        $sheet->fromArray($inventariosArray,  NULL, 'A5');

        // guardar
        $excelWritter = PHPExcel_IOFactory::createWriter($workbook, "Excel2007");
        $randomFileName = "pmensual_".md5(uniqid(rand(), true)).".xlxs";
        $excelWritter->save($randomFileName);

        // entregar la descarga al usuario
        return response()->download($randomFileName, "programacion $annoMesDia.xlsx");
    }

    // GET programacionIG/semanal
    public function showSemanal(){
        // validar de que el usuario tenga los permisos
        $user = Auth::user();
        if(!$user || !$user->can('programaInventarios_ver'))
            return view('errors.403');

        // Clientes
        $clientes  = Clientes::all();
        // Captadores
        $rolCaptador = Role::where('name', 'Captador')->first();
        $captadores = $rolCaptador!=null? $rolCaptador->users : '[]';
        // Supervisores
        $rolSupervisor = Role::where('name', 'Supervisor')->first();
        $supervisores = $rolSupervisor!=null? $rolSupervisor->users : '[]';
        // Lideres
        $rolLider = Role::where('name', 'Lider')->first();
        $lideres = $rolLider!=null? $rolLider->users : '[]';

        // buscar la mayor fechaProgramada en los iventarios
        return view('operacional.programacionIG.programacionIG-semanal', [
            'puedeModificarInventarios' => $user->can('programaInventarios_modificar')? "true":"false",
            'clientes' => $clientes,
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
