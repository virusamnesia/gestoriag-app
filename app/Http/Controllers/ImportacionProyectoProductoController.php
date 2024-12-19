<?php

namespace App\Http\Controllers;

use App\Models\ImportacionProyecto;
use App\Models\ImportacionProyectoProducto;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportacionProyectoProductoController extends Controller
{
    public function index($id){
        
        $import = ImportacionProyecto::where('id',$id)->first();
        $productos = Producto::where('es_activo','=',1)->get();

        $productos_all = DB::table('importacion_proyecto_productos')
        ->join('produtos', 'productos.id','=','importacion_proyecto_productos.producto_id')
        ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('importacion_proyecto_productos.*','productos.nombre as producto','tipos_productos.nombre as tipo')
        ->where('importacion_proyecto_productos.importacion_proyecto_id','=',$id)
        ->get();
       
        return view('importaciones/productos', ['import' => $import,'productos_all' => $productos_all,'productos' => $productos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id)
    {
        
        $producto = ImportacionProyectoProducto::where('importacion_proyecto_id','=',$id)->where('producto_id','=',$request->producto)->first();

        if($producto){
            $import = new ImportacionProyectoProducto();

            $import->importacion_proyecto_id = $id;
            $import->producto_id = $request->producto;

            $import->save();
            $inf = 1;
            session()->flash('Exito','Se agregó el producto a la importación...');
        }
        else{
            $inf = 1;
            session()->flash('Error','El producto ya existe la importación...');
        }

        
        return redirect()->route('importaciones.productos',['id' => $id])->with('info',$inf);

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
    public function destroy($idi,$idp)
    {
        $import = DB::table('impotacion_proyecto_productos')
            ->where('id','=',$idp)
            ->where('impotacion_proyecto_id','=',$idi)
            ->delete();

        $inf = 1;

        session()->flash('Exito','Se eliminó el producto la importación...');
        return redirect()->route('importaciones.productos',['id' => $idi])->with('info',$inf);
    }
    
}
