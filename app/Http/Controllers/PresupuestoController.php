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
            ->leftjoin('clientes', 'clientes.id', '=', 'sucursals.cliente_id')
            ->leftjoin('proveedor_municipios', 'proveedor_municipios.municipio_contacto_id', '=', 'municipio_contactos.id')
            ->leftJoin('proveedors', 'proveedor_municipios.proveedor_id', '=', 'proveedors.id')
            ->leftJoin('productos', 'proyecto_lineas.producto_id', '=', 'productos.id')
            ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
            ->select('clientes.id','clientes.nombre','clientes.clave','clientes.rfc')
            ->where('proveedors.id','=',$request->proveedor)
            ->where('proyecto_lineas.proveedor_id','=',NULL)
            ->groupBy('clientes.id','clientes.nombre','clientes.clave','clientes.rfc')
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
            ->leftjoin('clientes', 'clientes.id', '=', 'sucursals.cliente_id')
            ->leftJoin('productos', 'proyecto_lineas.producto_id', '=', 'productos.id')
            ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
            ->select('productos.id as producto_id','productos.nombre as producto','tipos_productos.nombre as tipo','productos.alias')
            ->where('proveedors.id','=',$idv)
            ->where('clientes.id','=',$idc)
            ->where('proyecto_lineas.proveedor_id','=',NULL)
            ->groupBy('productos.id','productos.nombre','tipos_productos.nombre','productos.alias')
            ->orderBy('productos.nombre')
            ->get();

        $proveedor = Proveedor::where('id','=', $idv)->first();
        $presupuesto = Presupuesto::where('id','=', $idp)->first();
        $cliente = Cliente::where('id','=', $idc)->first();
        $inf = 1;
        session()->flash('Exito','Selecciona los productos para el presupuesto...');
        return view('presupuesto.linea.productos', ['idp' => $idp,'lineas' => $lineas,'presupuesto' => $presupuesto,'proveedor' => $proveedor,'cliente' => $cliente,'idv' => $idv,'idc' => $idc])->with('info',$inf);

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

     public function costos($id)
     {
        $presupuesto = Presupuesto::where('id','=', $id)->first();
         
        $lineas =DB::table('proyecto_lineas')
             ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
             ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
             ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
             ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
             ->leftJoin('productos', 'proyecto_lineas.producto_id', '=', 'productos.id')
             ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
             ->select('productos.id as producto_id','productos.nombre as producto','tipos_productos.nombre as tipo','productos.alias')
             ->where('proyecto_lineas.presupuesto_id','=',$presupuesto->id)
             ->where('proyecto_lineas.proveedor_id','=',$presupuesto->proveedor_id)
             ->groupBy('productos.id','productos.nombre','tipos_productos.nombre','productos.alias')
             ->orderBy('productos.nombre')
             ->get();
 
         $proveedor = Proveedor::where('id','=', $presupuesto->proveedor_id)->first();

         $inf = 1;
         session()->flash('Exito','Selecciona los productos para el presupuesto...');
         return view('presupuesto.productos', ['id' => $id,'lineas' => $lineas,'presupuesto' => $presupuesto,'proveedor' => $proveedor])->with('info',$inf);
 
     }

    public function updatecostos(Request $request, $id)
    {
        $presupuesto = Presupuesto::where('id','=', $id)->first();
         
        $lineas =DB::table('proyecto_lineas')
             ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
             ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
             ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
             ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
             ->leftJoin('productos', 'proyecto_lineas.producto_id', '=', 'productos.id')
             ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
             ->select('productos.id as producto_id','proyecto_lineas.id as linea_id')
             ->where('proyecto_lineas.presupuesto_id','=',$id)
             ->where('proyecto_lineas.proveedor_id','=',$presupuesto->proveedor_id)
             ->orderBy('productos.nombre')
             ->get();
        
        $subtotal = 0;
        $total = 0; 

        foreach ($lineas as $row){
            $imput = "costo".$row->producto_id;
            if ($request->$imput > 0){
                
                $subtotal += $request->$imput;
                $total += $request->$imput;

                $line = DB::table('proyecto_lineas')
                    ->where('id','=', $row->linea_id)
                    ->update([
                    'costo'=> $request->$imput,
                ]);
            }
        }
        
        $data = [
            'importe' => $subtotal,
        ];
        
        $pres = DB::table('presupuesto')
            ->where('id','=', $id)
            ->update($data);

        $inf = 0;
        session()->flash('Exito','Los costos se modificarón con éxito...');
        return redirect()->route('presupuestos')->with('info',$inf);
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
        ->select('clientes.nombre','clientes.clave','clientes.rfc')
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
        $presupuesto = Presupuesto::where('id','=', $id)->first();
         
        $lineas =DB::table('proyecto_lineas')
             ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
             ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
             ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
             ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
             ->leftJoin('productos', 'proyecto_lineas.producto_id', '=', 'productos.id')
             ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
             ->select('productos.id as producto_id','proyecto_lineas.id as linea_id')
             ->where('proyecto_lineas.presupuesto_id','=',$id)
             ->where('proyecto_lineas.proveedor_id','=',$presupuesto->proveedor_id)
             ->where('proyecto_lineas.costo','=',0)
             ->get();
        
        if (count($lineas) > 0){
            $inf = 0;
            session()->flash('Error','Existen porductos sin costo asignado...');
            return redirect()->route('presupuestos')->with('info',$inf);
        }
        else{
            $lineas =DB::table('proyecto_lineas')
                ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
                ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
                ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
                ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
                ->leftJoin('productos', 'proyecto_lineas.producto_id', '=', 'productos.id')
                ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
                ->select('productos.id as producto_id','proyecto_lineas.id as linea_id','proyecto_lineas.costo')
                ->where('proyecto_lineas.presupuesto_id','=',$id)
                ->where('proyecto_lineas.proveedor_id','=',$presupuesto->proveedor_id)
                ->get();

            $subtotal = 0;
            $saldototal = 0; 
    
            foreach ($lineas as $row){
                $subtotal += $row->costo;

                $movs =DB::table('proyecto_lineas')
                    ->join('proyecto_sucursal_lineas', 'proyecto_lineas.id', '=', 'proyecto_sucursal_lineas.proyecto_linea_id')
                    ->leftjoin('movimientos_pago_clientes', 'movimientos_pago_clientes.id', '=', 'proyecto_sucursal_lineas.movimientos_pago_cliente_id')
                    ->select('movimientos_pago_clientes.id as mov_id','movimientos_pago_clientes.valor_proveedor as mov_porc','proyecto_lineas.id as linea_id','proyecto_lineas.costo','movimientos_pago_clientes.importe_proveeodr','movimientos_pago_clientes.saldo_proveeodr')
                    ->where('proyecto_lineas.presupuesto_id','=',$id)
                    ->where('proyecto_lineas.proveedor_id','=',$presupuesto->proveedor_id)
                    ->get();
                $cxp = 0;
                $saldo = $row->costo;
                foreach($movs as $m){
                    $importe = $row->costo * $m->mov_porc;
                    $cxp += $importe;
                    $saldo = $saldo - $importe;
                    if($importe > 0){
                        $data = [
                            'importe_proveeodr'=> $importe,
                            'saldo_proveeodr'=> $saldo,
                            'es_facturable'=> 1,
                        ];
                    }
                    else{
                        $data = [
                            'importe_proveeodr'=> $importe,
                            'saldo_proveeodr'=> $saldo,
                        ];
                    }
                    $upm = DB::table('proyecto_sucursal_lineas')
                        ->where('id','=', $m->mov_id)
                        ->update($data);
                }

                $line = DB::table('proyecto_lineas')
                    ->where('id','=', $row->linea_id)
                    ->update([
                    'saldoproveedor'=> $saldo,
                    'cxp'=> $cxp,
                ]);
                
                $saldototal += $saldo;
            }
            
            $data = [
                'saldo' => $saldototal,
                'fecha_autorizacion' => today(),
                'autorizar' => 1,
            ];
            
            $pres = DB::table('presupuesto')
                ->where('id','=', $id)
                ->update($data);
    
            $inf = 0;
            session()->flash('Exito','El presupuesto se autorizó con éxito...');
            return redirect()->route('presupuestos')->with('info',$inf);
        }
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

     public function lineas($id)
    {

        $presupuesto = Presupuesto::where('id','=', $id)->first();
        $proveedor = Proveedor::where('id','=', $presupuesto->proveedor_id)->first();

        $lineas =DB::table('proyecto_lineas')
            ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
            ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
            ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
            ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
            ->leftjoin('proveedor_municipios', 'proveedor_municipios.municipio_contacto_id', '=', 'municipio_contactos.id')
            ->leftJoin('proveedors', 'proveedor_municipios.proveedor_id', '=', 'proveedors.id')
            ->leftjoin('clientes', 'clientes.id', '=', 'sucursals.cliente_id')
            ->leftJoin('productos', 'proyecto_lineas.producto_id', '=', 'productos.id')
            ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
            ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.domicilio as domicilio',
            'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais',
            'proveedors.id as proveedor_id','proveedors.nombre as proveedor','productos.id as producto_id', 'productos.nombre as producto','tipos_productos.nombre as tipo')
            ->where('proyecto_lineas.presupuesto_id','=',$id)
            ->orderBy('sucursals.nombre')
            ->get();

        

        $inf = 1;
        return view('presupuestos.linea.lineas', ['id' => $id,'lineas' => $lineas,'presupuesto' => $presupuesto,'proveedor' => $proveedor])->with('info',$inf);

    }

    public function storeLineas(Request $request, $idp,$idv,$idc)
    {

        $lineas =DB::table('proyecto_lineas')
            ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
            ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
            ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
            ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
            ->leftjoin('proveedor_municipios', 'proveedor_municipios.municipio_contacto_id', '=', 'municipio_contactos.id')
            ->leftJoin('proveedors', 'proveedor_municipios.proveedor_id', '=', 'proveedors.id')
            ->leftjoin('clientes', 'clientes.id', '=', 'sucursals.cliente_id')
            ->leftJoin('productos', 'proyecto_lineas.producto_id', '=', 'productos.id')
            ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
            ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.domicilio as domicilio',
            'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais',
            'proveedors.id as proveedor_id','proveedors.nombre as proveedor','productos.id as producto_id', 'productos.nombre as producto','tipos_productos.nombre as tipo')
            ->where('proveedors.id','=',$idv)
            ->where('clientes.id','=',$idc)
            ->where('proyecto_lineas.proveedor_id','=',NULL)
            ->orderBy('clientes.nombre')
            ->get();

        foreach ($lineas as $row){
            $sel = "sel".$row->id;
            if ($request->$sel){
                $data = [
                    'proveedor_id' => $idv,
                    'presupuesto_id' => $idp,
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
            'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais','proyectos.id as proyecto_id','proyectos.nombre as proyecto',
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
            return view('presu´puesto.movimiento.create', ['idp' => $idp,'idl' => $idl,'presupuesto' => $presupuesto,'proveedor' => $proveedor,
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
        'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais','proyectos.id as proyecto_id','proyectos.saldo as proyecto_saldo','proyectos.cxc as proyecto_cxc',
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
            $mov->cliente_id = $linea->cliente_id;
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
              'saldo' => $linea->proyecto_saldo - $importec,
                'cxc' => $linea->proyecto_cxc + $importec,
            ];
  
            $proy = DB::table('proyectos')
            ->where('id','=',$linea->proyecto_id)
            ->update($data);

            $data = [
                'saldo' => $presupuesto->saldo - $importep,
                'cxp' => $presupuesto->cxp + $importep,
            ];
    
            $pres = DB::table('presupuestos')
                ->where('id','=',$idp)
                ->update($data);
            
            //
            $inf = 1;
            session()->flash('Exito','La actualización se agregó con éxito...');
            return redirect()->route('presupuestos.lineas', ['id' => $idp])->with('info',$inf);
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
            'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais','proyectos.id as proyecto_id','proyectos.saldo as proyecto_saldo','proyectos.cxc as proyecto_cxc',
            'productos.id as producto_id', 'productos.nombre as producto', 'terminos_pago_clientes.id as terminos','estatus_linea_clientes.id as estatus',
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
