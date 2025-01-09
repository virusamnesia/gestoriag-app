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
        
        $rev = ProyectoLinea::where('sucursal_id','=',$request->sucursal)
            ->where('producto_id','=', $request->producto)
            ->where('proyecto_id','=', $id)
            ->first();

        if (!$rev){

            $linea = new ProyectoLinea();

            $linea->proyecto_id = $id;
            $linea->cliente_id = $cliente->id;
            $linea->sucursal_id = $request->sucursal;
            $linea->producto_id = $request->producto;
            $linea->precio = $request->precio;
            $linea->saldocliente = $request->precio;
            $linea->costo = 0;
            $linea->cxc = 0;
            $linea->cxp = 0;
            $linea->saldoproveedor = 0;
            $linea->terminos_pago_cliente_id = $terminos->id;
            $linea->estatus_linea_cliente_id = $terminos->estatus; 

            $linea->save();

            $data = [
                'importe' => $proyecto->importe + $request->precio,
                'saldo' => $proyecto->saldo + $request->precio,
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

        $errors = ImportacionError::where('importacion_id','=',$importacion->id)
        ->get();

        if($errors == null){
            $inf = 0;
            session()->flash('Exito','El proyecto se importó con éxito...');
            return redirect()->route('proyectos.lineas', ['id' => $idp])->with('info',$inf);;
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
}
