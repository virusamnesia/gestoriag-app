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
        ->leftJoin('estatus_linea_clientes', 'estatus_linea_clientes.id', '=', 'movimientos_pago_clientes.estatus_linea_cliente_id')
        ->leftjoin('clientes_factura_lineas', 'proyecto_sucursal_lineas.id', '=', 'clientes_factura_lineas.proyecto_sucursal_linea_id')
        ->leftjoin('clientes_facturas', 'clientes_facturas.id', '=', 'clientes_factura_lineas.clientes_factura_id')
        ->select('proyecto_sucursal_lineas.*','estatus_linea_clientes.nombre as movimiento','movimientos_pago_clientes.secuencia as secuencia',
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

        $inf = 'No existen más acciones que agregar...';

        if($next == null){
            session()->flash('Error',$inf);
            return redirect()->route('proyectos.lineas', ['id' => $idp])->with('error',$inf);
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
        ->leftJoin('presupuestos', 'presupuestos.id', '=', 'proyecto_lineas.presupuesto_id')
        ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.domicilio as domicilio',
        'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais','proyectos.id as proyecto_id',
        'productos.id as producto_id', 'productos.nombre as producto', 'productos.iva', 'terminos_pago_clientes.id as terminos','estatus_linea_clientes.id as estatus',
        'presupuestos.id as presupuesto_id','presupuestos.saldo as presupuesto_saldo','presupuestos.cxp as presupuestos_cxp','tipos_productos.nombre as tipo')
        ->where('proyecto_lineas.id','=',$idl)
        ->first();

        $movimiento = MovimientosPagoCliente::where('id','=',$request->movimiento)->first();

        if($movimiento == null){
            $inf = 'No existen más acciones que agregar...';
            session()->flash('Error',$inf);
            return redirect()->route('proyectos.lineas', ['id' => $idp])->with('error',$inf);
        }
        else{
            $posicion =DB::table('proyectos')
            ->join('fiscal_positions', 'fiscal_positions.id', '=', 'proyectos.fiscal_position_id')
            ->select('fiscal_positions.*')
            ->where('proyectos.id','=',$idp)
            ->get();

            foreach ($posicion as $pos){
                $posicion_id = $pos->id;
                $iva_t = $pos->iva_t;
                $isr_r = $pos->isr_r;
                $iva_r = $pos->iva_r;
                $imp_c = $pos->imp_c;
            } 

            if($linea->iva <> 16){
                $iva_t = $linea->iva;
                $iva_r = $linea->iva;    
            }
            
            $subtotal_linea = $linea->subtotal_v * ($movimiento->valor_cliente / 100);
            $iva_t_linea = $subtotal_linea * ($iva_t / 100);
            $isr_r_linea = $subtotal_linea * ($isr_r / 100);
            $iva_r_linea = $subtotal_linea * ($iva_r / 100);
            $imp_c_linea = $subtotal_linea * ($imp_c / 100);
            $total_linea = $subtotal_linea + $iva_t_linea - $isr_r_linea - $iva_r_linea - $imp_c_linea;
            $saldoc = $linea->saldocliente - $total_linea;

            // Posicion del proveedor
            $total_r = 0;
            $subtotal_r = 0;
            if($linea->presupuesto_id > 0){
                $presupuesto = DB::table('presupuestos')
                ->select('presupuestos.*')
                ->where('presupuesto.id','=',$linea->presupuesto_id)->first();

                $posicion_pv =DB::table('presupuestos')
                ->join('fiscal_positions', 'fiscal_positions.id', '=', 'presupuestos.fiscal_position_id')
                ->select('fiscal_positions.*')
                ->where('presupuestos.id','=',$linea->presupuesto_id)
                ->get();

                foreach ($posicion_pv as $pos_pv){
                    $posicion_id_pv = $pos_pv->id;
                    $iva_t_pv = $pos_pv->iva_t;
                    $isr_r_pv = $pos_pv->isr_r;
                    $iva_r_pv = $pos_pv->iva_r;
                    $imp_c_pv = $pos_pv->imp_c;
                }

                $subtotal_pv = $linea->subtotal_c;
                $saldo_pv = $linea->saldoproveedor;

                $subtotal_r = $presupuesto->subtotal_c - $subtotal_pv;
                $iva_t_r = $subtotal_pv * ($iva_t / 100);
                $isr_r_r = $subtotal_pv * ($isr_r / 100);
                $iva_r_r = $subtotal_pv * ($iva_r / 100);
                $imp_c_r = $subtotal_pv * ($imp_c / 100);
                $total_r = $subtotal_pv + $iva_t_r - $isr_r_r - $iva_r_r - $imp_c_r;
                
                $data = [
                    'subtotal' => $subtotal_r,
                    'iva_t' => $iva_t_r,
                    'isr_r' => $isr_r_r,
                    'iva_r' => $iva_r_r,
                    'imp_c' => $imp_c_r,
                    'importe' => $total_r,
                    'saldo' => $presupuesto->saldo - $saldo_pv,
                ];
                
                $pres = DB::table('presupuestos')
                    ->where('id','=',$linea->presupuesto_id)
                    ->update($data);

               $saldop = $linea->saldoproveedor - $total_r;
            }
            

            $facturable = 0;
            if ($total_linea >  0 or $total_r > 0 ){
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
            $mov->subtotal_cliente = $subtotal_linea;
            $mov->importe_cliente = $total_linea;
            $mov->saldo_cliente = $saldoc;
            $mov->importe_proveedor = $total_r;
            $mov->subtotal_proveedor = $subtotal_r;
            $mov->saldo_proveedor = $saldop;
            $mov->observaciones = $request->observaciones;
            $mov->url = $request->url;

            $mov->save();

            $data = [
                'saldocliente' => $linea->saldoc,
                'cxc' => $linea->cxc + $total_linea,
                'saldoproveedor' => $saldop,
                'cxp' => $linea->cxc + $total_r,
                'estatus_linea_cliente_id' => $movimiento->estatus_linea_cliente_id,
            ];

            $proy = DB::table('proyecto_lineas')
                ->where('id','=',$idl)
                ->update($data);

            $data = [
                'saldo' => $proyecto->saldo - $total_linea,
                'cxc' => $proyecto->cxc + $total_linea,
            ];

            $proy = DB::table('proyectos')
                ->where('id','=',$idp)
                ->update($data);

            if($linea->presupuesto_id > 0){
                $data = [
                    'saldo' => $linea->presupuesto_saldo - $total_r,
                    'cxp' => $linea->presupuesto_cxp + $total_r,
                ];
        
                $pres = DB::table('presupuestos')
                    ->where('id','=',$linea->presupuesto_id)
                    ->update($data);
            }
            
            //
            $inf = 'La actualización se agregó con éxito...';
            session()->flash('Exito',$inf);
            return redirect()->route('proyectos.lineas', ['id' => $idp])->with('message',$inf);
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
