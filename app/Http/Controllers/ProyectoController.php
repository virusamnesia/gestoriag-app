<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\EstadosProyecto;
use App\Models\ListasPrecio;
use App\Models\MovimientosPagoCliente;
use App\Models\Proyecto;
use App\Models\ProyectoSucursalLinea;
use App\Models\Sucursal;
use App\Models\sucursales_proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProyectoController extends Controller
{
    public function index(){
        
        $proyectos =DB::table('proyectos')
        ->join('clientes', 'clientes.id', '=', 'proyectos.cliente_id')
        ->leftjoin('estados_proyectos', 'estados_proyectos.id', '=', 'proyectos.estados_proyecto_id')
        ->select('proyectos.*','clientes.nombre as cliente','clientes.id as cliente_id','estados_proyectos.nombre as estado',
        'estados_proyectos.id as estados_proyecto_id')
        ->orderBy('proyectos.id', 'desc')
        ->get();

        return view('proyecto.index', ['proyectos' => $proyectos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $estados = EstadosProyecto::all();
        $clientes = Cliente::all();

        return view('proyecto.create', ['clientes' => $clientes, 'estados' => $estados]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $proyecto = new Proyecto();

        $proyecto->nombre = $request->nombre;
        $proyecto->anio = $request->año;
        $proyecto->cliente_id = $request->cliente;
        $proyecto->importe = 0;
        $proyecto->saldo = 0;
        $proyecto->cxc = 0;
        $proyecto->estados_proyecto_id = 1;
        $proyecto->fecha_cotizacion = today();
        $proyecto->es_agrupado = $request->agrupado;
        $proyecto->autorizar = 0;

        $proyecto->save();

        $id = $proyecto->id;

        $sucursales =DB::table('sucursals')
        ->join('clientes', 'clientes.id', '=', 'sucursals.cliente_id')
        ->select('sucursals.*','clientes.nombre as cliente','clientes.id as cliente_id')
        ->where('clientes.id','=',$request->cliente)
        ->get();

        $rev = sucursales_proyecto::where('proyecto_id', $id)
        ->first();     

        if (!$rev){
            foreach ($sucursales as $suc){
                $sucursal = new sucursales_proyecto();
                $sucursal->proyecto_id = $id;
                $sucursal->cliente_id = $request->cliente;
                $sucursal->sucursal_id = $suc->id;
                $sucursal->cotizado = False;

                $sucursal->save();
            };
        };

        $inf = 1;
        session()->flash('Exito','El proyecto se agregó con éxito...');
        return redirect()->route('proyectos.sucursales', ['idp' => $id,'idc' => $request->cliente])->with('info',$inf);
    }

    /**
     * Cambia el estatus de autorizado a un proyecto especifico
     * Establece los primeros movimientos a las lineas del proyecto
     * @param  \App\Models\proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function auth($id)
    {
        $proyecto = DB::table('proyectos')
            ->where('id','=',$id)
            ->first();

        if($proyecto->autorizar == 0){
            
            $proy = DB::table('proyectos')
                ->where('id','=',$id)
                ->update([
                'estados_proyecto_id' => 2,
                'fecha_autorizacion' => now(),
                ]);

            $lineas =DB::table('proyecto_lineas')
                ->join('proyectos', 'proyectos.id', '=', 'proyecto_lineas.proyecto_id')
                ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
                ->join('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
                ->join('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
                ->join('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
                ->join('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
                ->leftJoin('terminos_pago_clientes', 'proyecto_lineas.terminos_pago_cliente_id', '=', 'terminos_pago_clientes.id')
                ->leftJoin('estatus_linea_clientes', 'estatus_linea_clientes.id', '=', 'proyecto_lineas.estatus_linea_cliente_id')
                ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
                ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.domicilio as domicilio','sucursals.cliente_id as cliente',
                'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais','proyectos.id as proyecto_id',
                'productos.id as producto_id', 'productos.nombre as producto', 'terminos_pago_clientes.id as terminos','estatus_linea_clientes.id as estatus',
                'tipos_productos.nombre as tipo')
                ->where('proyecto_lineas.proyecto_id','=',$id)
                ->get();

            $total_cliente = 0;
            $total_proveedor = 0;

            foreach($lineas as $linea){
                
                $movimiento = MovimientosPagoCliente::where('terminos_pago_cliente_id','=',$linea->terminos)
                    ->where('secuencia','=',1)
                    ->first();

                if($movimiento == null){
                    $inf = 1;
                    session()->flash('Error','No existen acciones que agregar: '.$linea->sucursal." . ".$linea->producto);
                }
                else{
                    $importe_cliente = 0;
                    $saldo_cliente = $linea->saldocliente;
                    $importe_proveedor = 0;
                    $saldo_proveedor = $linea->saldoproveedor;

                    $importe_cliente = $linea->precio * ($movimiento->valor_cliente / 100);
                    $saldo_cliente = $saldo_cliente - $importe_cliente;
                    $importe_proveedor = $linea->precio * ($movimiento->valor_proveedor / 100);
                    $saldo_proveedor = $saldo_cliente - $importe_proveedor;

                    $total_cliente = $total_cliente + $importe_cliente;
                    $total_proveedor = $total_proveedor + $importe_proveedor;

                    $facturable = 0;
                    if ($importe_cliente >  0 or $importe_proveedor > 0 ){
                        $facturable = 1;
                    }
                    

                    $mov =  new ProyectoSucursalLinea();

                    $mov->proyecto_linea_id = $linea->id;
                    $mov->movimientos_pago_cliente_id = $movimiento->id;
                    $mov->tipos_proceso_id = 1;
                    $mov->es_facturable = $facturable;
                    $mov->fecha_mov = now();
                    $mov->cliente_id = $linea->cliente;
                    $mov->proveedor_id = $linea->proveedor_id;
                    $mov->importe_cliente = $importe_cliente;
                    $mov->saldo_cliente = $saldo_cliente;
                    $mov->importe_proveedor = $importe_proveedor;
                    $mov->saldo_proveedor = $saldo_proveedor;
                    $mov->observaciones = "Autorización";
                    $mov->url = "";

                    $mov->save();

                    $data = [
                        'saldocliente' => $linea->saldocliente - $importe_cliente,
                        'cxc' => $linea->cxc + $importe_cliente,
                        'estatus_linea_cliente_id' => $movimiento->estatus_linea_cliente_id,
                        'saldoproveedor' => $linea->saldoproveedor - $importe_proveedor,
                        'cxp' => $linea->cxp + $importe_proveedor,
                        'proyecto_sucursal_linea_id' => $mov->id,
                    ];

                    $proy = DB::table('proyecto_lineas')
                        ->where('id','=',$linea->id)
                        ->update($data);

                }

            }

            $data = [
                'saldo' => $proyecto->saldo - $total_cliente,
                'cxc' => $proyecto->cxc + $total_cliente,
                'autorizar' => 1,
            ];

            $proy = DB::table('proyectos')
                ->where('id','=',$id)
                ->update($data);
            
            $inf = 1;
            session()->flash('Exito','La autorización se realizó con éxito...');
            return redirect()->route('proyectos')->with('info',$inf);

        }
        else{
            $inf = 1;
            session()->flash('Error','Proyecto previamente autorizado...');
            return redirect()->route('proyectos')->with('info',$inf);
        }
    }

    /**
     * Presenta los productos involucrados en el proyectos.
     *  Permite cambiar los terminos de pago de los productos del proyecto
     * @param  \App\Models\proyecto  $proyecto
     */
    public function terminos($id)
    {
        $proyecto = DB::table('proyectos')
            ->where('id','=',$id)
            ->first();

        $cliente = Cliente::where('id','=',$proyecto->cliente_id)->first();

        if($proyecto->autorizar == 0){
            
            $productos =DB::table('proyecto_lineas')
                ->join('proyectos', 'proyectos.id', '=', 'proyecto_lineas.proyecto_id')
                ->join('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
                ->leftJoin('terminos_pago_clientes', 'proyecto_lineas.terminos_pago_cliente_id', '=', 'terminos_pago_clientes.id')
                ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
                ->select('proyectos.id as proyecto_id','productos.id as producto_id', 'productos.nombre as producto', 'terminos_pago_clientes.id as terminos_id','terminos_pago_clientes.nombre as terminos',
                'tipos_productos.nombre as tipo','productos.alias')
                ->where('proyecto_lineas.proyecto_id','=',$id)
                ->groupBy('proyectos.id','productos.id', 'productos.nombre', 'terminos_pago_clientes.id','terminos_pago_clientes.nombre',
                'tipos_productos.nombre','productos.alias')
                ->get();
            
            $terminos = DB::table('terminos_pago_clientes')
                ->select('terminos_pago_clientes.*')
                ->get();
            $inf = 1;
            return view('proyecto.terminos',['proyecto' => $proyecto,'cliente' => $cliente,'productos' => $productos, 'id' => $id,'terminos' => $terminos])->with('info',$inf);

        }
        else{
            $inf = 1;
            session()->flash('Error','Proyecto previamente autorizado...');
            return redirect()->route('proyectos')->with('info',$inf);
        }
    }

    /**
     * Actualza los terminos de pago de los productos del proyecto
     *  
     * @param  \App\Models\proyecto  $proyecto
     */
    public function termupdate($id,$idp,$term)
    {
        $proyecto = DB::table('proyectos')
            ->where('id','=',$id)
            ->first();

        if($proyecto->autorizar == 0){
            
            $data = [
                'terminos_pago_cliente_id' => $term,
            ];

            $proy = DB::table('proyecto_lineas')
                ->where('proyecto_id','=',$id)
                ->where('producto_id','=',$idp)
                ->update($data);
            
            $inf = 1;
            session()->flash('Exito','La actualización se realizó con éxito...');
            return redirect()->route('terminos.proyectos',['id' => $id])->with('info',$inf);

        }
        else{
            $inf = 1;
            session()->flash('Error','Actualización no realizada...');
            return redirect()->route('terminos.proyectos',['id' => $id])->with('info',$inf);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\proyecto  $proyecto
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
