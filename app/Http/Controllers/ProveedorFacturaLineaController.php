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
            ->join('movimientos_pago_proveedors', 'movimientos_pago_proveedors.id', '=', 'proyecto_sucursal_lineas.movimientos_pago_proveedor_id')
            ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.domicilio as domicilio','proveedors.id as proveedor_id','proveedors.nombre as proveedor',
            'proveedors.rfc as rfc','municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais',
            'presupuestos.id as presupuesto_id','presupuestos.nombre as presupuesto', 'productos.id as producto_id', 'productos.nombre as producto', 'agrupador_facturas.nombre as agrupador', 
            'terminos_pago_clientes.nombre as terminos','estatus_linea_clientes.nombre as estatus','tipos_productos.nombre as tipo','proyecto_sucursal_lineas.fecha_mov as fecha',
            'movimientos_pago_proveedors.secuencia as secuencia','proyecto_sucursal_lineas.importe_proveedor as cxp','proyecto_sucursal_lineas.id as mov_id','movimientos_pago_proveedors.valor_proveedor as porcentaje')
            ->where('presupuestos.id', '=',$id)
            ->where('proyecto_sucursal_lineas.es_facturable', '=',1)
            ->where('proyecto_sucursal_lineas.importe_proveedor', '>', 0)
            ->where('proyecto_sucursal_lineas.proveedor_factura_id', '=', NULL)
            ->get();

        $presupuesto = DB::table('presupuestos')
            ->where('id','=',$id)
            ->first();

        $proveedor = Proveedor::where('id','=',$presupuesto->proveedor_id)->first();

        $proveedor_id = $proveedor->id;
        $presupuesto_id = $presupuesto->id;
        $proveedor = $proveedor->nombre;
        $presupuesto = $presupuesto->nombre;
        $subtotal = $presupuesto->cxp;

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
            ->join('movimientos_pago_proveedors', 'movimientos_pago_proveedors.id', '=', 'proyecto_sucursal_lineas.movimientos_pago_proveedor_id')
            ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.domicilio as domicilio','proveedors.id as proveedor_id','proveedors.nombre as proveedor',
            'proveedors.rfc as rfc','municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais',
            'presupuestos.id as presupuesto_id','presupuestos.nombre as presupuesto', 'productos.id as producto_id', 'productos.nombre as producto', 'agrupador_facturas.nombre as agrupador', 
            'terminos_pago_clientes.nombre as terminos','estatus_linea_clientes.nombre as estatus','tipos_productos.nombre as tipo','proyecto_sucursal_lineas.fecha_mov as fecha',
            'movimientos_pago_proveedors.secuencia as secuencia','proyecto_sucursal_lineas.importe_proveedor as cxp','proyecto_sucursal_lineas.id as mov_id','movimientos_pago_proveedors.valor_proveedor as porcentaje')
            ->where('presupuestos.id', '=',$idp)
            ->where('proyecto_sucursal_lineas.es_facturable', '=',1)
            ->where('proyecto_sucursal_lineas.importe_proveedor', '>', 0)
            ->where('proyecto_sucursal_lineas.proveedor_factura_id', '=', NULL)
            ->get();
        
        $subtotal = 0;
        $impuestos = 0;
        $total = 0;

        $fact = new ProveedorFactura();
        $fact->proveedor_id = $idc;
        $fact->presupuesto_id = $idp;
        $fact->fecha = now();
        $fact->subtotal = $subtotal;
        $fact->impuestos = 0;
        $fact->total = $subtotal;
        $fact->es_activo = 1;
        $fact->save();

        foreach ($movimientos as $row){
            $sel = "sel".$row->mov_id;
            if ($request->$sel){
                $linea = new ProveedorFacturaLinea();
                $linea->proveedor_factura_id = $fact->id;
                $linea->proyecto_sucursal_linea_id = $row->mov_id;
                $linea->subtotal = $row->cxp;
                $linea->impuestos = 0;
                $linea->total = $row->cxp;
                $linea->fecha = today();
                $linea->save();

                $subtotal += $row->cxp;
                $total += $row->cxp;

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
                    'cxp'=> $presupuesto->cxp - $row->cxp,
                ]);
            } 
        };

        $facturas = DB::table('proveedor_facturas')
            ->where('id','=',$fact->id)
            ->update([
            'subtotal'=> $subtotal,
            'impuestos'=> $impuestos,
            'total'=> $total,
        ]);

        $inf = 1;
        session()->flash('Exito','La factura fue creada con éxito...');
        return redirect()->route('factproveedores')->with('info',$inf);
    }

    public function lineas($id){
        
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
            ->join('movimientos_pago_proveedors', 'movimientos_pago_proveedors.id', '=', 'proyecto_sucursal_lineas.movimientos_pago_proveedor_id')
            ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.domicilio as domicilio','proveedors.id as proveedor_id','proveedors.nombre as proveedor',
            'proveedors.rfc as rfc','municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais',
            'presupuestos.id as presupuesto_id','presupuestos.nombre as presupuesto', 'productos.id as producto_id', 'productos.nombre as producto', 'agrupador_facturas.nombre as agrupador', 
            'terminos_pago_clientes.nombre as terminos','estatus_linea_clientes.nombre as estatus','tipos_productos.nombre as tipo','proyecto_sucursal_lineas.fecha_mov as fecha',
            'movimientos_pago_proveedors.secuencia as secuencia','proyecto_sucursal_lineas.importe_proveedor as cxp','proyecto_sucursal_lineas.id as mov_id','movimientos_pago_proveedors.valor_proveedor as porcentaje')
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
