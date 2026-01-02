<?php

namespace App\Http\Controllers;

use App\Models\Presupuesto;
use App\Models\Proveedor;
use App\Models\ProveedorFactura;
use App\Models\ProyectoLinea;
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

    public function cancelar(Request $request){

        //lee las lineas de la factura
        $lineas = DB::table('proveedor_facturas')
            ->join('proveedor_factura_lineas','proveedor_facturas.id','=','proveedor_factura_lineas.proveedor_factura_id')
            ->join('proyecto_sucursal_lineas','proyecto_sucursal_lineas.id','=','proveedor_factura_lineas.proyecto_sucursal_linea_id')
            ->join('proyecto_lineas','proyecto_sucursal_lineas.proyecto_linea_id','=','proyecto_lineas.id')
            ->join('presupuestos','presupuestos.id','=','proyecto_lineas.presupuesto_id')
            ->join('proyectos','proyectos.id','=','proyecto_lineas.proyecto_id')
            ->join('proveedors','proveedors.id','=','presupuestos.proveedor_id')
            ->join('clientes','clientes.id','=','proyectos.cliente_id')
            ->join('productos','proyecto_lineas.producto_id','=','productos.id')
            ->join('sucursals','sucursals.id','=','proyecto_lineas.sucursal_id')
            ->join('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
            ->leftJoin('terminos_pago_clientes', 'proyecto_lineas.terminos_pago_cliente_id', '=', 'terminos_pago_clientes.id')
            ->leftJoin('agrupador_facturas', 'productos.agrupador_factura_id', '=', 'agrupador_facturas.id')
            ->leftJoin('estatus_linea_clientes', 'estatus_linea_clientes.id', '=', 'proyecto_lineas.estatus_linea_cliente_id')
            ->leftJoin('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
            ->leftJoin('movimientos_pago_clientes', 'movimientos_pago_clientes.id', '=', 'proyecto_sucursal_lineas.movimientos_pago_cliente_id')
            ->select('proveedor_factura_lineas.*','proveedors.id as proveedor_id','proveedors.nombre as proveedor','proyecto_lineas.id as linea_id',
            'presupuestos.id as presupuesto_id','presupuestos.nombre as presupuesto', 'productos.id as producto_id','estatus_linea_clientes.nombre as estatus','proyecto_sucursal_lineas.importe_cliente as cxc',
            'proyecto_sucursal_lineas.id as mov_id','movimientos_pago_clientes.valor_cliente as porcentaje')
            ->where('clientes_facturas.id', '=', $request->id)
            ->get();

        $factura = ProveedorFactura::where('id','=',$request->id)->first();

        $presupuesto = Presupuesto::where('id','=',$factura->presupuesto_id)->first();

        $proveedor = Proveedor::where('id','=',$factura->proveedor_id)->first();

        //reactivar los cxc de las lineas del proyecto
        foreach($lineas as $row){

            $mov = DB::table('proyecto_sucursal_lineas')
                ->where('id','=', $row->mov_id)
                ->update([
                'cliente_factura_id'=> NULL,
            ]);

            $line = ProyectoLinea::where('id',$row->linea_id)->first();

            $mov = DB::table('proyecto_lineas')
                ->where('id','=', $line->id)
                ->update([
                'cxp'=> $line->cxp + $row->total,
            ]);

            $mov = DB::table('presupuestos')
                ->where('id','=', $presupuesto->id)
                ->update([
                'cxp'=> $presupuesto->cxp + $row->total,
            ]);
        }
            
        //actualziar el estatua de la factura
        $mov = DB::table('proveedor_facturas')
            ->where('id','=', $factura->id)
            ->update([
            'es_activo'=> 0,
        ]);

        $inf = 1;
        session()->flash('Exito','La factura cancelada con éxito...');
        return redirect()->route('factproveedores')->with('info',$inf);
    }
    
}
