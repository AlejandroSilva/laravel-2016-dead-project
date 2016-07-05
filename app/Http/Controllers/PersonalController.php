<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
// Utils
use Hash;
use Log;
use Carbon\Carbon;
// PHP Excel
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
// Modelos
use App\User;
use App\Role;
use App\Permission;
// Permisos
use Auth;

class PersonalController extends Controller {
    private $userRules = [
        'usuarioRUN' => 'required|unique:users|max:15',
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
    private $permissionRules = [
        'name' => 'required|unique:permissions',
        'description' => 'max:80'

    ];
    private $roleRules = [
        'name' => 'required|unique:roles',
        'description' => 'max:80'

    ];
    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */

    /**
     * ##########################################################
     * Rutas para consumo del API REST (CON autentificacion)
     * ##########################################################
     */
    // GET api/usuarios/buscar
    function api_buscar(Request $request){
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
    function api_nuevoOperador(){
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
    public function api_actualizar($idUsuario){
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
    
    
    /**
     * Funciones para cambio de contraseña
     */
    public function showChangePassword(){
        return view('auth.changePassword');
    }
    public function postChangePassword(){
        $rules = array(
            'password' => 'required',
            'newpassword' => 'required|min:5',
            'repassword' => 'required|same:newpassword'
        );
        $messages = array(
            'required' => 'El campo :attribute es obligatorio.',
            'min' => 'El campo :attribute no puede tener menos de :min carácteres.'
        );
        $validation = Validator::make(Input::all(), $rules, $messages);
        if ($validation->fails())
        {
            return Redirect::to('/user/changePassword')->withErrors($validation)->withInput();
        }
        else{
            if (Hash::check(Input::get('password'), Auth::user()->password)){
                $cliente = Auth::user();
                $cliente->password = Hash::make(Input::get('newpassword'));
                $cliente->save();
                if($cliente->save()){
                    return Redirect::to('/');
                }
                else
                {
                    return Redirect::to('/user/changePassword')->with('flash_message', "No se ha podido guardar la nueva contaseña");
                }
            }
            else
            {
                return Redirect::to('/user/changePassword')->with('flash-message',"La contraseña actual no es correcta");
            }
        }
    }
    /**
     * ##########################################################
     * Funciones para mantenedor de usuarios roles y permisos
     * ##########################################################
     */
    public function showUsuariosRoles(){
        $usuario = Auth::user();
        if(!$usuario || !$usuario->hasRole('Administrador'))
            return view('errors.403');


        $users = User::get();
        $roles = Role::get();

        return view('operacional.usuarios.usuarios', [
            'users' => $users,
            'roles' => $roles
        ]);
    }

    public function api_nuevo_rol($idUsuario, $idRole){
        $usuario = Auth::user();
        if(!$usuario || !$usuario->hasRole('Administrador'))
            return view('errors.403');
        $user = User::find($idUsuario);
        $user->roles()->attach($idRole);
    }

    public function api_delete_rol($idUsuario, $idRole){
        $usuario = Auth::user();
        if(!$usuario || !$usuario->hasRole('Administrador'))
            return view('errors.403');
        $user = User::find($idUsuario);
        $user->detachRole($idRole);
    }
    public function showPermissionsRoles(){
        $usuario = Auth::user();
        if(!$usuario || !$usuario->hasRole('Administrador'))
            return view('errors.403');
        $roles = Role::get();
        $permissions = Permission::get();

        return view('operacional.usuarios.permissions', [
            'permissions' => $permissions,
            'roles' => $roles
        ]);
    }
    public function api_nuevo_permiso($idPermission, $idRole){
        $usuario = Auth::user();
        if(!$usuario || !$usuario->hasRole('Administrador'))
            return view('errors.403');

        $rol = Role::find($idRole);
        $rol->perms()->attach($idPermission);
    }
    public function api_delete_permiso($idPermission, $idRole){
        $usuario = Auth::user();
        if(!$usuario || !$usuario->hasRole('Administrador'))
            return view('errors.403');

        $rol = Role::find($idRole);
        $rol->perms()->detach($idPermission);
    }
    public function showPermissions(){
        $usuario = Auth::user();
        if(!$usuario || !$usuario->hasRole('Administrador'))
            return view('errors.403');
        $permissions = Permission::get();
        return view('operacional.usuarios.mantPermissions',[
            'permissions' => $permissions
        ]);
    }
    public function api_actualizarPermission($idPermission, Request $request){
        $usuario = Auth::user();
        if(!$usuario || !$usuario->hasRole('Administrador'))
            return view('errors.403');
        $permission = Permission::find($idPermission);
        $permissionRules = $this->permissionRules;
        $permissionRules["name"] = "required|unique:permissions,name,$permission->name,name";

        $validator = Validator::make(Input::all(), $permissionRules);

        if($validator->fails()){
            return Redirect::to("admin/permissions")->withErrors($validator, 'error')->withInput();
        }else{
            if($permission){
                if(isset($request->name))
                    $permission->name = $request->name;
                if(isset($request->description))
                    $permission->description = $request->description;
                $permission->save();
                return Redirect::to("admin/permissions");
            }else{
                return response()->json([],404);
            }
        }
    }

    public function api_nuevoPermission(Request $request){
        $usuario = Auth::user();
        if(!$usuario || !$usuario->hasRole('Administrador'))
            return view('errors.403');
        $validator = Validator::make(Input::all(), $this->permissionRules);
        if($validator->fails()){
            return Redirect::to("admin/permissions")->withErrors($validator);
        }else{
            $permission = new Permission();
            $permission->name = $request->name;
            $permission->description = $request->description;
            $result = $permission->save();
            if($result){
                return Redirect::to("admin/permissions");
            }
            else{
                return response()->json([],400);
            }
        }
    }
    public function api_eliminarPermission($idPermission){
        $usuario = Auth::user();
        if(!$usuario || !$usuario->hasRole('Administrador'))
            return view('errors.403');
        $permission = Permission::findOrFail($idPermission);
        if(count($permission->roles->all())==0){
            $permission->delete();
            return Redirect::to("admin/permissions");
        }
        else{
            return Redirect::to("admin/permissions")->withErrors($permission->id, 'errorEliminar')->withInput();
        }
    }

    public function showRoles(){
        $usuario = Auth::user();
        if(!$usuario || !$usuario->hasRole('Administrador'))
            return view('errors.403');
        $roles = Role::get();
        return view('operacional.usuarios.mantRoles',[
            'roles' => $roles
        ]);
    }
    public function api_actualizarRole($idRole, Request $request){
        $usuario = Auth::user();
        if(!$usuario || !$usuario->hasRole('Administrador'))
            return view('errors.403');
        $role = Role::find($idRole);
        $roleRules = $this->roleRules;
        $roleRules["name"] = "required|unique:roles,name,$role->name,name";

        $validator = Validator::make(Input::all(), $roleRules);

        if($validator->fails()){
            return Redirect::to("admin/roles")->withErrors($validator, 'error')->withInput();
        }else{
            if($role){
                if(isset($request->name))
                    $role->name = $request->name;
                if(isset($request->description))
                    $role->description = $request->description;
                $role->save();
                return Redirect::to("admin/roles");
            }else{
                return response()->json([],404);
            }
        }
    }

    public function api_nuevoRole(Request $request){
        $usuario = Auth::user();
        if(!$usuario || !$usuario->hasRole('Administrador'))
            return view('errors.403');
        $validator = Validator::make(Input::all(), $this->roleRules);
        if($validator->fails()){
            return Redirect::to("admin/roles")->withErrors($validator);
        }else{
            $role = new Role();
            $role->name = $request->name;
            $role->description = $request->description;
            $result = $role->save();
            if($result){
                return Redirect::to("admin/roles");
            }
            else{
                return response()->json([],400);
            }
        }
    }
    public function api_eliminarRole($idRole){
        $usuario = Auth::user();
        if(!$usuario || !$usuario->hasRole('Administrador'))
            return view('errors.403');
        $role = Role::findOrFail($idRole);

        if(count($role->users->all())==0){
            $role->delete();
            return Redirect::to("admin/roles");
        }
        else{
            return Redirect::to("admin/roles")->withErrors($role->id, 'errorEliminar')->withInput();
        }
    }
}



