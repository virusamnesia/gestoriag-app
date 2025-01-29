<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\MovimientosPagoCliente;
use App\Models\Proyecto;
use App\Models\ProyectoLinea;
use App\Models\ProyectoSucursalLinea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProyectoSucursalLineaController extends Controller
{
    public function index($idp,$idl){
        
        $proyecto = Proyecto::where('proyectos.id','=',$idp)->first();

        $cliente = Cliente::where('id','=',$proyecto->cliente_id)->first();

        $linea =DB::table('proyecto_lineas')
        ->join('proyectos', 'proyectos.id', '=', 'proyecto_lineas.proyecto_id')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->join('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
        ->join('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
        ->join('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
        ->join('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
        ->leftJoin('terminos_pago_clientes', 'proyecto_lineas.terminos_pago_cliente_id', '=', 'terminos_pago_clientes.id')
        ->leftJoin('estatus_linea_clientes', 'estatus_linea_clientes.id', '=', 'proyecto_lineas.estatus_linea_cliente_id')
        ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.domicilio as domicilio',
        'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais','proyectos.id as proyecto_id',
        'productos.id as producto_id', 'productos.nombre as producto', 'terminos_pago_clientes.nombre as terminos','estatus_linea_clientes.nombre as estatus',
        'tipos_productos.nombre as tipo')
        ->where('proyecto_lineas.id','=',$idl)
        ->first();

        $movimientos =DB::table('proyecto_sucursal_lineas')
        ->join('movimientos_pago_clientes', 'movimientos_pago_clientes.id', '=', 'proyecto_sucursal_lineas.movimientos_pago_cliente_id')
        ->leftjoin('clientes_factura_lineas', 'proyecto_sucursal_lineas.id', '=', 'clientes_factura_lineas.proyecto_sucursal_linea_id')
        ->leftjoin('clientes_facturas', 'clientes_facturas.id', '=', 'clientes_factura_lineas.clientes_factura_id')
        ->select('proyecto_sucursal_lineas.*','movimientos_pago_clientes.nombre as movimiento','movimientos_pago_clientes.secuencia as secuencia',
        'clientes_facturas.id as factura')
        ->where('proyecto_sucursal_lineas.proyecto_linea_id','=',$idl)
        ->get();

        return view('proyecto.movimiento.index', ['movimientos' => $movimientos,'linea' => $linea,'cliente' => $cliente,'proyecto' => $proyecto,'idp' => $idp,'idl' => $idl]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($idp,$idl)
    {
        $proyecto = Proyecto::where('proyectos.id','=',$idp)->first();

        $cliente = Cliente::where('id','=',$proyecto->cliente_id)->first();

        $linea =DB::table('proyecto_lineas')
        ->join('proyectos', 'proyectos.id', '=', 'proyecto_lineas.proyecto_id')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->join('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
        ->join('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
        ->join('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
        ->join('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
        ->leftJoin('terminos_pago_clientes', 'proyecto_lineas.terminos_pago_cliente_id', '=', 'terminos_pago_clientes.id')
        ->leftJoin('estatus_linea_clientes', 'estatus_linea_clientes.id', '=', 'proyecto_lineas.estatus_linea_cliente_id')
        ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.domicilio as domicilio',
        'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais','proyectos.id as proyecto_id',
        'productos.id as producto_id', 'productos.nombre as producto', 'terminos_pago_clientes.id as terminos','estatus_linea_clientes.nombre as estatus',
        'tipos_productos.nombre as tipo')
        ->where('proyecto_lineas.id','=',$idl)
        ->first();

        $movimiento =DB::table('proyecto_sucursal_lineas')
        ->join('movimientos_pago_clientes', 'movimientos_pago_clientes.id', '=', 'proyecto_sucursal_lineas.movimientos_pago_cliente_id')
        ->select('movimientos_pago_clientes.*')
        ->where('proyecto_sucursal_lineas.proyecto_linea_id','=',$idl)
        ->orderBy('movimientos_pago_clientes.secuencia','desc')
        ->first();

        if($movimiento == null){
            $secuencia = 0;
        }
        else{
            $secuencia = $movimiento->secuencia;
        }
        $next =DB::table('movimientos_pago_clientes')
            ->join('estatus_linea_clientes', 'estatus_linea_clientes.id', '=', 'movimientos_pago_clientes.estatus_linea_cliente_id')
            ->select('movimientos_pago_clientes.*','estatus_linea_clientes.nombre')
            ->where('movimientos_pago_clientes.secuencia','=',$secuencia + 1)
            ->where('movimientos_pago_clientes.terminos_pago_cliente_id','=',$linea->terminos)
            ->first();

        $inf = 1;

        if($next == null){
            session()->flash('Error','No existen más acciones que agregar...');
            return redirect()->route('proyectos.lineas', ['id' => $idp])->with('info',$inf);
        }
        else{
            return view('proyecto.movimiento.create', ['idp' => $idp,'idl' => $idl,'proyecto' => $proyecto,'cliente' => $cliente,
            'linea' => $linea,'next' => $next])->with('info',$inf);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$idp,$idl)
    {
        $proyecto = Proyecto::where('proyectos.id','=',$idp)->first();

        $cliente = Cliente::where('id','=',$proyecto->cliente_id)->first();

        $linea =DB::table('proyecto_lineas')
        ->join('proyectos', 'proyectos.id', '=', 'proyecto_lineas.proyecto_id')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->join('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
        ->join('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
        ->join('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
        ->join('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
        ->leftJoin('terminos_pago_clientes', 'proyecto_lineas.terminos_pago_cliente_id', '=', 'terminos_pago_clientes.id')
        ->leftJoin('estatus_linea_clientes', 'estatus_linea_clientes.id', '=', 'proyecto_lineas.estatus_linea_cliente_id')
        ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.domicilio as domicilio',
        'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais','proyectos.id as proyecto_id',
        'productos.id as producto_id', 'productos.nombre as producto', 'terminos_pago_clientes.id as terminos','estatus_linea_clientes.id as estatus',
        'tipos_productos.nombre as tipo')
        ->where('proyecto_lineas.id','=',$idl)
        ->first();

        $movimiento = MovimientosPagoCliente::where('id','=',$request->movimiento)->first();

        if($movimiento == null){
            $inf = 1;
            session()->flash('Error','No existen más acciones que agregar...');
            return redirect()->route('proyectos.lineas', ['id' => $idp])->with('info',$inf);
        }
        else{
            $importec = 0;
            $saldoc = $linea->saldocliente;
            $importep = 0;
            $saldop = $linea->saldoproveedor;

            $importec = $linea->precio * ($movimiento->valor_cliente / 100);
            $saldoc = $saldoc - $importec;
            $importep = $linea->costo * ($movimiento->valor_proveedor / 100);
            $saldop = $saldop - $importep;

            $facturable = 0;
            if ($importec >  0 or $importep > 0 ){
                $facturable = 1;
            }
            

            $mov =  new ProyectoSucursalLinea();

            $mov->proyecto_linea_id = $idl;
            $mov->movimientos_pago_cliente_id = $request->movimiento;
            $mov->tipos_proceso_id = 1;
            $mov->es_facturable = $facturable;
            $mov->fecha_mov = $request->fecha;
            $mov->cliente_id = $cliente->id;
            $mov->proveedor_id = $linea->proveedor_id;
            $mov->importe_cliente = $importec;
            $mov->saldo_cliente = $saldoc;
            $mov->importe_proveedor = $importep;
            $mov->saldo_proveedor = $saldop;
            $mov->observaciones = $request->observaciones;
            $mov->url = $request->url;

            $mov->save();

            $data = [
                'saldocliente' => $linea->saldocliente - $importec,
                'cxc' => $linea->cxc + $importec,
                'saldoproveedor' => $linea->saldoproveedor - $importep,
                'cxp' => $linea->cxc + $importep,
                'estatus_linea_cliente_id' => $movimiento->estatus_linea_cliente_id,
            ];

            $proy = DB::table('proyecto_lineas')
                ->where('id','=',$idl)
                ->update($data);

            $data = [
                'saldo' => $proyecto->saldo - $importec,
                'cxc' => $proyecto->cxc + $importec,
            ];

            $proy = DB::table('proyectos')
                ->where('id','=',$idp)
                ->update($data);
            
            //
            $inf = 1;
            session()->flash('Exito','La actualización se agregó con éxito...');
            return redirect()->route('proyectos.lineas', ['id' => $idp])->with('info',$inf);
        }

        

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function edit()
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
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        //
    }
    
}
