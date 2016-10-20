<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Redirect;
// Utils
use Hash;
use Log;
// Modelos
use App\User;
use App\Role;
use App\Permission;
// Permisos
use Auth;

// SI OCURREN PROBLEMAS DE DEPENDENCIA ACA, REVISAR "PersonalController", LOS METODOS SE EXTRAJERON DE ALLA

class AuthController extends Controller {
    private $permissionRules = [
        'name' => 'required|unique:permissions',
        'description' => 'max:80'

    ];
    private $roleRules = [
        'name' => 'required|unique:roles',
        'description' => 'max:80'

    ];

    /**
     * VISTAS
     */

    // GET auth/login
    function show_Login(){
        return view('auth.login');
    }

    // GET auth/logout
    function show_Logout(){
        // destruir la session
        Auth::logout();
        return Redirect::to('/');
    }


    /**
     * ##########################################################
     * Funciones para el cambio de contraseñas
     * ##########################################################
     */
    // GET user/changePassword
    public function show_changePassword(){
        return view('auth.changePassword');
    }
    // POST user/changePassword
    public function post_change_password(){
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
    //GET admin/usuarios-roles
    public function show_usuarios_roles(){
        if(!Auth::user()->can('administar-permisos'))
            return view('errors.403');

        return view('operacional.usuarios.usuarios', [
            'users' => User::get(),
            'roles' => Role::get()
        ]);
    }

    // GET admin/permissions-roles
    public function show_permissions_roles(){
        if(!Auth::user()->can('administar-permisos'))
            return view('errors.403');

        return view('operacional.usuarios.permissions', [
            'permissions' => Permission::get(),
            'roles' => Role::get()
        ]);
    }

    //GET admin/permissions
    public function show_permissions(){
        if(!Auth::user()->can('administar-permisos'))
            return view('errors.403');

        return view('operacional.usuarios.mantPermissions',[
            'permissions' => Permission::get()
        ]);
    }

    // GET admin/roles
    public function show_roles(){
        if(!Auth::user()->can('administar-permisos'))
            return view('errors.403');

        return view('operacional.usuarios.mantRoles',[
            'roles' => Role::get()
        ]);
    }

    // POST api/usuario/{idUsuario}/role/{idRole}
    public function api_nuevo_rol($idUsuario, $idRole){
        if(!Auth::user()->can('administar-permisos'))
            return view('errors.403');

        $user = User::find($idUsuario);
        $user->roles()->attach($idRole);
        return response()->json([]);
    }

    // POST api/usuario/{idUsuario}/role/{idRole}
    public function api_delete_rol($idUsuario, $idRole){
        if(!Auth::user()->can('administar-permisos'))
            return view('errors.403');

        $user = User::find($idUsuario);
        $user->detachRole($idRole);
        return response()->json([]);
    }

    // POST api/permission/{idPermission}/role/{idRole}
    public function api_nuevo_permiso($idPermission, $idRole){
        if(!Auth::user()->can('administar-permisos'))
            return view('errors.403');

        $rol = Role::find($idRole);
        $rol->perms()->attach($idPermission);
        return response()->json([]);
    }

    // DELETE permission/{idPermission}/roles/{idRole}
    public function api_delete_permiso($idPermission, $idRole){
        if(!Auth::user()->can('administar-permisos'))
            return view('errors.403');

        $rol = Role::find($idRole);
        $rol->perms()->detach($idPermission);
        return response()->json([]);
    }

    // PUT permission/{idPermission}/editar
    public function api_permission_actualizar($idPermission, Request $request){
        if(!Auth::user()->can('administar-permisos'))
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

    // POST permission/nuevo
    public function api_permission_nuevo(Request $request){
        if(!Auth::user()->can('administar-permisos'))
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

    // DELETE permission/{idPermission}
    public function api_permission_eliminar($idPermission){
        if(!Auth::user()->can('administar-permisos'))
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

    // PUT api/role/{idRole}
    public function api_actualizarRol($idRole, Request $request){
        if(!Auth::user()->can('administar-permisos'))
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

    // POST api/roles
    public function api_nuevoRol(Request $request){
        if(!Auth::user()->can('administar-permisos'))
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

    // DELETE api/role/{idRole}
    public function api_eliminarRol($idRole){
        if(!Auth::user()->can('administar-permisos'))
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