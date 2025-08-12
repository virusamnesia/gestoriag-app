<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\productos_proyecto;
use App\Models\Proyecto;
use App\Models\ProyectoLinea;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductosProyectoController extends Controller
{
    public function index($idp,$idc){
        
        $productos =DB::table('productos_proyectos')
            ->join('proyectos', 'proyectos.id', '=', 'productos_proyectos.proyecto_id')
            ->join('productos', 'productos.id', '=', 'productos_proyectos.producto_id')
            ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
            ->select('productos_proyectos.*','productos.nombre as producto','tipos_productos.id as tps_id',
            'proyectos.nombre as proyecto','tipos_productos.nombre as tps_nombre')
            ->where('proyectos.id','=',$idp)
            ->orderBy('productos.nombre')
            ->get();

        $cliente = Cliente::where('id','=',$idc)->first();
        $proyecto = Proyecto::where('id','=',$idp)->first();

        return view('proyecto.producto.index', ['productos' => $productos,'cliente' => $cliente,'proyecto' => $proyecto,'idp' => $idp,'idc' => $idc]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$idp,$idc)
    {
        
        $productos = productos_proyecto::where('proyecto_id','=',$idp)->get();

        foreach ($productos as $row){
            $sel = "sel".$row->id;
            $prec = "prec".$row->id;
            if ($request->$sel){$cotizado = 1;}
            else {$cotizado = 0;}
            $sucursal = DB::table('productos_proyectos')
                ->where('productos_proyectos.id','=',$row->id)
                ->update([
                    'cotizado'=> $cotizado,
                    'precio'=> $request->$prec,
                ]
            );
        };

        //Llenado de la tabla de lineas del proyecto para determinar el importe del mismo

        $sucursales =DB::table('sucursales_proyectos')
            ->join('proyectos', 'proyectos.id', '=', 'sucursales_proyectos.proyecto_id')
            ->join('clientes', 'clientes.id', '=', 'sucursales_proyectos.cliente_id')
            ->join('sucursals', 'sucursals.id', '=', 'sucursales_proyectos.sucursal_id')
            ->select('sucursales_proyectos.*','sucursals.nombre as sucursal','sucursals.domicilio as domicilio',
            'clientes.nombre as cliente','clientes.id as cliente_id','sucursals.municipio_contacto_id as municipio_id',
            'proyectos.id as proyecto_id')
            ->where('proyectos.id','=',$idp)
            ->where('sucursales_proyectos.cotizado','=',true)
            ->orderBy('sucursals.nombre')
            ->get();

        $productos =DB::table('productos_proyectos')
            ->join('proyectos', 'proyectos.id', '=', 'productos_proyectos.proyecto_id')
            ->join('productos', 'productos.id', '=', 'productos_proyectos.producto_id')
            ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
            ->Join('terminos_pago_clientes', 'productos.terminos_pago_cliente_id', '=', 'terminos_pago_clientes.id')
            ->Join('movimientos_pago_clientes', function (JoinClause $join) {
                $join->on('terminos_pago_clientes.id', '=', 'movimientos_pago_clientes.terminos_pago_cliente_id')
                ->where('movimientos_pago_clientes.secuencia', '=', 1);
            })
            ->select('productos_proyectos.*','productos.nombre as producto','tipos_productos.id as tps_id',
            'proyectos.nombre as proyecto','tipos_productos.nombre as tps_nombre', 'terminos_pago_clientes.id as terminos',
            'movimientos_pago_clientes.estatus_linea_cliente_id as estatus')
            ->where('proyectos.id','=',$idp)
            ->where('productos_proyectos.cotizado','=',true)
            ->orderBy('productos.nombre')
            ->get();

        $importe = 0;

        foreach ($sucursales as $suc){
            foreach ($productos as $prod){
                
                $rev = ProyectoLinea::where('sucursal_id','=',$suc->id)
                    ->where('producto_id','=', $prod->id)
                    ->where('proyecto_id','=', $suc->proyecto_id)
                    ->first();

                if ($rev == null){

                    $linea = new ProyectoLinea();

                    $linea->proyecto_id = $idp;
                    $linea->cliente_id = $idc;
                    $linea->sucursal_id = $suc->id;
                    $linea->producto_id = $prod->producto_id;
                    $linea->precio = $prod->precio;
                    $linea->saldocliente = $prod->precio;
                    $linea->costo = 0;
                    $linea->saldoproveedor = 0;
                    $linea->terminos_pago_cliente_id = $prod->terminos;
                    $linea->estatus_linea_cliente_id = 1; 
                    $linea->cxc = 0;
                    $linea->cxp = 0;

                    $linea->save();

                    $importe += $prod->precio;
                };
            };
        };

        $data = [
            'importe' => $importe,
            'saldo' => $importe,
        ];
        
        $proy = DB::table('proyectos')
            ->where('id','=',$idp)
            ->update($data);        
        
        $inf = 1;
        session()->flash('Exito','Las partidas del proyecto se agregaron con éxito...');
        return redirect()->route('proyectos')->with('info',$inf);
        //, ['idp' => $idp,'idc' => $idc]
    }
}
