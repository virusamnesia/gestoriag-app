<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
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

        $proyecto = DB::table('proyectos')
            ->where('id','=',$id)
            ->first();

        $cliente = Cliente::where('id','=',$proyecto->cliente_id)->first();

        $cliente_id = $cliente->id;
        $proyecto_id = $proyecto->id;
        $cliente = $cliente->nombre;
        $proyecto = $proyecto->nombre;
        $subtotal = $proyecto->cxc;

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

                $mov = DB::table('proyecto_sucursal_lineas')
                    ->where('id','=', $row->mov_id)
                    ->update([
                    'clientes_factura_id'=> $fact->id,
                    'fecha_factura'=> $fact->fecha,
                ]);

                $line = DB::table('proyecto_lineas')
                    ->where('id','=', $row->id)
                    ->update([
                    'cxc'=> 0,
                ]);

                $proyecto = DB::table('proyectos')
                    ->where('id','=',$idp)
                    ->first();

                $proy = DB::table('proyectos')
                    ->where('id','=', $proyecto->id)
                    ->update([
                    'cxc'=> $proyecto->cxc - $row->cxc,
                ]);
            } 
        };

        $facturas = DB::table('clientes_facturas')
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

    public function lineas($id){
        
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
            ->where('proyecto_sucursal_lineas.clientes_factura_id', '=', $id)
            ->get();

        $factura = DB::table('clientes_facturas')
            ->where('id','=',$id)
            ->first();

        $proyecto = DB::table('proyectos')
            ->where('id','=',$factura->proyecto_id)
            ->first();

        $cliente = Cliente::where('id','=',$factura->cliente_id)->first();

        $cliente_id = $cliente->id;
        $proyecto_id = $proyecto->id;
        $cliente = $cliente->nombre;
        $proyecto = $proyecto->nombre;
        $subtotal = $factura->subtotal;

        return view('factclientes.lineas.lineas', ['factura' => $factura,'cliente' => $cliente,'proyecto' => $proyecto,'cliente_id' => $cliente_id,'proyecto_id' => $proyecto_id,'subtotal' => $subtotal,'movimientos' => $movimientos])->with('info',$inf);
    }

    public function odooDetalle($id){
        
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
            'proyectos.id as proyecto_id','proyectos.nombre as proyecto', 'productos.id as producto_id', 'productos.nombre as producto', 'productos.alias as clave', 'agrupador_facturas.nombre as agrupador', 
            'terminos_pago_clientes.nombre as terminos','estatus_linea_clientes.nombre as estatus','tipos_productos.nombre as tipo','proyecto_sucursal_lineas.fecha_mov as fecha',
            'movimientos_pago_clientes.secuencia as secuencia','proyecto_sucursal_lineas.importe_cliente as cxc','proyecto_sucursal_lineas.id as mov_id','movimientos_pago_clientes.valor_cliente as porcentaje')
            ->where('proyecto_sucursal_lineas.clientes_factura_id', '=', $id)
            ->get();

        $factura = DB::table('clientes_facturas')
            ->where('id','=',$id)
            ->first();

        $proyecto = DB::table('proyectos')
            ->where('id','=',$factura->proyecto_id)
            ->first();

        $cliente = Cliente::where('id','=',$factura->cliente_id)->first();

        $cliente_id = $cliente->id;
        $proyecto_id = $proyecto->id;
        $cliente = $cliente->nombre;
        $proyecto = $proyecto->nombre;
        $subtotal = $factura->subtotal;

        return view('factclientes.lineas.odoodetalle', ['factura' => $factura,'cliente' => $cliente,'proyecto' => $proyecto,'cliente_id' => $cliente_id,'proyecto_id' => $proyecto_id,'subtotal' => $subtotal,'movimientos' => $movimientos]);
    }

    public function odooAgrupador($id){
        
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
            ->select('agrupador_facturas.nombre as agrupador', DB::raw('sum(proyecto_sucursal_lineas.importe_cliente) AS `cxc`'))
            ->where('proyecto_sucursal_lineas.clientes_factura_id', '=', $id)
            ->groupBy('agrupador_facturas.nombre')
            ->get();

        $factura = DB::table('clientes_facturas')
            ->where('id','=',$id)
            ->first();

        $proyecto = DB::table('proyectos')
            ->where('id','=',$factura->proyecto_id)
            ->first();

        $cliente = Cliente::where('id','=',$factura->cliente_id)->first();

        $cliente_id = $cliente->id;
        $proyecto_id = $proyecto->id;
        $cliente = $cliente->nombre;
        $proyecto = $proyecto->nombre;
        $subtotal = $factura->subtotal;

        return view('factclientes.lineas.odooagrupador', ['factura' => $factura,'cliente' => $cliente,'proyecto' => $proyecto,'cliente_id' => $cliente_id,'proyecto_id' => $proyecto_id,'subtotal' => $subtotal,'movimientos' => $movimientos]);
    }

    public function odootipo($id){
        
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
            ->select('agrupador_facturas.nombre as agrupador', DB::raw('sum(proyecto_sucursal_lineas.importe_cliente) AS `cxc`'))
            ->where('proyecto_sucursal_lineas.clientes_factura_id', '=', $id)
            ->groupBy('agrupador_facturas.nombre')
            ->get();

        $factura = DB::table('clientes_facturas')
            ->where('id','=',$id)
            ->first();

        $proyecto = DB::table('proyectos')
            ->where('id','=',$factura->proyecto_id)
            ->first();

        $cliente = Cliente::where('id','=',$factura->cliente_id)->first();

        $cliente_id = $cliente->id;
        $proyecto_id = $proyecto->id;
        $cliente = $cliente->nombre;
        $proyecto = $proyecto->nombre;
        $subtotal = $factura->subtotal;

        return view('factclientes.lineas.odooagrupador', ['factura' => $factura,'cliente' => $cliente,'proyecto' => $proyecto,'cliente_id' => $cliente_id,'proyecto_id' => $proyecto_id,'subtotal' => $subtotal,'movimientos' => $movimientos]);
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
