<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\ProveedorFactura;
use App\Models\ProveedorFacturaLinea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProveedorFacturaLineaController extends Controller
{
    public function index($id){
        
        $movimientos = DB::table('presupuestos')
            ->join('proveedors','proveedors.id','=','presupuestos.proveedor_id')
            ->join('proyecto_lineas','proyecto_lineas.presupuesto_id','=','presupuestos.id')
            ->join('proyecto_sucursal_lineas','proyecto_lineas.id','=','proyecto_sucursal_lineas.proyecto_linea_id')
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
            ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.domicilio as domicilio','proveedors.id as proveedor_id','proveedors.nombre as proveedor',
            'proveedors.rfc as rfc','municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais',
            'presupuestos.id as presupuesto_id','presupuestos.nombre as presupuesto', 'productos.id as producto_id', 'productos.nombre as producto', 'agrupador_facturas.nombre as agrupador', 
            'terminos_pago_clientes.nombre as terminos','estatus_linea_clientes.nombre as estatus','tipos_productos.nombre as tipo','proyecto_sucursal_lineas.fecha_mov as fecha',
            'movimientos_pago_clientes.secuencia as secuencia','proyecto_sucursal_lineas.importe_proveedor as cxp','proyecto_sucursal_lineas.id as mov_id','movimientos_pago_clientes.valor_proveedor as porcentaje')
            ->where('presupuestos.id', '=',$id)
            ->where('proyecto_sucursal_lineas.es_facturable', '=',1)
            ->where('proyecto_sucursal_lineas.importe_proveedor', '>', 0)
            ->where('proyecto_sucursal_lineas.proveedor_factura_id', '=', NULL)
            ->get();

        $presupuesto = DB::table('presupuestos')
            ->where('id','=',$id)
            ->first();
        $presupuestos = DB::table('presupuestos')
            ->where('id','=',$id)
            ->get();

        $proveedor = Proveedor::where('id','=',$presupuesto->proveedor_id)->first();

        $proveedor_id = $proveedor->id;
        $presupuesto_id = $presupuesto->id;
        $proveedor = $proveedor->nombre;
        $presupuesto = $presupuesto->nombre;
        foreach($presupuestos as $pre){
            $subtotal = $pre->cxp;
        }

        $inf = 1;
        session()->flash('Exito','Selecciona las partidas a facturar...');
        return view('factproveedores.lineas.index', ['proveedor' => $proveedor,'presupuesto' => $presupuesto,'proveedor_id' => $proveedor_id,'presupuesto_id' => $presupuesto_id,'subtotal' => $subtotal,'movimientos' => $movimientos])->with('info',$inf);
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
    public function store(Request $request,$idp,$idc)
    {
        $movimientos = DB::table('presupuestos')
            ->join('proveedors','proveedors.id','=','presupuestos.proveedor_id')
            ->join('proyecto_lineas','proyecto_lineas.presupuesto_id','=','presupuestos.id')
            ->join('proyecto_sucursal_lineas','proyecto_lineas.id','=','proyecto_sucursal_lineas.proyecto_linea_id')
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
            ->join('fiscal_positions','presupuestos.fiscal_position_id','=','fiscal_positions.id')
            ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.domicilio as domicilio','proveedors.id as proveedor_id','proveedors.nombre as proveedor',
            'proveedors.rfc as rfc','municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais',
            'presupuestos.id as presupuesto_id','presupuestos.nombre as presupuesto', 'productos.id as producto_id', 'productos.nombre as producto', 'agrupador_facturas.nombre as agrupador', 
            'terminos_pago_clientes.nombre as terminos','estatus_linea_clientes.nombre as estatus','tipos_productos.nombre as tipo','proyecto_sucursal_lineas.fecha_mov as fecha', 'proyecto_sucursal_lineas.subtotal_proveedor as subtotal_c',
            'movimientos_pago_clientes.secuencia as secuencia','proyecto_sucursal_lineas.importe_proveedor as cxp','proyecto_sucursal_lineas.id as mov_id','movimientos_pago_clientes.valor_proveedor as porcentaje',
            'productos.iva','presupuestos.fiscal_position_id as posicion_id','fiscal_positions.iva_t','fiscal_positions.isr_r','fiscal_positions.iva_r','fiscal_positions.imp_c')
            ->where('presupuestos.id', '=',$idp)
            ->where('proyecto_sucursal_lineas.es_facturable', '=',1)
            ->where('proyecto_sucursal_lineas.importe_proveedor', '>', 0)
            ->where('proyecto_sucursal_lineas.proveedor_factura_id', '=', NULL)
            ->get();
        
        $subtotal = 0;
        $impuestos = 0;
        $iva_t_p = 0;
        $isr_r_p = 0;
        $iva_r_p = 0;
        $imp_c_p = 0;
        $total = 0;

        $fact = new ProveedorFactura();
        $fact->proveedor_id = $idc;
        $fact->presupuesto_id = $idp;
        $fact->fecha = now();
        $fact->subtotal = $subtotal;
        $fact->impuestos = $impuestos;
        $fact->iva_t = $impuestos;
        $fact->isr_r = $impuestos;
        $fact->iva_r = $impuestos;
        $fact->imp_c = $impuestos;
        $fact->total = $total;
        $fact->es_activo = 1;
        $fact->save();

        foreach ($movimientos as $row){
            $sel = "sel".$row->mov_id;
            if ($request->$sel){
                if($row->iva <> 16){
                    $iva_t_linea = $row->subtotal_c * ($row->iva/100);
                    $iva_r_linea = $row->subtotal_c * ($row->iva/100);
                } 
                else{
                    $iva_t_linea = $row->subtotal_c * ($row->iva_t/100);
                    $iva_r_linea = $row->subtotal_c * ($row->iva_r/100);
                }
                $isr_r_linea = $row->subtotal_c * ($row->isr_r/100);
                $imp_c_linea = $row->subtotal_c * ($row->imp_c/100);
                $posicion_id = $row->posicion_id;

                $linea = new ProveedorFacturaLinea();
                $linea->proveedor_factura_id = $fact->id;
                $linea->proyecto_sucursal_linea_id = $row->mov_id;
                
                $linea->subtotal = $row->subtotal_c;
                $linea->iva_t = $iva_t_linea;
                $linea->isr_r = $isr_r_linea;
                $linea->iva_r = $iva_r_linea;
                $linea->imp_c = $imp_c_linea;
                $linea->impuestos = $iva_t_linea - $isr_r_linea - $iva_r_linea - $imp_c_linea;
                $linea->total = $row->subtotal_c + $iva_t_linea - $isr_r_linea - $iva_r_linea - $imp_c_linea;
                $linea->fecha = today();
                $linea->save();

                $subtotal += $row->subtotal_c;
                $iva_t_p += $iva_t_linea;
                $isr_r_p += $isr_r_linea;
                $iva_r_p += $iva_r_linea;
                $imp_c_p += $imp_c_linea;
                $impuestos += $iva_t_linea - $isr_r_linea - $iva_r_linea - $imp_c_linea;
                $total += $row->subtotal_c + $iva_t_linea - $isr_r_linea - $iva_r_linea - $imp_c_linea;

                $mov = DB::table('proyecto_sucursal_lineas')
                    ->where('id','=', $row->mov_id)
                    ->update([
                    'proveedor_factura_id'=> $fact->id,
                ]);

                $line = DB::table('proyecto_lineas')
                    ->where('id','=', $row->id)
                    ->update([
                    'cxp'=> 0,
                ]);

                $presupuesto = DB::table('presupuestos')
                    ->where('id','=',$idp)
                    ->first();

                $proy = DB::table('presupuestos')
                    ->where('id','=', $presupuesto->id)
                    ->update([
                    'cxp'=> $presupuesto->cxp - $row->subtotal_c - $iva_t_linea + $isr_r_linea + $iva_r_linea + $imp_c_linea,
                ]);
            } 
        };

        $facturas = DB::table('proveedor_facturas')
            ->where('id','=',$fact->id)
            ->update([
            'subtotal'=> $subtotal,
            'iva_t'=> $iva_t_p,
            'isr_r'=> $isr_r_p,
            'iva_r'=> $iva_r_p,
            'imp_c'=> $imp_c_p,
            'impuestos'=> $impuestos,
            'total'=> $total,
        ]);

        $inf = 1;
        session()->flash('Exito','La factura fue creada con éxito...');
        return redirect()->route('factproveedores')->with('info',$inf);
    }

    public function lineas($id){
        
        $movimientos = DB::table('proveedor_facturas')
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
            ->select('proveedor_factura_lineas.*','proyecto_lineas.cantidad','sucursals.nombre as sucursal','sucursals.domicilio as domicilio','proveedors.id as proveedor_id','proveedors.nombre as proveedor',
            'proveedors.rfc as rfc','municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais',
            'presupuestos.id as presupuesto_id','presupuestos.nombre as presupuesto', 'productos.id as producto_id', 'productos.nombre as producto', 'agrupador_facturas.nombre as agrupador', 
            'terminos_pago_clientes.nombre as terminos','estatus_linea_clientes.nombre as estatus','tipos_productos.nombre as tipo','proyecto_sucursal_lineas.fecha_mov as fecha',
            'movimientos_pago_clientes.secuencia as secuencia','proyecto_sucursal_lineas.importe_proveedor as cxp','proyecto_sucursal_lineas.id as mov_id','movimientos_pago_clientes.valor_proveedor as porcentaje')
            ->where('proyecto_sucursal_lineas.proveedor_factura_id', '=', $id)
            ->get();

        $factura = DB::table('proveedor_facturas')
            ->where('id','=',$id)
            ->first();

        $presupuesto = DB::table('presupuestos')
            ->where('id','=',$factura->presupuesto_id)
            ->first();

        $proveedor = proveedor::where('id','=',$factura->proveedor_id)->first();

        $proveedor_id = $proveedor->id;
        $presupuesto_id = $presupuesto->id;
        $proveedor = $proveedor->nombre;
        $presupuesto = $presupuesto->nombre;
        $subtotal = $factura->subtotal;
        $inf = 1;
        return view('factproveedores.lineas.lineas', ['factura' => $factura,'proveedor' => $proveedor,'presupuesto' => $presupuesto,'proveedor_id' => $proveedor_id,'presupuesto_id' => $presupuesto_id,'subtotal' => $subtotal,'movimientos' => $movimientos])->with('info',$inf);
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
