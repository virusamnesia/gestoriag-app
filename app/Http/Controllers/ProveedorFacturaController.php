<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProveedorFacturaController extends Controller
{
    public function index(){
        $user = Auth::user()->id;

        $acceso = 7;

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
            $facturas = DB::table('proveedor_facturas')
                ->join('proveedors','proveedors.id','=','proveedor_facturas.proveedor_id')
                ->join('presupuestos','presupuestos.id','=','proveedor_facturas.presupuesto_id')
                ->select('proveedor_facturas.*','proveedors.nombre as proveedor','proveedors.rfc as rfc','presupuestos.nombre as presupuesto')
                ->orderBy('proveedor_facturas.id')
                ->get();

        
            return view('factproveedores.index', ['facturas' => $facturas]);
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
        $presupuestos = DB::table('presupuestos')
            ->join('proveedors','proveedors.id','=','presupuestos.proveedor_id')
            ->select('presupuestos.*','proveedors.nombre as proveedor','proveedors.rfc as rfc')
            ->where('presupuestos.cxp', '>',0)
            ->get();
        
        return view('factproveedores.create', ['presupuestos' => $presupuestos]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function show(top50 $top50)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function edit(top50 $top50)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, top50 $top50)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function destroy(top50 $top50)
    {
        //
    }
    
}
