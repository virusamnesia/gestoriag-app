<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\ClientesFactura;
use App\Models\Proyecto;
use App\Models\ProyectoLinea;
use App\Models\SaldosClientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ClientesFacturaController extends Controller
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
            $facturas = DB::table('clientes_facturas')
                ->join('clientes','clientes.id','=','clientes_facturas.cliente_id')
                ->join('proyectos','proyectos.id','=','clientes_facturas.proyecto_id')
                ->select('clientes_facturas.*','clientes.nombre as cliente','clientes.rfc as rfc','proyectos.nombre as proyecto')
                ->orderBy('clientes_facturas.id')
                ->get();

        
            return view('factclientes.index', ['facturas' => $facturas]);
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
        $proyectos = DB::table('proyectos')
            ->join('clientes','clientes.id','=','proyectos.cliente_id')
            ->select('proyectos.*','clientes.nombre as cliente','clientes.rfc as rfc')
            ->where('proyectos.cxc', '>',0)
            ->get();
        
        return view('factclientes.create', ['proyectos' => $proyectos]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function show($idc, $idl)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function abonar(Request $request,$id){
        
        $factura = ClientesFactura::where('id',$id)->first();

        $saldos = DB::table('saldos_clientes')
            ->join('clientes','clientes.id','=','saldos_clientes.cliente_id')
            ->join('proyectos','saldos_clientes.proyecto_id','=','proyectos.id')
            ->join('productos','saldos_clientes.producto_id','=','productos.id')
            ->join('sucursals','sucursals.id','=','saldos_clientes.sucursal_id')
            ->select('saldos_clientes.*','sucursals.nombre as sucursal','clientes.id as cliente_id','clientes.nombre as cliente',
            'clientes.rfc as rfc','proyectos.id as proyecto_id','proyectos.nombre as proyecto', 'productos.id as producto_id', 'productos.nombre as producto')
            ->where('saldos_clientes.cliente_id', '=',$factura->cliente_id)
            ->where('saldos_clientes.saldo', '>',0)
            ->get();
        
        $subtotal = 0;
        $iva_t = 0;
        $isr_r = 0;
        $iva_r = 0;
        $imp_c = 0;
        
        foreach ($saldos as $row){
            $sel = "sel".$row->id;
            if ($request->$sel){
                
                $mov = DB::table('saldos_clientes')
                    ->where('id','=', $row->id)
                    ->update([
                    'saldo'=> 0,
                    'aplicado'=> $row->total,
                    'clientes_factura_id'=> $id,
                ]);

                $subtotal += $row->subtotal;
                $iva_t += $row->iva_t;
                $isr_r += $row->isr_r;
                $iva_r += $row->iva_r;
                $imp_c += $row->imp_c;
            } 
        }

        if($subtotal > 0){
            $line = DB::table('clientes_facturas')
                ->where('id','=', $id)
                ->update([
                'descuento'=> $subtotal,
                'iva_t'=> $factura->iva_t - $iva_t,
                'isr_r'=> $factura->isr_r - $isr_r,
                'iva_r'=> $factura->iva_r - $iva_r,
                'imp_c'=> $factura->imp_c - $imp_c,
                'total'=> $factura->subtotal - $subtotal + $factura->iva_t - $iva_t - $factura->isr_r + $isr_r - $factura->iva_r + $iva_r - $factura->imp_c + $imp_c,
            ]);    
        }
        
        $inf = 1;
        session()->flash('Exito','La factura con saldos a favor aplicados con éxito...');
        return redirect()->route('factclientes')->with('info',$inf);
    }

    public function cancelar(Request $request){

        //lee las lineas de la factura
        $lineas = DB::table('clientes_facturas')
            ->join('clientes_factura_lineas','clientes_facturas.id','=','clientes_factura_lineas.clientes_factura_id')
            ->join('proyecto_sucursal_lineas','proyecto_sucursal_lineas.id','=','clientes_factura_lineas.proyecto_sucursal_linea_id')
            ->join('proyecto_lineas','proyecto_sucursal_lineas.proyecto_linea_id','=','proyecto_lineas.id')
            ->join('proyectos','proyectos.id','=','proyecto_lineas.proyecto_id')
            ->join('clientes','clientes.id','=','proyectos.cliente_id')
            ->join('productos','proyecto_lineas.producto_id','=','productos.id')
            ->join('sucursals','sucursals.id','=','proyecto_lineas.sucursal_id')
            ->join('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
            ->leftJoin('terminos_pago_clientes', 'proyecto_lineas.terminos_pago_cliente_id', '=', 'terminos_pago_clientes.id')
            ->leftJoin('agrupador_facturas', 'productos.agrupador_factura_id', '=', 'agrupador_facturas.id')
            ->leftJoin('estatus_linea_clientes', 'estatus_linea_clientes.id', '=', 'proyecto_lineas.estatus_linea_cliente_id')
            ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
            ->join('movimientos_pago_clientes', 'movimientos_pago_clientes.id', '=', 'proyecto_sucursal_lineas.movimientos_pago_cliente_id')
            ->select('clientes_factura_lineas.*','clientes.id as cliente_id','clientes.nombre as cliente','proyecto_lineas.id as linea_id',
            'proyectos.id as proyecto_id','proyectos.nombre as proyecto', 'productos.id as producto_id','estatus_linea_clientes.nombre as estatus','proyecto_sucursal_lineas.importe_cliente as cxc',
            'proyecto_sucursal_lineas.id as mov_id','movimientos_pago_clientes.valor_cliente as porcentaje')
            ->where('clientes_facturas.id', '=', $request->id)
            ->get();

        $factura = ClientesFactura::where('id','=',$request->id)->first();

        $proyecto = Proyecto::where('id','=',$factura->proyecto_id)->first();

        $cliente = Cliente::where('id','=',$factura->cliente_id)->first();

        //reactivar los cxc de las lineas del proyecto
        foreach($lineas as $row){

            $mov = DB::table('proyecto_sucursal_lineas')
                ->where('id','=', $row->mov_id)
                ->update([
                'proveedor_factura_id'=> NULL,
            ]);

            $line = ProyectoLinea::where('id',$row->linea_id)->first();

            $mov = DB::table('proyecto_lineas')
                ->where('id','=', $line->id)
                ->update([
                'cxc'=> $line->cxc + $row->total,
            ]);

            $mov = DB::table('proyectos')
                ->where('id','=', $proyecto->id)
                ->update([
                'cxc'=> $proyecto->cxc + $row->total,
            ]);
        }
        //reactivar los saldos del cliente
        $saldos = SaldosClientes::where('clientes_factura_id','=', $factura->id)->get();   
        
        foreach($saldos as $row){
            $mov = DB::table('saldos_clientes')
                ->where('d','=', $row->id)
                ->update([
                'saldo'=> $row->total,
                'aplicado'=> 0,
                'clientes_factura_id'=> NULL,
            ]);
        }
            
        //actualziar el estatua de la factura
        $mov = DB::table('clientes_facturas')
            ->where('id','=', $factura->id)
            ->update([
            'es_activo'=> 0,
        ]);

        $inf = 1;
        session()->flash('Exito','La factura cancelada con éxito...');
        return redirect()->route('factclientes')->with('info',$inf);
    }
    
}
