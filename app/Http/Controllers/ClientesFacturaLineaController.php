<?php

namespace App\Http\Controllers;

use App\Models\ClientesFactura;
use App\Models\ClientesFacturaLinea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientesFacturaLineaController extends Controller
{
    public function index($id){
        
        $movimientos = DB::table('proyectos')
            ->join('clientes','clientes.id','=','proyectos.cliente_id')
            ->join('proyecto_lineas','proyecto_lineas.proyecto_id','=','proyectos.id')
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
            ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.domicilio as domicilio','clientes.id as cliente_id','clientes.nombre as cliente',
            'clientes.rfc as rfc','municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais',
            'proyectos.id as proyecto_id','proyectos.nombre as proyecto', 'productos.id as producto_id', 'productos.nombre as producto', 'agrupador_facturas.nombre as agrupador', 
            'terminos_pago_clientes.nombre as terminos','estatus_linea_clientes.nombre as estatus','tipos_productos.nombre as tipo','proyecto_sucursal_lineas.fecha_mov as fecha',
            'movimientos_pago_clientes.secuencia as secuencia','proyecto_sucursal_lineas.importe_cliente as cxc','proyecto_sucursal_lineas.id as mov_id','movimientos_pago_clientes.valor_cliente as porcentaje')
            ->where('proyectos.id', '=',$id)
            ->where('proyecto_sucursal_lineas.es_facturable', '=',1)
            ->where('proyecto_sucursal_lineas.importe_cliente', '>', 0)
            ->where('proyecto_sucursal_lineas.clientes_factura_id', '=', NULL)
            ->get();

        $cliente = "";
        $proyecto = "";
        $cliente_id = "";
        $proyecto_id = "";
        $subtotal = 0;
        foreach ($movimientos as $mov){
            $cliente_id = $mov->cliente_id;
            $proyecto_id = $mov->proyecto_id;
            $cliente = $mov->cliente;
            $proyecto = $mov->proyecto;
            $subtotal += $mov->cxc;
        }

        $inf = 1;
        session()->flash('Exito','Selecciona las partidas a facturar...');
        return view('factclientes.lineas.index', ['cliente' => $cliente,'proyecto' => $proyecto,'cliente_id' => $cliente_id,'proyecto_id' => $proyecto_id,'subtotal' => $subtotal,'movimientos' => $movimientos])->with('info',$inf);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $fact = new ClientesFactura();
        $cliente = "";
        $proyecto = "";
        $subtotal = 0;

        foreach ($movimientos as $mov){
            $cliente = $mov->cliente_id;
            $proyecto = $mov->proyecto_id;
            $subtotal += $mov->cxc;
        }

        $fact->cliente_id = $cliente;
        $fact->proyecto_id = $proyecto;
        $fact->fecha = today();
        $fact->subtotal = $subtotal;
        $fact->impuestos = 0;
        $fact->total = $subtotal;
        $fact->es_activo = 1;
        $fact->save();

        

        $factura = DB::table('clientes_facturas')
            ->join('clientes','clientes.id','=','clientes_facturas.cliente_id')
            ->join('proyecto','clientes_facturas.proyecto_id','=','proyectos.id')
            ->select('clientes_facturas.*','proyectos.id as proyecto_id', 'clientes.id as cliente_id',
            'proyectos.nombre as proyecto','clientes.nombre as cliente')
            ->where('proyectos.id', '=',$fact->id)
            ->first();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$idp,$idc)
    {
        $movimientos = DB::table('proyectos')
            ->join('clientes','clientes.id','=','proyectos.cliente_id')
            ->join('proyecto_lineas','proyecto_lineas.proyecto_id','=','proyectos.id')
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
            ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.domicilio as domicilio','clientes.id as cliente_id','clientes.nombre as cliente',
            'clientes.rfc as rfc','municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais',
            'proyectos.id as proyecto_id','proyectos.nombre as proyecto', 'productos.id as producto_id', 'productos.nombre as producto', 'agrupador_facturas.nombre as agrupador', 
            'terminos_pago_clientes.nombre as terminos','estatus_linea_clientes.nombre as estatus','tipos_productos.nombre as tipo','proyecto_sucursal_lineas.fecha_mov as fecha',
            'movimientos_pago_clientes.secuencia as secuencia','proyecto_sucursal_lineas.importe_cliente as cxc','proyecto_sucursal_lineas.id as mov_id','movimientos_pago_clientes.valor_cliente as porcentaje')
            ->where('proyectos.id', '=',$idp)
            ->where('proyecto_sucursal_lineas.es_facturable', '=',1)
            ->where('proyecto_sucursal_lineas.importe_cliente', '>', 0)
            ->where('proyecto_sucursal_lineas.clientes_factura_id', '=', NULL)
            ->get();
        
        $subtotal = 0;
        $impuestos = 0;
        $total = 0;

        $fact = new ClientesFactura();
        $fact->cliente_id = $idc;
        $fact->proyecto_id = $idp;
        $fact->fecha = now();
        $fact->subtotal = $subtotal;
        $fact->impuestos = 0;
        $fact->total = $subtotal;
        $fact->es_activo = 1;
        $fact->save();

        foreach ($movimientos as $row){
            $sel = "sel".$row->mov_id;
            if ($request->$sel){
                $linea = new ClientesFacturaLinea();
                $linea->clientes_factura_id = $fact->id;
                $linea->proyecto_sucursal_linea_id = $row->mov_id;
                $linea->subtotal = $row->cxc;
                $linea->impuestos = 0;
                $linea->total = $row->cxc;
                $linea->fecha = today();
                $linea->save();

                $subtotal += $row->cxc;
                $total += $row->cxc;
            } 
        };

        $factura = DB::table('clientes_facturas')
            ->where('id','=',$fact->id)
            ->update([
            'subtotal'=> $subtotal,
            'impuestos'=> $impuestos,
            'total'=> $total,
        ]);

        $inf = 1;
        session()->flash('Exito','La factura fue creada con éxito...');
        return redirect()->route('factclientes')->with('info',$inf);
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
