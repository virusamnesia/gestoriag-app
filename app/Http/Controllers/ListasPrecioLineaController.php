<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\ListasPrecio;
use App\Models\ListasPrecioLinea;
use App\Models\MunicipioContacto;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListasPrecioLineaController extends Controller
{
    public function index($idc,$idl){
        
        $lista = ListasPrecio::where('id',$idl)
        ->first();

        $cliente = Cliente::where('id',$idc)
            ->first();

        $productos_all = Producto::all();
        $municipios = MunicipioContacto::all();

        $productos = DB::table('listas_precio_lineas')
            ->leftJoin('listas_precios', 'listas_precio_lineas.listas_precio_id', '=', 'listas_precios.id')
            ->leftJoin('productos', 'productos.id', '=', 'listas_precios.producto_id')
            ->leftJoin('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
            ->lefjoin('municipio_contactos', 'municipio_contactos.id', '=', 'listas_precios.municipio_contacto_id')
            ->lefjoin('estado_contactos', 'estado_contactos.id', '=', 'municipio_contactos.estado_contacto_id')
            ->select('listas_precio_lineas.*','productos.nombre as producto','municipio_contactos.nombre as municipio', 
            'estado_contactos.nombre as estado', 'tipos_productos.nombre as tipo')
            ->where('listas_precios.id',$idl)
            ->orderBy('productos.nombre')
            ->get();
        
        return view('listasprecio.productos.index', ['lista' => $lista, 'idc' => $idc, 'idl' => $idl, 'productos' => $productos, 'cliente' => $cliente, 'productos_all' => $productos_all, 'municipios' => $municipios]);
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
    public function store(Request $request, $idc, $idl)
    {
        $inf = 1;
        
        $producto = ListasPrecioLinea::where('listas_precio_id',$idl)
            ->where('producto_id',$request->producto)
            ->where('municipio_contacto_id',$request->muinicipio)
            ->first();

        if($producto){
            session()->flash('Error','Producto-Municipio ya registrado en la lista...');
            return redirect()->route('listas.productos',['idc' => $idc, 'idl' => $idl])->with('info',$inf);
        }
        
        $linea = new ListasPrecioLinea();

        $linea->listas_precio_id = $idl;
        $linea->producto_id = $request->producto;
        $linea->municipio_contacto_id = $request->muinicipio;
        $linea->precio = $request->precio;
        $linea->costo = $request->costo;

        $linea->save();

        session()->flash('Exito','Producto agregado a la lista...');
        return redirect()->route('listas.productos',['idc' => $idc, 'idl' => $idl])->with('info',$inf);
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
