<?php
namespace App\Http\Controllers;
//use Illuminate\Support\Facades\Redirect;
//use Illuminate\Support\Facades\Session;
use App\Inventarios;
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
    private $userRules = [
        'usuarioRUN' => 'required|max:15|unique:users',
        'usuarioDV' => 'required|max:1',
        'email' => 'max:60',
        'emailPersonal' => 'max:60',
        'nombre1' => 'required|min:3|max:20',
        'nombre2' => 'max:20',
        'apellidoPaterno' => 'required|min:3|max:20',
        'apellidoMaterno' => 'max:20',
        'fechaNacimiento' => 'required',
        'telefono' => 'max:20',
        'telefonoEmergencia' => 'max:20',
        'direccion' => 'max:150',
        'cutComuna' => 'required|exists:comunas,cutComuna',
        'tipoContrato' => 'max:30',
        'fechaInicioContrato' => 'date',
        'fechaCertificadoAntecedentes' => 'date',
        'banco' => 'max:30',
        'tipoCuenta' => 'max:30',
        'numeroCuenta' => 'max:20',
    ];
    private $rulesMessages = [
        'required' => 'El campo es obligatorio',
        'unique' => 'Ya asignado',
        'max' => 'Debe tener máximo :max caracteres',
        'min' => 'Debe tener mínimo :min caracteres',
    ];
    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */
    function show_personal_index(){
        if(!Auth::user()->can('administrar-personal'))
            return view('errors.403');

        return response()->view('admin.index-personal', []);
    }

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
                ->map(['\App\User', 'formatoTablaMantenedorPersonal']),
            200
        );
    }

    // POST api/usuarios/nuevo-usuario
    function api_usuario_nuevo(){
        // Todo: solo algunos usuarios pueden agregar operadores (crear un permiso?)
        $usuarioAuth = Auth::user();
        $validator = Validator::make(Input::all(), $this->userRules, $this->rulesMessages);
        if($validator->fails()){
            $error = $validator->messages();
            Log::info("[USUARIO:NUEVO_OPERADOR:ERROR] Usuario:'$usuarioAuth->nombre1 $usuarioAuth->apellidoPaterno'($usuarioAuth->id)'. Validador: $error");
            return response()->json($error, 400);
        }else{
            $usuario = User::create( Input::all() );
            Log::info("[USUARIO:NUEVO_OPERADOR:OK] Usuario:'$usuarioAuth->nombre1 $usuarioAuth->apellidoPaterno'($usuarioAuth->id)' ha creado:'$usuario->nombre1 $usuario->apellidoPaterno'($usuario->id)'");
            // se parsea el usuario con el formato "estandar"
            $usuarioActualizado = User::find($usuario->id);
            return response()->json( User::formatoCompleto($usuarioActualizado), 200);
        }
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

    // GET usuario/{idUsuario}
    function api_usuario_get($idUsuario){
        // el usuario existe?
        $user = User::find($idUsuario);
        if(!$user)
            return response()->json(['idUsuario'=>'El usuario no existe'], 400);

        return response()->json( User::formatoCompleto($user) );
    }

    // PUT api/usuario/{idUsuario}
    function api_usuario_actualizar($idUsuario){
        // todo validar permisos
        
        // el usuario existe?
        $user = User::find($idUsuario);
        if(!$user)
            return response()->json(['idUsuario'=>'El usuario no existe'], 400);

        // el "usuarioRUN" no puede repetirse con ningun otro, "excepto el mismo usuario de origen"
        $actualizarRules = $this->userRules;
        $actualizarRules['usuarioRUN'] = "required|max:15|unique:users,usuarioRUN,$idUsuario";

        $validator = Validator::make(Input::all(), $actualizarRules, $this->rulesMessages);
        if($validator->fails()){
            $error = $validator->messages();
            return response()->json($error, 400);
        }

        // todo validar que campos son los que realmente se puede actualizar
        $user->update(Input::all());
        return response()->json(['msg'=>'OK'], 200);
    }

    function api_bloquearUsuario($idUsuario){
        // todo permisos

        $user = User::find($idUsuario);
        if(!$user)
            return response()->json(['idUsuario'=>'El usuario no existe'], 400);
        $user->bloquear();
        return response()->json([], 200);
    }

    function api_cambiarContrasena($idUsuario, Request $request){
        // todo permisos

        $user = User::find($idUsuario);
        if(!$user)
            return response()->json(['idUsuario'=>'El usuario no existe'], 400);
        $user->cambiarContrasena($request->contrasena);
        return response()->json([], 200);
    }
    // GET api/usuario/{rut}/historial-nominas  /* EN DESARROLLO, NO SE OCUPA POR NINGUNA API */
    function api_historial_nominas($idUsuario){
        //$user = User::where('usuarioRUN', $rut)->first();
        $user = User::find($idUsuario);
        if(!$user)
            return response()->json('rut no encontrado');

        $nominas = $user->nominasComoTitular('2000-01-01', '2020-12-12');

        return response()->json([
            'nombre' => $user->nombreCompleto(),
            'nominas' => array_values($nominas->todas->map(function($inv) {
                return [
                    'role' => $inv->cargoUsuario,
                    'url' => "http://sig.seiconsultores.cl/nomina/" . $inv->idNomina,
                    'inventario' => $inv->inventario->local->cliente->nombreCorto . " " . $inv->inventario->local->nombre,
                    'fecha' => $inv->inventario->fechaProgramadaF()
                ];
            })->toArray())
        ]);
    }
    /**
     * ##########################################################
     * API DE INTERACCION CON LA OTRA PLATAFORMA (publicas: SIN autentificacion)
     * ##########################################################
     */
    // GET /api/usuario/{idUsuario}/roles
    function api_getRolesUsuario($idUsuario){
        $usuario = User::find($idUsuario);
        if($usuario){
            return response()->json($usuario->roles, 200);
        }else{
            return response()->json([], 404);
        }
    }
    
    // GET /api/usuarios/descargar-excel
    function excel_descargarTodos(){
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