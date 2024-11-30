<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Proyecto;
use App\Models\ProyectoLinea;
use App\Models\TerminosPagoCliente;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProyectoLineaController extends Controller
{
    public function index($id){
        
        $proyecto = DB::table('proyectos')
        ->leftjoin('estados_proyectos', 'estados_proyectos.id', '=', 'proyectos.estados_proyecto_id')
        ->select('proyectos.*','estados_proyectos.nombre as estado')
        ->where('proyectos.id','=',$id)->first();

        $cliente = Cliente::where('id','=',$proyecto->cliente_id)->first();
        
        $lineas =DB::table('proyecto_lineas')
        ->join('proyectos', 'proyectos.id', '=', 'proyecto_lineas.proyecto_id')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->join('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
        ->join('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
        ->join('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
        ->join('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
        ->leftJoin('terminos_pago_clientes', 'proyecto_lineas.terminos_pago_cliente_id', '=', 'terminos_pago_clientes.id')
        ->leftJoin('terminos_pago_proveedors', 'terminos_pago_proveedors.id', '=', 'proyecto_lineas.terminos_pago_proveedor_id')
        ->leftJoin('estatus_linea_clientes', 'estatus_linea_clientes.id', '=', 'proyecto_lineas.estatus_linea_cliente_id')
        ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.domicilio as domicilio',
        'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais','proyectos.id as proyecto_id',
        'productos.id as producto_id', 'productos.nombre as producto', 'terminos_pago_clientes.nombre as terminos','estatus_linea_clientes.nombre as estatus',
        'tipos_productos.nombre as tipo')
        ->where('proyectos.id','=',$id)
        ->orderBy('sucursals.nombre')
        ->get();

        

        return view('proyecto.linea.index', ['lineas' => $lineas,'cliente' => $cliente,'proyecto' => $proyecto,'id' => $id]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $proyecto = DB::table('proyectos')
        ->leftjoin('estados_proyectos', 'estados_proyectos.id', '=', 'proyectos.estados_proyecto_id')
        ->select('proyectos.*','estados_proyectos.nombre as estado','estados_proyectos.id as estado_id')
        ->where('proyectos.id','=',$id)->first();

        $cliente = Cliente::where('id','=',$proyecto->cliente_id)->first();
        
        $sucursales = DB::table('sucursals')
        ->join('clientes', 'clientes.id', '=', 'sucursals.cliente_id')
        ->join('ciudad_contactos', 'ciudad_contactos.id', '=', 'sucursals.ciudad_contacto_id')
        ->join('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
        ->join('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
        ->join('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
        ->select('sucursals.*','ciudad_contactos.nombre as ciudad','municipio_contactos.nombre as municipio','estado_contactos.alias as estado','pais_contactos.alias as pais')
        ->where('clientes.id','=',$cliente->id)
        ->get();

        $productos = DB::table('productos')
        ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('productos.*','tipos_productos.nombre as tipo')
        ->where('productos.es_activo','=', true)->get();

        $terminos = TerminosPagoCliente::all();

        return view('proyecto.linea.create', ['cliente' => $cliente,'proyecto' => $proyecto, 'sucursales' => $sucursales, 'productos' => $productos, 'terminos' => $terminos]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $proyecto = DB::table('proyectos')
        ->leftjoin('estados_proyectos', 'estados_proyectos.id', '=', 'proyectos.estados_proyecto_id')
        ->select('proyectos.*','estados_proyectos.nombre as estado','estados_proyectos.id as estado_id')
        ->where('proyectos.id','=',$id)->first();

        $cliente = Cliente::where('id','=',$proyecto->cliente_id)->first();
        
        $sucursal = DB::table('sucursals')
            ->join('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
            ->select('sucursals.*','municipio_contactos.nombre as municipio', 'municipio_contactos.id as municipio_id',
            'estado_contactos.alias as estado','pais_contactos.alias as pais')
            ->where('sucursals.id','=',$request->sucursal)
            ->first();
        
        $producto =DB::table('productos')
            ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
            ->select('productos.*','tipos_productos.nombre as tps_nombre')
            ->where('productos.id','=',$request->producto)
            ->first();

        $terminos =DB::table('terminos_pago_clientes')
            ->Join('movimientos_pago_clientes', function (JoinClause $join) {
                $join->on('terminos_pago_clientes.id', '=', 'movimientos_pago_clientes.terminos_pago_cliente_id')
                ->where('movimientos_pago_clientes.secuencia', '=', 1);
            })
            ->select('terminos_pago_clientes.*','movimientos_pago_clientes.estatus_linea_cliente_id as estatus')
            ->where('terminos_pago_clientes.id','=',$request->termino)
            ->first();
        
        $rev = ProyectoLinea::where('sucursal_id','=',$request->sucursal)
            ->where('producto_id','=', $request->producto)
            ->where('proyecto_id','=', $id)
            ->first();

        if (!$rev){
            
            
            $lista = DB::table('listas_precio_lineas')
                ->join('proyectos', 'proyectos.listas_precio_id', '=', 'listas_precio_lineas.listas_precio_id')
                ->select('listas_precio_lineas.*')
                ->where('proyectos.listas_precio_id','=',$proyecto->listas_precio_id)
                ->where('listas_precio_lineas.producto_id','=', $request->producto)
                ->where('listas_precio_lineas.municipio_contacto_id','=', $sucursal->municipio_id)
                ->get();

            $precio = 0;
            $costo = 0;
            foreach ($lista as $l){
                $precio = $l->precio;
                $costo = $l->costo;
            }

            $linea = new ProyectoLinea();

            $linea->proyecto_id = $id;
            $linea->cliente_id = $cliente->id;
            $linea->sucursal_id = $request->sucursal;
            $linea->producto_id = $request->producto;
            $linea->precio = $precio;
            $linea->saldocliente = $precio;
            $linea->costo = $costo;
            $linea->saldoproveedor = $costo;
            $linea->terminos_pago_cliente_id = $terminos->id;
            $linea->estatus_linea_cliente_id = $terminos->estatus; 

            $linea->save();

            $data = [
                'importe' => $proyecto->importe + $precio,
                'saldo' => $proyecto->saldo + $precio,
            ];
            
            $proy = DB::table('proyectos')
                ->where('id','=',$id)
                ->update($data);

            session()->flash('Exito','la partida del proyecto se agregó con éxito...');
        }
        else{
            session()->flash('Error','El producto para la sucursal seleccionada ya existe en el proyecto...');
        };

        $inf = 1;
        
        return redirect()->route('proyectos.lineas', ['id' => $id])->with('info',$inf);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sucursal  $sucursal
     * @return \Illuminate\Http\Response
     */
    public function edit($idp,$idl)
    {
        $proyecto = DB::table('proyectos')
        ->leftjoin('estados_proyectos', 'estados_proyectos.id', '=', 'proyectos.estados_proyecto_id')
        ->select('proyectos.*','estados_proyectos.nombre as estado','estados_proyectos.id as estado_id')
        ->where('proyectos.id','=',$idp)->first();

        if ($proyecto->estado_id != 3){
            $cliente = Cliente::where('id','=',$proyecto->cliente_id)->first();
            
            $linea = ProyectoLinea::where('id','=',$idl)->first();

            $sucursal = DB::table('sucursals')
            ->join('ciudad_contactos', 'ciudad_contactos.id', '=', 'sucursals.ciudad_contacto_id')
            ->join('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
            ->select('sucursals.*','ciudad_contactos.nombre as ciudad','municipio_contactos.nombre as municipio','estado_contactos.alias as estado','pais_contactos.alias as pais')
            ->where('sucursals.id','=',$linea->sucursal_id)
            ->first();

            $producto = DB::table('productos')
            ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
            ->select('productos.*','tipos_productos.nombre as tipo')
            ->where('productos.id','=', $linea->producto_id)->first();

            $terminos = TerminosPagoCliente::all();

            return view('proyecto.linea.edit', ['cliente' => $cliente,'linea' => $linea,'proyecto' => $proyecto, 'sucursal' => $sucursal, 'producto' => $producto, 'terminos' => $terminos]);
        }
        else{
            session()->flash('Error','El estatus del proyecto no permite edición de las partidas...');
            $inf = 1;
        
            return redirect()->route('proyectos.lineas', ['id' => $idp])->with('info',$inf);
        };
        
        
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$idp,$idl)
    {
        
        $proyecto = DB::table('proyectos')
            ->leftjoin('estados_proyectos', 'estados_proyectos.id', '=', 'proyectos.estados_proyecto_id')
            ->select('proyectos.*','estados_proyectos.nombre as estado','estados_proyectos.id as estado_id')
            ->where('proyectos.id','=',$idp)->first();

        $linea = ProyectoLinea::where('id','=',$idl)->first();

        $terminos =DB::table('terminos_pago_clientes')
            ->Join('movimientos_pago_clientes', function (JoinClause $join) {
                $join->on('terminos_pago_clientes.id', '=', 'movimientos_pago_clientes.terminos_pago_cliente_id')
                ->where('movimientos_pago_clientes.secuencia', '=', 1);
            })
            ->select('terminos_pago_clientes.*','movimientos_pago_clientes.estatus_linea_cliente_id as estatus')
            ->where('terminos_pago_clientes.id','=',$request->termino)
            ->first();

        $dif = $request->precio - $linea->precio;
        $saldo = $linea->saldocliente + $dif;
        
        $lineas = DB::table('proyecto_lineas')
                ->where('id','=',$idl)
                ->update([
                    'precio'=> $request->precio,
                    'saldocliente'=> $saldo,
                    'terminos_pago_cliente_id'=> $request->termino,
                    'estatus_linea_cliente_id'=> $terminos->estatus,
                ]
            );

        $data = [
            'importe' => $proyecto->importe + $dif,
            'saldo' => $proyecto->saldo + $dif,
        ];
        
        $proy = DB::table('proyectos')
            ->where('id','=',$idp)
            ->update($data);
        
        $inf = 1;
        session()->flash('Exito','Las sucursales se agregaron con éxito...');
        return redirect()->route('proyectos.lineas', ['id' => $idp])->with('info',$inf);
    }
    
}
