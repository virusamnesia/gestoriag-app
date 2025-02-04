<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\EstadosPresupuesto;
use App\Models\MovimientosPagoCliente;
use App\Models\Presupuesto;
use App\Models\Proveedor;
use App\Models\ProyectoSucursalLinea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PresupuestoController extends Controller
{
    public function index(){
        
        $presupuestos =DB::table('presupuestos')
        ->join('proveedors', 'proveedors.id', '=', 'presupuestos.proveedor_id')
        ->leftjoin('estados_presupuestos', 'estados_presupuestos.id', '=', 'presupuestos.estados_presupuesto_id')
        ->select('presupuestos.*','proveedors.nombre as proveedor','proveedors.id as proveedor_id','estados_presupuestos.nombre as estado',
        'estados_presupuestos.id as estados_presupuesto_id')
        ->orderBy('presupuestos.id', 'desc')
        ->get();

        return view('presupuesto.index', ['presupuestos' => $presupuestos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $estados = EstadosPresupuesto::all();
        $proveedores = Proveedor::all();

        return view('presupuesto.create', ['proveedores' => $proveedores, 'estados' => $estados]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $lineas =DB::table('proyecto_lineas')
            ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
            ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
            ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
            ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
            ->leftjoin('proveedor_municipios', 'proveedor_municipios.municipio_contacto_id', '=', 'municipio_contactos.id')
            ->leftJoin('proveedors', 'proveedor_municipios.proveedor_id', '=', 'proveedors.id')
            ->leftJoin('productos', 'proyecto_lineas.producto_id', '=', 'productos.id')
            ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
            ->select('clientes.id','clientes.nombre','clientes.alias','clientes.rfc')
            ->where('proveedors.id','=',$request->proveedor)
            ->where('proyecto_lineas.proveedor_id','=',NULL)
            ->groupBy('clientes.id','clientes.nombre','clientes.alias','clientes.rfc')
            ->orderBy('clientes.nombre')
            ->get();

        if (count($lineas) > 0){
            $presupuesto = new Presupuesto();

            $presupuesto->nombre = $request->nombre;
            $presupuesto->anio = $request->año;
            $presupuesto->proveedor_id = $request->proveedor;
            $presupuesto->importe = 0;
            $presupuesto->saldo = 0;
            $presupuesto->cxp = 0;
            $presupuesto->estados_presupuesto_id = 1;
            $presupuesto->fecha_cotizacion = today();
            $presupuesto->autorizar = 0;

            $presupuesto->save();

            $id = $presupuesto->id;

            $proveedor = Proveedor::where('id','=', $request->proveedor)->first();
            $inf = 1;
            session()->flash('Exito','El presupuesto se agregó con éxito...');
            return view('presupuesto.linea.clientes', ['id' => $id,'lineas' => $lineas,'presupuesto' => $presupuesto,'proveedor' => $proveedor])->with('info',$inf);
        }
        else{
            $inf = 1;
            session()->flash('Error','No existen gestiones para este proveedor...');
            return redirect()->route('presupuestos')->with('info',$inf);
        }

    }

    public function products($idp,$idv,$idc)
    {
        $lineas =DB::table('proyecto_lineas')
            ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
            ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
            ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
            ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
            ->leftjoin('proveedor_municipios', 'proveedor_municipios.municipio_contacto_id', '=', 'municipio_contactos.id')
            ->leftJoin('proveedors', 'proveedor_municipios.proveedor_id', '=', 'proveedors.id')
            ->leftJoin('productos', 'proyecto_lineas.producto_id', '=', 'productos.id')
            ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
            ->select('productos.id as producto_id', 'productos.nombre as producto','tipos_productos.nombre as tipo','productos.alias')
            ->where('proveedors.id','=',$idv)
            ->where('clientes.id','=',$idc)
            ->where('proyecto_lineas.proveedor_id','=',NULL)
            ->groupBy('productos.id as producto_id', 'productos.nombre as producto','tipos_productos.nombre as tipo','productos.alias')
            ->orderBy('clientes.nombre')
            ->get();

        $proveedor = Proveedor::where('id','=', $idv)->first();
        $presupuesto = Presupuesto::where('id','=', $idp)->first();
        $cliente = Cliente::where('id','=', $idc)->first();
        $inf = 1;
        session()->flash('Exito','Selecciona los productos para el presupuesto...');
        return view('presupuesto.linea.clientes', ['idp' => $idp,'lineas' => $lineas,'presupuesto' => $presupuesto,'proveedor' => $proveedor,'cliente' => $cliente,'idv' => $idv,'idc' => $idc])->with('info',$inf);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $lineas =DB::table('proyecto_lineas')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
        ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
        ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
        ->leftjoin('proveedor_municipios', 'proveedor_municipios.municipio_contacto_id', '=', 'municipio_contactos.id')
        ->leftJoin('proveedors', 'proveedor_municipios.proveedor_id', '=', 'proveedors.id')
        ->leftJoin('productos', 'proyecto_lineas.producto_id', '=', 'productos.id')
        ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.domicilio as domicilio',
        'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais',
        'proveedors.id as proveedor_id','proveedors.nombre as proveedor','productos.id as producto_id', 'productos.nombre as producto','tipos_productos.nombre as tipo')
        ->where('proyecto_lineas.presupuesto_id','=',$id)
        ->orderBy('sucursals.nombre')
        ->get();

        $presupuesto = Presupuesto::where('id','=', $id)->first();
        $proveedor = Proveedor::where('id','=', $presupuesto->proveedor_id)->first();
        $inf = 1;
        return view('show.presupuesto', ['id' => $id,'lineas' => $lineas,'presupuesto' => $presupuesto,'proveedor' => $proveedor])->with('info',$inf);
    }
    /**
     * Update the specified cost resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function updatecosto(Request $request, $id)
    {
        $data = [
            'costo' => $request->ecosto,
            'saldoproveedor' => $request->ecosto,
        ];
        
        $linea = DB::table('proyecto_lineas')
            ->where('id','=', $request->eid)
            ->update($data);

        $inf = 0;
        session()->flash('Exito','El costo se modificó con éxito...');
        return redirect()->route('show.presupuestos', ['id' => $id])->with('info',$inf);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function edit(top50 $top50)
    {
        $lineas =DB::table('proyecto_lineas')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
        ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
        ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
        ->leftjoin('proveedor_municipios', 'proveedor_municipios.municipio_contacto_id', '=', 'municipio_contactos.id')
        ->leftJoin('proveedors', 'proveedor_municipios.proveedor_id', '=', 'proveedors.id')
        ->leftJoin('productos', 'proyecto_lineas.producto_id', '=', 'productos.id')
        ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('clientes.nombre','clientes.alias','clientes.rfc')
        ->where('proveedors.id','=',$request->proveedor)
        ->orderBy('sucursals.nombre')
        ->get();

        $lineas =DB::table('proyecto_lineas')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
        ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
        ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
        ->leftjoin('proveedor_municipios', 'proveedor_municipios.municipio_contacto_id', '=', 'municipio_contactos.id')
        ->leftJoin('proveedors', 'proveedor_municipios.proveedor_id', '=', 'proveedors.id')
        ->leftJoin('productos', 'proyecto_lineas.producto_id', '=', 'productos.id')
        ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.domicilio as domicilio',
        'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais',
        'proveedors.id as proveedor_id','proveedors.nombre as proveedor','productos.id as producto_id', 'productos.nombre as producto','tipos_productos.nombre as tipo')
        ->where('proveedors.id','=',$request->proveedor)
        ->orderBy('sucursals.nombre')
        ->get();
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

    /**
     * Mass price assignation to buget products.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function auth($id)
    {
        $lineas =DB::table('proyecto_lineas')
            ->leftJoin('productos', 'proyecto_lineas.producto_id', '=', 'productos.id')
            ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
            ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
            ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
            ->select('productos.id as producto_id', 'productos.nombre as producto','tipos_productos.nombre as tipo')
            ->where('proyecto_lineas.presupuesto_id','=',$id)
            ->orderBy('productos.id')
            ->orderBy('productos.nombre')
            ->orderBy('tipos_productos.nombre')
            ->get();

            $inf = 1;
            $presupuesto = Presupuesto::where('id','=', $id)->first();
            $proveedor = Proveedor::where('id','=', $presupuesto->proveedor_id)->first();
            
            return view('presupuesto.precios', ['id' => $id,'lineas' => $lineas,'presupuesto' => $presupuesto,'proveedor' => $proveedor])->with('info',$inf);
    }

    public function updatePrice(Request $request,$id)
    {
        $lineas =DB::table('proyecto_lineas')
            ->leftJoin('productos', 'proyecto_lineas.producto_id', '=', 'productos.id')
            ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
            ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
            ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
            ->select('productos.id as producto_id', 'productos.nombre as producto','tipos_productos.nombre as tipo')
            ->where('proveedors.id','=',$id)
            ->orderBy('productos.id')
            ->orderBy('productos.nombre')
            ->orderBy('tipos_productos.nombre')
            ->get();

        $costo_total= 0;

        foreach ($lineas as $row){
            $sel = "sel".$row->producto_id;
            $costo = "costo".$row->producto_id;
            if ($request->$sel){
                $data = [
                    'costo' => $request-> $costo,
                    'saldoproveedor' => $request->$costo,
                ];
                
                $linea = DB::table('proyecto_lineas')
                    ->where('producto_id','=',$row->id)
                    ->update($data);
                
                $costo_total += $request->$costo;
            } 

            $data = [
                'importe' => $costo_total,
                'saldo' => $costo_total,
                'autorizar' => 1,
            ];
            
            $presupuesto = DB::table('presupuestos')
                ->where('id','=',$id)
                ->update($data);

        };

        $inf = 1;
        session()->flash('Exito','El prresupuesto fue autorizado con éxito...');
        return redirect()->route('presupuestos')->with('info',$inf);
    }

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeLineas(Request $request, $id,$idv)
    {

        $lineas =DB::table('proyecto_lineas')
            ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
            ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
            ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
            ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
            ->leftjoin('proveedor_municipios', 'proveedor_municipios.municipio_contacto_id', '=', 'municipio_contactos.id')
            ->leftJoin('proveedors', 'proveedor_municipios.proveedor_id', '=', 'proveedors.id')
            ->leftJoin('productos', 'proyecto_lineas.producto_id', '=', 'productos.id')
            ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
            ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.domicilio as domicilio',
            'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais',
            'proveedors.id as proveedor_id','proveedors.nombre as proveedor','productos.id as producto_id', 'productos.nombre as producto','tipos_productos.nombre as tipo')
            ->where('proveedors.id','=',$idv)
            ->orderBy('sucursals.nombre')
            ->get();

        foreach ($lineas as $row){
            $sel = "sel".$row->id;
            if ($request->$sel){
                $data = [
                    'proveedor_id' => $idv,
                    'presupuesto_id' => $id,
                ];
                
                $linea = DB::table('proyecto_lineas')
                    ->where('id','=',$row->id)
                    ->update($data);
            } 
        };

        $inf = 1;
        session()->flash('Exito','El presupuesto fue creado con éxito...');
        return redirect()->route('presupuestos')->with('info',$inf);

    }

    /**
     * Show the form for creating a new resource line.
     *
     * @return \Illuminate\Http\Response
     */
    public function createmov($idp,$idl)
    {
        $presupuesto = Presupuesto::where('id','=', $idp)->first();
        $proveedor = Proveedor::where('id','=', $presupuesto->proveedor_id)->first();

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
        $next = MovimientosPagoCliente::where('secuencia','=',$secuencia + 1)
        ->where('terminos_pago_cliente_id','=',$linea->terminos)->first();

        $inf = 1;

        if($next == null){
            session()->flash('Error','No existen más acciones que agregar...');
            return redirect()->route('show.presupuestos', ['id' => $idp])->with('info',$inf);
        }
        else{
            return view('presupuesto.movimiento.create', ['idp' => $idp,'idl' => $idl,'presupuesto' => $presupuesto,'proveedor' => $proveedor,
            'linea' => $linea,'next' => $next])->with('info',$inf);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storemov(Request $request,$idp,$idl)
    {
        $presupuesto = Presupuesto::where('id','=', $idp)->first();
        $proveedor = Proveedor::where('id','=', $presupuesto->proveedor_id)->first();

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
            return redirect()->route('show.presupuestos', ['id' => $idp])->with('info',$inf);
        }
        else{
            $importec = 0;
            $saldoc = $linea->saldocliente;
            $importep = 0;
            $saldop = $linea->saldoproveedor;

            if($movimiento->facturable == 1){
                $importec = $linea->precio * ($movimiento->valor_cliente / 100);
                $saldoc = $saldoc - $importec;
                $importep = $linea->costo * ($movimiento->valor_proveedor / 100);
                $saldop = $saldop - $importep;
            }
            

            $mov =  new ProyectoSucursalLinea();

            $mov->proyecto_linea_id = $idl;
            $mov->movimientos_pago_cliente_id = $request->movimiento;
            $mov->movimientos_pago_proveedor_id = 0;
            $mov->tipos_proceso_id = 1;
            $mov->es_facturable = $movimiento->facturable;
            $mov->fecha_mov = $request->fecha;
            $mov->cliente_id = $linea->id;
            $mov->proveedor_id = $proveedor->id;
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
                'saldo' => $presupuesto->saldo - $importep,
                'cxp' => $presupuesto->cxp + $importep,
            ];

            $proy = DB::table('presupuestos')
                ->where('id','=',$idp)
                ->update($data);
            
            //
            $inf = 1;
            session()->flash('Exito','La actualización se agregó con éxito...');
            return redirect()->route('show.presupuestos', ['id' => $idp])->with('info',$inf);
        }
    }

    public function indexmov($idp,$idl){
        
        $presupuesto = Presupuesto::where('id','=', $idp)->first();
        $proveedor = Proveedor::where('id','=', $presupuesto->proveedor_id)->first();
        
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

        return view('presupuesto.movimiento.index', ['movimientos' => $movimientos,'linea' => $linea,'proveedor' => $proveedor,'presupuesto' => $presupuesto,'idp' => $idp,'idl' => $idl]);
    }
}
