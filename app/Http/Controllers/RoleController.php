<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(){
        $roles =Role::all();
        return view('user.rol.index', ['roles' => $roles]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.rol.create');
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
            'nombre' => 'required|string|max:225|unique:roles,name',
        ]);
        
        
        $role = Role::create(['name' => $request->nombre]);

        $inf = 'El rol se agregó con éxito...';
        session()->flash('Exito',$inf);
        return redirect()->route('roles')->with('message',$inf);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\role  $role
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $rol = Role::find($id);

        return view('user.rol.show', ['rol' => $rol]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $rol = Role::find($id);

        return view('user.rol.edit', ['rol' => $rol]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validacion = $request->validate([
            'enombre' => 'required|string|max:225',
        ]);

        $role = DB::table('roles')
            ->where('id','=',$request->id)
            ->update([
            'name'=> $request->enombre,
        ]);

        $inf = 'El rol se modificó con éxito...';
        session()->flash('Exito',$inf);
        return redirect()->route('roles')->with('message',$inf);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::find($id);

        $role->delete();
        $inf = 'El rol se eliminó con éxito...';
        session()->flash('Exito',$inf);
        return redirect()->route('roles')->with('message',$inf);
    }

    public function permisos($id)
    {
        $rol = Role::find($id);

        $permisos =DB::table('permissions')
            ->leftJoin('role_has_permissions', function (JoinClause $join) use ($id){
                $join->on('permissions.id', '=', 'role_has_permissions.permission_id')
                ->where('role_has_permissions.role_id', '=', DB::raw("'".$id."'"));
            })
            ->leftJoin('roles','roles.id', '=', 'role_has_permissions.role_id')
            ->select('permissions.*', 'roles.id as role_id', DB::raw('(CASE WHEN roles.id > 0 THEN "checked" ELSE "" END) AS `check`'))
            ->get();

        return view('user.rol.permisos', ['rol' => $rol,'permisos' => $permisos]);
    }

    public function storepermisos(Request $request,$id)
    {

        $role = Role::find($id);
        $permisos = Permission::all();
        foreach ($permisos as $row){
            $sel = "sel".$row->id;
            if ($request->$sel == 1){
                $role->givePermissionTo($row->id);
            }
            else{
                $role->revokePermissionTo($row->id);
            }
        };

        $inf = 'Se asignarón los permisos con éxito...';
        session()->flash('Exito',$inf);
        return redirect()->route('roles')->with('message',$inf);
    }
}
