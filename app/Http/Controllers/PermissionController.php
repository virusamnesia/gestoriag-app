<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index(){
        
        $permisos =Permission::all();
        return view('user.permiso.index', ['permisos' => $permisos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
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
            'nombre' => 'required|string|max:225|unique:permissions,name',
        ]);
        
        
        $permiso = Permission::create(['name' => $request->nombre]);

        $inf = 'El permiso se agregó con éxito...';
        session()->flash('Exito',$inf);
        return redirect()->route('permisos')->with('message',$inf);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\role  $role
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
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
            'enombre' => 'required|string|max:225|unique:permissions,name',
        ]);

        $permiso = DB::table('permissions')
            ->where('id','=',$request->id)
            ->update([
            'name'=> $request->enombre,
        ]);

        $inf = 'El permiso se modificó con éxito...';
        session()->flash('Exito',$inf);
        return redirect()->route('permisos')->with('message',$inf);
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
        $inf = 'El permiso se eliminó con éxito...';
        session()->flash('Exito',$inf);
        return redirect()->route('permisos')->with('message',$inf);
    }
}
