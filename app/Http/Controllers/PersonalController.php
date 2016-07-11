<?php
namespace App\Http\Controllers;
//use Illuminate\Support\Facades\Redirect;
//use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
// Utils
use Log;
use Carbon\Carbon;
// PHP Excel
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
// Modelos
use App\User;
use App\Role;
// Permisos
use Auth;

class PersonalController extends Controller {
    /**
     * ##########################################################
     * Rutas para consumo del API REST (CON autentificacion)
     * ##########################################################
     */
    // GET api/usuarios/buscar
    function api_usuarios_buscar(Request $request){
        $query = User::with([]);

        // buscar por RUN?
        $run = $request->query('run');
        if(isset($run)){
            $query->where('usuarioRUN', $run);
        }
        
        $usuarios = $query->get();
        return response()->json(
            $usuarios
                ->sortBy('id')
                ->map(['\App\User', 'formatoCompleto']), 
            200
        );
    }

    // POST api/usuarios/nuevo-operador
    function api_operador_nuevo(){
        // Todo: solo algunos usuarios pueden agregar operadores (crear un permiso?)
        $usuarioAuth = Auth::user();
        $validator = Validator::make(Input::all(), $this->userRules);
        if($validator->fails()){
            $error = $validator->messages();
            Log::info("[USUARIO:NUEVO_OPERADOR:ERROR] Usuario:'$usuarioAuth->nombre1 $usuarioAuth->apellidoPaterno'($usuarioAuth->id)'. Validador: $error");
            return response()->json($error, 400);
        }else{
            $usuario = User::create( Input::all() );
            $rolOperador = Role::where('name', 'Operador')->first();
            $usuario->attachRole($rolOperador);     // attach llama a save (?)
            Log::info("[USUARIO:NUEVO_OPERADOR:OK] Usuario:'$usuarioAuth->nombre1 $usuarioAuth->apellidoPaterno'($usuarioAuth->id)' ha creado:'$usuario->nombre1 $usuario->apellidoPaterno'($usuario->id)'");
            // se parsea el usuario con el formato "estandar"
            $usuarioActualizado = User::find($usuario->id);
            return response()->json( User::formatoCompleto($usuarioActualizado), 200);
        }
    }
    
    // PUT api/usuario/{idUsuario}
    public function api_usuario_actualizar($idUsuario){
        return response()->json(['msg'=>'por implementar'], 501);
    }
    
    /**
     * ##########################################################
     * API DE INTERACCION CON LA OTRA PLATAFORMA (publicas: SIN autentificacion)
     * ##########################################################
     */
    // GET /api/usuario/{idUsuario}/roles
    public function api_getRolesUsuario($idUsuario){
        $usuario = User::find($idUsuario);
        if($usuario){
            return response()->json($usuario->roles, 200);
        }else{
            return response()->json([], 404);
        }
    }
    
    // GET /api/usuarios/descargar-excel
    public function excel_descargarTodos(){
        $users = User::all()->map(function($user){
            // codigo, nombre
            return [$user->usuarioRUN, $user->nombreCompleto()];
        })->toArray();

        // crear el archivo
        $workbook = new PHPExcel();  // workbook
        $sheet = $workbook->getActiveSheet();

        // asignar datos
        $sheet->fromArray($users, NULL, 'A1');

        // las columnas deben tener un ancho "dinamico"
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);

        // guardar y descargar el archivo
        $excelWritter = PHPExcel_IOFactory::createWriter($workbook, "Excel2007");
        $ahora = Carbon::now()->format('Y-m-d_h.i.s');
        $randomFileName = "archivos_temporales/usuarios_".$ahora."_".md5(uniqid(rand(), true)).".xlsx";
        $downloadFileName = "usuarios_al_$ahora.xlsx";
        $excelWritter->save($randomFileName);
        return response()->download($randomFileName, $downloadFileName);
    }

}