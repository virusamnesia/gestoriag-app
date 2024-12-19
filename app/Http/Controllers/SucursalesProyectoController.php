<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Importacion;
use App\Models\Producto;
use App\Models\productos_proyecto;
use App\Models\Proyecto;
use App\Models\sucursales_proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SucursalesProyectoController extends Controller
{
    public function index($idp,$idc){
        
        $sucursales =DB::table('sucursales_proyectos')
        ->join('proyectos', 'proyectos.id', '=', 'sucursales_proyectos.proyecto_id')
        ->join('clientes', 'clientes.id', '=', 'sucursales_proyectos.cliente_id')
        ->join('sucursals', 'sucursals.id', '=', 'sucursales_proyectos.sucursal_id')
        ->join('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
        ->join('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
        ->join('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
        ->select('sucursales_proyectos.*','sucursals.nombre as sucursal','sucursals.domicilio as domicilio',
        'clientes.nombre as cliente','clientes.id as cliente_id','municipio_contactos.nombre as municipio',
        'estado_contactos.alias as estado', 'pais_contactos.alias as pais','proyectos.id as proyecto_id')
        ->where('proyectos.id','=',$idp)
        ->orderBy('sucursals.nombre')
        ->get();

        $cliente = Cliente::where('id','=',$idc)->first();
        $proyecto = Proyecto::where('id','=',$idp)->first();
        $tipos = Importacion::all();

        return view('proyecto.sucursal.index', ['sucursales' => $sucursales,'cliente' => $cliente,'proyecto' => $proyecto,'idp' => $idp,'idc' => $idc,'tipos' => $tipos]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$idp,$idc)
    {
        
        $sucursales = sucursales_proyecto::where('proyecto_id','=',$idp)->get();

        foreach ($sucursales as $suc){
            $sel = "sel".$suc->id;
            if ($request->$sel){$cotizado = 1;}
            else {$cotizado = 0;}
            $sucursal = DB::table('sucursales_proyectos')
                ->where('sucursales_proyectos.id','=',$suc->id)
                ->update([
                    'cotizado'=> $cotizado,
                ]
            );
        };

        $cliente = Cliente::where('id','=',$idc);
        $proyecto = Proyecto::where('id','=',$idp);

        $productos = DB::table('productos')
            ->leftJoin('terminos_pago_clientes', 'productos.terminos_pago_cliente_id', '=', 'terminos_pago_clientes.id')
            ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
            ->select('productos.*','terminos_pago_clientes.id as tpc_id','terminos_pago_clientes.nombre as tpc_nombre', 
            'tipos_productos.id as tps_id','tipos_productos.nombre as tps_nombre')
            ->get();
        
            $rev = productos_proyecto::where('proyecto_id', $idp)
            ->first();     
    
            if (!$rev){
                foreach ($productos as $prod){
                    $producto = new productos_proyecto();
                    
                    $producto->proyecto_id = $idp;
                    $producto->producto_id = $prod->id;
                    $producto->precio = 0;
                    $producto->cotizado = False;
        
                    $producto->save();
                };
            };        
        
        $inf = 1;
        session()->flash('Exito','Las sucursales se agregaron con éxito...');
        return redirect()->route('proyectos.productos', ['sucursales' => $sucursales,'cliente' => $cliente,'proyecto' => $proyecto,'idp' => $idp,'idc' => $idc])->with('info',$inf);
    }
}
