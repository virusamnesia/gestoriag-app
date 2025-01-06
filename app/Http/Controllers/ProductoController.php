<?php

namespace App\Http\Controllers;

use App\Models\AgrupadorFactura;
use App\Models\Producto;
use App\Models\TerminosPagoCliente;
use App\Models\TerminosPagoProveedor;
use App\Models\TiposProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index(){
        
        $productos = DB::table('productos')
            ->leftJoin('terminos_pago_clientes', 'productos.terminos_pago_cliente_id', '=', 'terminos_pago_clientes.id')
            ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
            ->join('agrupador_facturas', 'agrupador_facturas.id', '=', 'productos.agrupador_factura_id')
            ->select('productos.*','terminos_pago_clientes.id as tpc_id','terminos_pago_clientes.nombre as tpc_nombre', 
            'tipos_productos.id as tps_id','tipos_productos.nombre as tps_nombre','agrupador_facturas.nombre as agrupador')
            ->get();

        return view('producto.index', ['productos' => $productos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $termclie = TerminosPagoCliente::all();
        $termprov = TerminosPagoProveedor::all();
        $tipos = TiposProducto::all();
        $agrupadores = AgrupadorFactura::all();
        
        return view('producto.create', ['termclie' => $termclie,'agrupadores' => $agrupadores, 'termprov' => $termprov, 'tipos' => $tipos]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $producto = new Producto();

        $producto->alias = $request->clave;
        $producto->nombre = $request->nombre;
        $producto->es_activo = True;
        if ($request->termclie){
            $producto->terminos_pago_cliente_id = $request->termclie;
        }
        $producto->tipos_producto_id = $request->tipos;
        $producto->agrupador_factura_id = $request->agrupador;

        $producto->save();
        $inf = 1;
        session()->flash('Exito','El producto se agregó con éxito...');
        return redirect()->route('productos')->with('info',$inf);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $termclie = TerminosPagoCliente::all();
        $termprov = TerminosPagoProveedor::all();
        $tipos = TiposProducto::all();
        $agrupadores = AgrupadorFactura::all();

        $producto = DB::table('productos')
            ->leftJoin('terminos_pago_clientes', 'productos.terminos_pago_cliente_id', '=', 'terminos_pago_clientes.id')
            ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
            ->join('agrupador_facturas', 'agrupador_facturas.id', '=', 'productos.agrupador_factura_id')
            ->select('productos.*','terminos_pago_clientes.id as tpc_id','terminos_pago_clientes.nombre as tpc_nombre', 
            'tipos_productos.id as tps_id','tipos_productos.nombre as tps_nombre','agrupador_facturas.nombre as agrupador')
            ->where('productos.id',$id)
            ->orderBy('productos.nombre')
            ->get();
        
        return view('producto.show', ['producto' => $producto,'agrupadores' => $agrupadores,'termclie' => $termclie, 'termprov' => $termprov, 'tipos' => $tipos]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $termclie = TerminosPagoCliente::all();
        $termprov = TerminosPagoProveedor::all();
        $tipos = TiposProducto::all();
        $agrupadores = AgrupadorFactura::all();

        $producto = DB::table('productos')
            ->leftJoin('terminos_pago_clientes', 'productos.terminos_pago_cliente_id', '=', 'terminos_pago_clientes.id')
            ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
            ->join('agrupador_facturas', 'agrupador_facturas.id', '=', 'productos.agrupador_factura_id')
            ->select('productos.*','terminos_pago_clientes.id as tpc_id','terminos_pago_clientes.nombre as tpc_nombre', 
            'tipos_productos.id as tps_id','tipos_productos.nombre as tps_nombre','agrupador_facturas.nombre as agrupador')
            ->where('productos.id',$id)
            ->orderBy('productos.nombre')
            ->get();

        return view('producto.edit', ['producto' => $producto,'agrupadores' => $agrupadores, 'termclie' => $termclie, 'termprov' => $termprov, 'tipos' => $tipos]);
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
        $data = [
            'nombre' => $request->nombre,
            'tipos_producto_id' => $request->tipos,
            'agrupador_factura_id' => $request->agrupador,
            'es_activo' => $request->activo,
        ];
        //isset($array('clave'));
        if ($request->clave){
            $data['alias'] = $request->clave;
        }
        if ($request->termclie){
            $data['terminos_pago_cliente_id'] = $request->termclie;
        }
        

        $producto = DB::table('productos')
            ->where('productos.id','=',$id)
            ->update($data);

        $inf = 1;
        session()->flash('Exito','El producto se midificó con éxito...');
        return redirect()->route('productos')->with('info',$inf);
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
