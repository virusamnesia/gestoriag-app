<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\ListasPrecio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListasPrecioController extends Controller
{
    public function index($id){
        if ($id == 0){
            $listas = DB::table('listas_precios')
            ->leftJoin('clientes', 'listas_precios.cliente_id', '=', 'clientes.id')
            ->select('listas_precios.*','clientes.id as cliente_id','clientes.nombre as cliente')
            ->get();

            $cliente = [
                'id' => 0,
            ];
        }
        else{
            $listas = DB::table('listas_precios')
            ->leftJoin('clientes', 'listas_precios.cliente_id', '=', 'clientes.id')
            ->select('listas_precios.*','clientes.id as cliente_id','clientes.nombre as cliente')
            ->where('clientes.id',$id)
            ->get();

            $cliente = DB::table('clientes')
            ->where('id',$id)
            ->get();
        }
       
        return view('listasprecio.index', ['listas' => $listas, 'cliente' => $cliente, 'id' => $id]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $clientes = Cliente::all();

        $listas = ListasPrecio::where('cliente_id',$id)
            ->where('final','is not null')
            ->first();

        $lisclie = 'ninguna';
        
        if ($listas){
            $lisclie = $listas->nombre;
        }

        $mensaje = 'El cliente tiene vigente la lista: '.$lisclie;

        if ($lisclie != 'ninguna'){
            session()->flash('Advertencia',$mensaje);
        }
        
        return view('listasprecio.create', ['clientes' => $clientes,'id' => $id, 'lisclie' => $lisclie]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $listas = ListasPrecio::where('cliente_id',$id)
            ->where('final','is not null')
            ->first();
        
        if ($listas){
            $listab = DB::table('listas_precios')
            ->where('listas_precios.id','=',$listas->id)
            ->update([
                'final' => today()-1,
            ]);
        } 
            
        $lista = new ListasPrecio();

        $lista->alias = $request->clave;
        $lista->nombre = $request->nombre;
        $lista->cliente_id = $request->cliente;
        $lista->inicio = today();

        $lista->save();
        $inf = 1;

        $data = ListasPrecio::latest('id')->first();
        $idl = $data->id;

        session()->flash('Exito','Agrega productos a la lista...');
        return redirect()->route('listas.productos',['idc' => $id, 'idl' => $idl])->with('info',$inf);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function show($idc, $idl)
    {
        $lista = ListasPrecio::where('id',$idl)
            ->first();

        $cliente = Cliente::where('id',$idc)
            ->first();

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
        
        return view('listasprecio.show', ['lista' => $lista, 'idc' => $idc, 'idl' => $idl, 'productos' => $productos, 'cliente' => $cliente]);
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
    
}
