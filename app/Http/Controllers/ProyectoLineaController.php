<?php

namespace App\Http\Controllers;

use App\Imports\ProyectoLineaImport;
use App\Models\Cliente;
use App\Models\Importacion;
use App\Models\ImportacionError;
use App\Models\Producto;
use App\Models\Proyecto;
use App\Models\ProyectoLinea;
use App\Models\TerminosPagoCliente;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

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
        ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
        ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
        ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
        ->leftjoin('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
        ->leftJoin('terminos_pago_clientes', 'proyecto_lineas.terminos_pago_cliente_id', '=', 'terminos_pago_clientes.id')
        ->leftJoin('estatus_linea_clientes', 'estatus_linea_clientes.id', '=', 'proyecto_lineas.estatus_linea_cliente_id')
        ->leftjoin('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.domicilio as domicilio',
        'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais','proyectos.id as proyecto_id',
        'productos.id as producto_id', 'productos.nombre as producto', 'terminos_pago_clientes.nombre as terminos','estatus_linea_clientes.nombre as estatus',
        'tipos_productos.nombre as tipo')
        ->where('proyectos.id','=',$id)
        ->orderBy('sucursals.nombre')
        ->get();

        $importacion = DB::table('importacions')
            ->join('importacion_errors', 'importacions.id', '=', 'importacion_errors.importacion_id')
            ->where('importacions.proyecto_id','=',$id)
            ->select('importacions.*')
            ->orderBy('importacions.id','desc')
            ->first();
        if($importacion == null){
            $import = 0;
        }
        else{
            $import = $importacion->id;
        }

        

        return view('proyecto.linea.index', ['lineas' => $lineas,'cliente' => $cliente,'proyecto' => $proyecto,'id' => $id, 'import' => $import]);
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

        $posicion =DB::table('proyectos')
        ->join('fiscal_positions', 'fiscal_positions.id', '=', 'proyectos.fiscal_position_id')
        ->select('fiscal_positions.*')
        ->where('proyectos.id','=',$id)
        ->get();

        foreach ($posicion as $pos){
            $posicion_id = $pos->id;
            $iva_t = $pos->iva_t;
            $isr_r = $pos->isr_r;
            $iva_r = $pos->iva_r;
            $imp_c = $pos->imp_c;
        }
        
        if($producto->iva <> 16){
            $iva_t = $producto->iva;
            $iva_r = $producto->iva;    
        }
        
        $rev = ProyectoLinea::where('sucursal_id','=',$request->sucursal)
            ->where('producto_id','=', $request->producto)
            ->where('proyecto_id','=', $id)
            ->first();

        if (!$rev){

            $subtotal_linea = $request->cantidad * $request->precio;
            $iva_t_linea = $subtotal_linea * ($iva_t / 100);
            $isr_r_linea = $subtotal_linea * ($isr_r / 100);
            $iva_r_linea = $subtotal_linea * ($iva_r / 100);
            $imp_c_linea = $subtotal_linea * ($imp_c / 100);
            $total_linea = $subtotal_linea + $iva_t_linea - $isr_r_linea - $iva_r_linea - $imp_c_linea;
            
            $linea = new ProyectoLinea();

            $linea->proyecto_id = $id;
            $linea->cliente_id = $cliente->id;
            $linea->sucursal_id = $request->sucursal;
            $linea->producto_id = $request->producto;
            $linea->cantidad = $request->cant;
            $linea->precio = $request->precio;
            $linea->subtotal_v = $subtotal_linea;
            $linea->iva_t_v = $iva_t_linea;
            $linea->isr_r_v = $isr_r_linea;
            $linea->iva_r_v = $iva_r_linea;
            $linea->imp_c_v = $imp_c_linea;
            $linea->total_v = $total_linea;
            $linea->saldocliente = $total_linea;
            $linea->costo = 0;
            $linea->subtotal_c = 0;
            $linea->iva_t_c = 0;
            $linea->isr_r_c = 0;
            $linea->iva_r_c = 0;
            $linea->imp_c_c = 0;
            $linea->total_c = 0;
            $linea->cxc = 0;
            $linea->cxp = 0;
            $linea->saldoproveedor = 0;
            $linea->terminos_pago_cliente_id = $terminos->id;
            $linea->estatus_linea_cliente_id = $terminos->estatus; 

            $linea->save();

            $data = [
                'importe' => $proyecto->importe + $total_linea,
                'saldo' => $proyecto->saldo + $total_linea,
                'subtotal' => $proyecto->subtotal + $subtotal_linea,
                'iva_t' => $proyecto->iva_t + $iva_t_linea,
                'isr_r' => $proyecto->isr_r + $isr_r_linea,
                'iva_r' => $proyecto->iva_r + $iva_r_linea,
                'imp_c' => $proyecto->imp_c + $imp_c_linea,
            ];
            
            $proy = DB::table('proyectos')
                ->where('id','=',$id)
                ->update($data);

            $inf = 'la partida del proyecto se agregó con éxito...';
            session()->flash('Exito',$inf);
            return redirect()->route('proyectos.lineas', ['id' => $id])->with('message',$inf);
        }
        else{
            $inf = 'El producto para la sucursal seleccionada ya existe en el proyecto...';
            session()->flash('Error',$inf);
            return redirect()->route('proyectos.lineas', ['id' => $id])->with('error',$inf);
        };
        
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

            $productos = Producto::all();

            $terminos = TerminosPagoCliente::all();

            return view('proyecto.linea.edit', ['cliente' => $cliente,'linea' => $linea,'proyecto' => $proyecto, 'sucursal' => $sucursal, 'producto' => $producto, 'terminos' => $terminos, 'productos' => $productos]);
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

        $producto = Producto::where('id','=',$linea->producto_id)->first();

        $terminos =DB::table('terminos_pago_clientes')
            ->Join('movimientos_pago_clientes', function (JoinClause $join) {
                $join->on('terminos_pago_clientes.id', '=', 'movimientos_pago_clientes.terminos_pago_cliente_id')
                ->where('movimientos_pago_clientes.secuencia', '=', 1);
            })
            ->select('terminos_pago_clientes.*','movimientos_pago_clientes.estatus_linea_cliente_id as estatus')
            ->where('terminos_pago_clientes.id','=',$request->termino)
            ->first();

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

        if($producto->iva <> 16){
            $iva_t = $producto->iva;
            $iva_r = $producto->iva;    
        }

        $subtotal_linea = $request->cantidad * $request->precio;
        $iva_t_linea = $subtotal_linea * ($iva_t / 100);
        $isr_r_linea = $subtotal_linea * ($isr_r / 100);
        $iva_r_linea = $subtotal_linea * ($iva_r / 100);
        $imp_c_linea = $subtotal_linea * ($imp_c / 100);
        $total_linea = $subtotal_linea + $iva_t_linea - $isr_r_linea - $iva_r_linea - $imp_c_linea;

        $difs = $subtotal_linea - $linea->subtotal;
        $dift = $total_linea - $linea->total;
        $saldo = $linea->saldocliente + $dift;
        
        $lineas = DB::table('proyecto_lineas')
                ->where('id','=',$idl)
                ->update([
                    'cantidad'=> $request->cantidad,
                    'precio'=> $request->precio,
                    'subtotal_v'=> $subtotal_linea,
                    'iva_t_v'=> $iva_t_linea,
                    'isr_r_v'=> $isr_r_linea,
                    'iva_r_v'=> $iva_r_linea,
                    'imp_c_v'=> $imp_c_linea,
                    'total_v'=> $total_linea,
                    'saldocliente'=> $saldo,
                    'terminos_pago_cliente_id'=> $request->termino,
                    'estatus_linea_cliente_id'=> $terminos->estatus,
                    'producto_id'=> $request->producto,
                ]
            );
        
        $subtotal_p = $proyecto->subtotal + $difs;
        $iva_t_p = $subtotal_p * ($iva_t / 100);
        $isr_r_p = $subtotal_p * ($isr_r / 100);
        $iva_r_p = $subtotal_p * ($iva_r / 100);
        $imp_c_p = $subtotal_p * ($imp_c / 100);
        $total_p = $subtotal_p + $iva_t_p - $isr_r_p - $iva_r_p - $imp_c_p;

        $data = [
            'subtotal' => $subtotal_p,
            'iva_t' => $iva_t_p,
            'isr_r' => $isr_r_p,
            'iva_r' => $iva_r_p,
            'imp_c' => $imp_c_p,
            'importe' => $total_p,
            'saldo' => $proyecto->saldo + $dift,
        ];
        
        $proy = DB::table('proyectos')
            ->where('id','=',$idp)
            ->update($data);
        
        $inf = 'Las sucursales se agregaron con éxito...';
        session()->flash('Exito',$inf);
        return redirect()->route('proyectos.lineas', ['id' => $idp])->with('message',$inf);
    }


    public function delete($idp,$idl)
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

            $productos = Producto::all();

            $terminos = TerminosPagoCliente::all();

            return view('proyecto.linea.delete', ['cliente' => $cliente,'linea' => $linea,'proyecto' => $proyecto, 'sucursal' => $sucursal, 'producto' => $producto, 'terminos' => $terminos, 'productos' => $productos]);
        }
        else{
            session()->flash('Error','El estatus del proyecto no permite eliminación de las partidas...');
            $inf = 1;
        
            return redirect()->route('proyectos.lineas', ['id' => $idp])->with('info',$inf);
        };
        
        
        
    }

    public function destroy(Request $request,$idp,$idl)
    {
        $proyecto = DB::table('proyectos')
            ->leftjoin('estados_proyectos', 'estados_proyectos.id', '=', 'proyectos.estados_proyecto_id')
            ->select('proyectos.*','estados_proyectos.nombre as estado','estados_proyectos.id as estado_id')
            ->where('proyectos.id','=',$idp)->first();

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
    
        $linea = ProyectoLinea::where('id','=',$idl)->first();        

        $subtotal = $linea->subtotal_v;
        $saldo = $linea->saldocliente;

        $subtotal_p = $proyecto->subtotal - $subtotal;
        $iva_t_p = $subtotal_p * ($iva_t / 100);
        $isr_r_p = $subtotal_p * ($isr_r / 100);
        $iva_r_p = $subtotal_p * ($iva_r / 100);
        $imp_c_p = $subtotal_p * ($imp_c / 100);
        $total_p = $subtotal_p + $iva_t_p - $isr_r_p - $iva_r_p - $imp_c_p;
        
        $lineas = DB::table('proyecto_lineas')
                ->where('id','=',$idl)
                ->delete();
        
        $data = [
            'subtotal' => $subtotal_p,
            'iva_t' => $iva_t_p,
            'isr_r' => $isr_r_p,
            'iva_r' => $iva_r_p,
            'imp_c' => $imp_c_p,
            'importe' => $total_p,
            'saldo' => $proyecto->saldo - $saldo,
        ];
        
        $proy = DB::table('proyectos')
            ->where('id','=',$idp)
            ->update($data);
        
        //Actualizción de saldos en el presupuesto
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
        }
        
        $inf = 'La partida se eliminó con éxito...';
        session()->flash('Exito',$inf);
        return redirect()->route('proyectos.lineas', ['id' => $idp])->with('message',$inf);
    }

    public function import(Request $request,$idp,$idc){
        $request->validate([
            'importfile' => 'required',
            'tipoimport' => 'required',
        ]);

        $file = $request->file('importfile');
        $tipoimport = $request->tipoimport;

        //$path = $request->importfile->extension();


        //$file->move('C:\xampp');
        //return $file->getRealPath();

        //Excel::import(new ProyectoLineaImport)->import($file, null, \Maatwebsite\Excel\Excel::XLSX);
        //$collection = Excel::toCollection(new ProyectoLineaImport, $file);
        //return $collection;

        $importacion = new Importacion();
        $importacion->proyecto_id = $idp;
        $importacion->cliente_id = $idc;
        $importacion->importacion_proyecto_id = $tipoimport;
        $importacion->fecha = today();
        $importacion->es_procesado = 0;
        $importacion->file = $file->getClientOriginalName();
        $importacion->save();
        
        Excel::import(new ProyectoLineaImport, $file);

        //$errors = ImportacionError::where('importacion_id','=',$importacion->id)
        //->first();

        $errors = DB::table('importacions')
            ->join('importacion_errors', 'importacions.id', '=', 'importacion_errors.importacion_id')
            ->select('importacions.id','importacion_errors.id as errores')
            ->where('importacions.id','=',$importacion->id)
            ->get();

        $numerrores = $errors->count();

        if($numerrores == 0){
            $inf = 'El proyecto se importó con éxito...';
            session()->flash('Exito',$inf);
            return redirect()->route('proyectos.lineas', ['id' => $idp])->with('message',$inf);;
        }
        else{
            $inf = $importacion->id;
            session()->flash('Error','El proyecto se importó con errores...');
            return redirect()->route('proyectos.lineas', ['id' => $idp])->with('info',$inf);
        }
    }

    public function errores($id){
        
        $importacion = Importacion::where('id','=',$id)
            ->first();

        $errores = DB::table('importacions')
            ->join('importacion_errors', 'importacions.id', '=', 'importacion_errors.importacion_id')
            ->where('importacions.id','=',$id)
            ->select('importacion_errors.*')
            ->get();
        
        
        $proyecto = DB::table('proyectos')
        ->leftjoin('estados_proyectos', 'estados_proyectos.id', '=', 'proyectos.estados_proyecto_id')
        ->select('proyectos.*','estados_proyectos.nombre as estado')
        ->where('proyectos.id','=',$importacion->proyecto_id)
        ->first();

        $cliente = Cliente::where('id','=',$proyecto->cliente_id)->first();
        
        return view('proyecto.linea.errors', ['errores' => $errores,'cliente' => $cliente,'proyecto' => $proyecto,'import' => $id,'importacion' => $importacion]);
    }

    public function matriz($id){
        
        $proyecto = DB::table('proyectos')
        ->leftjoin('estados_proyectos', 'estados_proyectos.id', '=', 'proyectos.estados_proyecto_id')
        ->select('proyectos.*','estados_proyectos.nombre as estado')
        ->where('proyectos.id','=',$id)->first();

        $cliente = Cliente::where('id','=',$proyecto->cliente_id)->first();
        
        $productos =DB::table('proyecto_lineas')
        ->join('proyectos', 'proyectos.id', '=', 'proyecto_lineas.proyecto_id')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->leftjoin('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
        ->leftjoin('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('productos.id as producto_id', 'productos.nombre as producto','tipos_productos.nombre as tipo')
        ->where('proyectos.id','=',$id)
        ->groupBy('productos.id', 'productos.nombre','tipos_productos.nombre')
        ->orderBy('productos.nombre','asc')
        ->get();

        $lineas =DB::table('proyecto_lineas')
        ->join('proyectos', 'proyectos.id', '=', 'proyecto_lineas.proyecto_id')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
        ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
        ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
        ->leftjoin('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
        ->leftJoin('estatus_linea_clientes', 'estatus_linea_clientes.id', '=', 'proyecto_lineas.estatus_linea_cliente_id')
        ->leftjoin('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.id as sucursal_id','sucursals.domicilio as domicilio','sucursals.superficie','sucursals.marca',
        'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais','proyectos.id as proyecto_id',
        'productos.id as producto_id', 'productos.nombre as producto','estatus_linea_clientes.nombre as estatus',
        'tipos_productos.nombre as tipo')
        ->where('proyectos.id','=',$id)
        ->orderBy('sucursals.marca','asc')
        ->orderBy('sucursals.id','asc')
        ->orderBy('productos.nombre','asc')
        ->get();

        return view('proyecto.linea.matriz', ['lineas' => $lineas,'cliente' => $cliente,'proyecto' => $proyecto,'id' => $id, 'productos' => $productos]);
    }

    public function matrizcxc($id){
        
        $proyecto = DB::table('proyectos')
        ->leftjoin('estados_proyectos', 'estados_proyectos.id', '=', 'proyectos.estados_proyecto_id')
        ->select('proyectos.*','estados_proyectos.nombre as estado')
        ->where('proyectos.id','=',$id)->first();

        $cliente = Cliente::where('id','=',$proyecto->cliente_id)->first();
        
        $productos =DB::table('proyecto_lineas')
        ->join('proyectos', 'proyectos.id', '=', 'proyecto_lineas.proyecto_id')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->leftjoin('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
        ->leftjoin('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('productos.id as producto_id', 'productos.nombre as producto','tipos_productos.nombre as tipo')
        ->where('proyectos.id','=',$id)
        ->groupBy('productos.id', 'productos.nombre','tipos_productos.nombre')
        ->orderBy('productos.nombre','asc')
        ->get();

        $lineas =DB::table('proyecto_lineas')
        ->join('proyectos', 'proyectos.id', '=', 'proyecto_lineas.proyecto_id')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
        ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
        ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
        ->leftjoin('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
        ->leftJoin('estatus_linea_clientes', 'estatus_linea_clientes.id', '=', 'proyecto_lineas.estatus_linea_cliente_id')
        ->leftjoin('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.id as sucursal_id','sucursals.domicilio as domicilio','sucursals.superficie','sucursals.marca',
        'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais','proyectos.id as proyecto_id',
        'productos.id as producto_id', 'productos.nombre as producto','estatus_linea_clientes.nombre as estatus',
        'tipos_productos.nombre as tipo')
        ->where('proyectos.id','=',$id)
        ->orderBy('sucursals.marca','asc')
        ->orderBy('sucursals.id','asc')
        ->orderBy('productos.nombre','asc')
        ->get();

        return view('proyecto.linea.matrizcxc', ['lineas' => $lineas,'cliente' => $cliente,'proyecto' => $proyecto,'id' => $id, 'productos' => $productos]);
    }

    public function matrizsaldos($id){
        
        $proyecto = DB::table('proyectos')
        ->leftjoin('estados_proyectos', 'estados_proyectos.id', '=', 'proyectos.estados_proyecto_id')
        ->select('proyectos.*','estados_proyectos.nombre as estado')
        ->where('proyectos.id','=',$id)->first();

        $cliente = Cliente::where('id','=',$proyecto->cliente_id)->first();
        
        $productos =DB::table('proyecto_lineas')
        ->join('proyectos', 'proyectos.id', '=', 'proyecto_lineas.proyecto_id')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->leftjoin('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
        ->leftjoin('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('productos.id as producto_id', 'productos.nombre as producto','tipos_productos.nombre as tipo')
        ->where('proyectos.id','=',$id)
        ->groupBy('productos.id', 'productos.nombre','tipos_productos.nombre')
        ->orderBy('productos.nombre','asc')
        ->get();

        $lineas =DB::table('proyecto_lineas')
        ->join('proyectos', 'proyectos.id', '=', 'proyecto_lineas.proyecto_id')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
        ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
        ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
        ->leftjoin('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
        ->leftJoin('estatus_linea_clientes', 'estatus_linea_clientes.id', '=', 'proyecto_lineas.estatus_linea_cliente_id')
        ->leftjoin('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.id as sucursal_id','sucursals.domicilio as domicilio','sucursals.superficie','sucursals.marca',
        'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais','proyectos.id as proyecto_id',
        'productos.id as producto_id', 'productos.nombre as producto','estatus_linea_clientes.nombre as estatus',
        'tipos_productos.nombre as tipo')
        ->where('proyectos.id','=',$id)
        ->orderBy('sucursals.marca','asc')
        ->orderBy('sucursals.id','asc')
        ->orderBy('productos.nombre','asc')
        ->get();

        return view('proyecto.linea.matrizsaldos', ['lineas' => $lineas,'cliente' => $cliente,'proyecto' => $proyecto,'id' => $id, 'productos' => $productos]);
    }

    public function matrizmargen($id){
        
        $proyecto = DB::table('proyectos')
        ->leftjoin('estados_proyectos', 'estados_proyectos.id', '=', 'proyectos.estados_proyecto_id')
        ->select('proyectos.*','estados_proyectos.nombre as estado')
        ->where('proyectos.id','=',$id)->first();

        $cliente = Cliente::where('id','=',$proyecto->cliente_id)->first();
        
        $productos =DB::table('proyecto_lineas')
        ->join('proyectos', 'proyectos.id', '=', 'proyecto_lineas.proyecto_id')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->leftjoin('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
        ->leftjoin('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('productos.id as producto_id', 'productos.nombre as producto','tipos_productos.nombre as tipo')
        ->where('proyectos.id','=',$id)
        ->groupBy('productos.id', 'productos.nombre','tipos_productos.nombre')
        ->orderBy('productos.nombre','asc')
        ->get();

        $lineas =DB::table('proyecto_lineas')
        ->join('proyectos', 'proyectos.id', '=', 'proyecto_lineas.proyecto_id')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
        ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
        ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
        ->leftjoin('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
        ->leftJoin('estatus_linea_clientes', 'estatus_linea_clientes.id', '=', 'proyecto_lineas.estatus_linea_cliente_id')
        ->leftjoin('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.id as sucursal_id','sucursals.domicilio as domicilio','sucursals.superficie','sucursals.marca',
        'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais','proyectos.id as proyecto_id',
        'productos.id as producto_id', 'productos.nombre as producto','estatus_linea_clientes.nombre as estatus',DB::raw('proyecto_lineas.precio - proyecto_lineas.costo as margen'),
        'tipos_productos.nombre as tipo')
        ->where('proyectos.id','=',$id)
        ->orderBy('sucursals.marca','asc')
        ->orderBy('sucursals.id','asc')
        ->orderBy('productos.nombre','asc')
        ->get();

        return view('proyecto.linea.matrizmargen', ['lineas' => $lineas,'cliente' => $cliente,'proyecto' => $proyecto,'id' => $id, 'productos' => $productos]);
    }
}
