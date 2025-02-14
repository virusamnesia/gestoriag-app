<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;

class UserController extends Controller
{
    use HasRoles;
    protected $guardName = 'sanctum';
    
    public function index(){
        
        $user = Auth::user()->id;

        $acceso = 8;

        $permisos = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->select('users.name','roles.name as role','roles.id as role_id','permissions.name as permission','permissions.id as permission_id')
            ->where('users.id','=', $user)
            ->get();
        
        $permiso = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->select('users.name','roles.name as role','roles.id as role_id','permissions.name as permission','permissions.id as permission_id')
            ->where('users.id','=', $user)
            ->where('permissions.id','=', $acceso)
            ->first();
        
        if ($permiso){
            $usuarios = DB::table('users')
            ->leftJoin('model_has_roles','model_has_roles.model_id','=','users.id')
            ->leftJoin('roles','roles.id','=','model_has_roles.role_id')
            ->select('users.*','roles.name as rol')
            ->get();


            $roles = Role::all();
            return view('user.index', ['usuarios' => $usuarios,'roles' => $roles]);
        }
        else{
            $inf = 'No cuentas con el permiso de acceso';
            return redirect()->route('dashboard')->with('error',$inf);
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validacion = $request->validate([
            'nombre' => 'required|string|max:225',
            'email' => 'required|email',
        ]);
        
        $password = 'gestoriag';
        $hashedPassword = Hash::make($password);
        
        $usuario = new User();

        $usuario->name = $request->nombre;
        $usuario->email = $request->email;
        $usuario->password = $hashedPassword;

        $usuario->save();
        $inf = 'El usuario se agregó con éxito...';
        session()->flash('Exito',$inf);
        return redirect()->route('usuarios')->with('message',$inf);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $usuario = User::find($id);

        return view('user.show', ['usuario' => $usuario]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $usuario = User::find($id);

        return view('user.edit', ['usuario' => $usuario]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validacion = $request->validate([
            'nombre' => 'required|string|max:225',
            'email' => 'required|email',
        ]);

        $usuario = DB::table('users')
            ->where('users.id','=',$id)
            ->update([
            'name'=> $request->nombre,
            'email'=> $request->email,
        ]);

        $inf = 'El usuario se modificó con éxito...';
        session()->flash('Exito','El usuario se modificó con éxito...');
        return redirect()->route('usuarios')->with('message',$inf);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $usuario = User::find($id);

        $usuario->delete();
        $inf = 'El usuario se eliminó con éxito...';
        session()->flash('Exito',$inf);
        return redirect()->route('usuarios')->with('message',$inf);
    }

    public function reset($id)
    {
        $password = 'gestoriag';
        $hashedPassword = Hash::make($password);
        $usuario = DB::table('users')
            ->where('users.id','=',$id)
            ->update([
            'password'=> $hashedPassword ,
        ]);

        $inf = 'La contraseña se reestableció con éxito...';
        session()->flash('Exito',$inf);
        return redirect()->route('usuarios')->with('message',$inf);
    }

    public function rol(Request $request)
    {
        $validacion = $request->validate([
            'rol' => 'required',
        ]);
        
        $usuario = User::find($request->id);

        
        $usuario->assignRole($request->rol);

        $inf = 'Se agregó el rol con éxito...';
        session()->flash('Exito',$inf);
        return redirect()->route('usuarios')->with('message',$inf);
    }
}
