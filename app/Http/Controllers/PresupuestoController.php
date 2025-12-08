<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\EstadosPresupuesto;
use App\Models\MovimientosPagoCliente;
use App\Models\Presupuesto;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\ProyectoLinea;
use App\Models\ProyectoSucursalLinea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;

class PresupuestoController extends Controller
{
    public function index(){
        
        $user = Auth::user()->id;

        $acceso = 5;

        $permisos = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->select('users.name','roles.name as role','roles.id as role_id','permissions.name as permission','permissions.id as permission_id')
            ->where('users.id','=', $user)
            ->get();
        
            $permisoa = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->select('users.name','roles.name as role','roles.id as role_id','permissions.name as permission','permissions.id as permission_id')
            ->where('users.id','=', $user)
            ->where('permissions.id','=', 6)
            ->first();
        
        $permisom = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->select('users.name','roles.name as role','roles.id as role_id','permissions.name as permission','permissions.id as permission_id')
            ->where('users.id','=', $user)
            ->where('permissions.id','=', 13)
            ->first();
        
        $permiso = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->select('users.name','roles.name as role','roles.id as role_id','permissions.name as permission','permissions.id as permission_id')
            ->where('users.id','=', $user)
            ->where('permissions.id','=', $acceso)
            ->first();
        
        if ($permiso){
            $presupuestos =DB::table('presupuestos')
            ->join('proveedors', 'proveedors.id', '=', 'presupuestos.proveedor_id')
            ->leftjoin('estados_presupuestos', 'estados_presupuestos.id', '=', 'presupuestos.estados_presupuesto_id')
            ->leftjoin('fiscal_positions', 'fiscal_positions.id', '=', 'presupuestos.fiscal_position_id')
            ->select('presupuestos.*','proveedors.nombre as proveedor','proveedors.id as proveedor_id','estados_presupuestos.nombre as estado',
            'estados_presupuestos.id as estados_presupuesto_id','fiscal_positions.id as posicion_id','fiscal_positions.nombre as posicion')
            ->orderBy('presupuestos.id', 'desc')
            ->get();

            return view('presupuesto.index', ['presupuestos' => $presupuestos,'permisos' => $permisos,'permisoa' => $permisoa,'permisom' => $permisom]);    
        }
        else{
            $inf = 'No cuentas con el permiso de acceso';
            return redirect()->route('dashboard')->with('error',$inf);
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user()->id;

        $acceso = 5;

        $permiso = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->select('users.name','roles.name as role','roles.id as role_id','permissions.name as permission','permissions.id as permission_id')
            ->where('users.id','=', $user)
            ->where('permissions.id','=', $acceso)
            ->first();
        
        if ($permiso){
            $estados = EstadosPresupuesto::all();
            $proveedores = Proveedor::all();

            return view('presupuesto.create', ['proveedores' => $proveedores, 'estados' => $estados]);
        }
        else{
            $inf = 'No cuentas con el permiso de acceso';
            return redirect()->route('presupuestos')->with('error',$inf);
        }
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

        $posicion =DB::table('proveedors')
            ->join('fiscal_positions', 'fiscal_positions.id', '=', 'proveedors.fiscal_position_id')
            ->select('fiscal_positions.*')
            ->where('proveedors.id','=',$request->proveedor)
            ->get();

        foreach ($posicion as $pos){
            $posicion_id = $pos->id;
            $iva_t = $pos->iva_t;
            $isr_r = $pos->isr_r;
            $iva_r = $pos->iva_r;
            $imp_c = $pos->imp_c;
        }

        if (count($lineas) > 0){
            $presupuesto = new Presupuesto();

            $presupuesto->nombre = $request->nombre;
            $presupuesto->anio = $request->año;
            $presupuesto->proveedor_id = $request->proveedor;
            $presupuesto->subtotal = 0;
            $presupuesto->iva_t = 0;
            $presupuesto->isr_r = 0;
            $presupuesto->iva_r = 0;
            $presupuesto->imp_c = 0;
            $presupuesto->importe = 0;
            $presupuesto->saldo = 0;
            $presupuesto->cxp = 0;
            $presupuesto->estados_presupuesto_id = 1;
            $presupuesto->fecha_cotizacion = now();
            $presupuesto->autorizar = 0;
            $presupuesto->fiscal_position_id = $posicion_id;

            $presupuesto->save();

            $id = $presupuesto->id;

            $proveedor = Proveedor::where('id','=', $request->proveedor)->first();
            $inf = 'El presupuesto se agregó con éxito...';
            session()->flash('Exito',$inf);
            return view('presupuesto.linea.clientes', ['id' => $id,'lineas' => $lineas,'presupuesto' => $presupuesto,'proveedor' => $proveedor])->with('message',$inf);
        }
        else{
            $inf = 'No existen gestiones para este proveedor...';
            session()->flash('Error',$inf);
            return redirect()->route('presupuestos')->with('error',$inf);
        }

    }

    public function products($idp,$idv,$idc)
    {
        $user = Auth::user()->id;

        $acceso = 6;

        $permiso = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->select('users.name','roles.name as role','roles.id as role_id','permissions.name as permission','permissions.id as permission_id')
            ->where('users.id','=', $user)
            ->where('permissions.id','=', $acceso)
            ->first();
        
        if ($permiso){
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
            $inf = 'Selecciona los productos para el presupuesto...';
            session()->flash('Exito',$inf);
            return view('presupuesto.linea.productos', ['idp' => $idp,'lineas' => $lineas,'presupuesto' => $presupuesto,'proveedor' => $proveedor,'cliente' => $cliente,'idv' => $idv,'idc' => $idc])->with('message',$inf);    
        }
        else{
            $inf = 'No cuentas con el permiso de acceso';
            return redirect()->route('presupuestos')->with('error',$inf);
        }
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
        $user = Auth::user()->id;

        $acceso = 5;

        $permiso = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->select('users.name','roles.name as role','roles.id as role_id','permissions.name as permission','permissions.id as permission_id')
            ->where('users.id','=', $user)
            ->where('permissions.id','=', $acceso)
            ->first();
        
        if ($permiso){
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

            $inf = 'Selecciona los productos para el presupuesto...';
            session()->flash('Exito',$inf);
            return view('presupuesto.productos', ['id' => $id,'lineas' => $lineas,'presupuesto' => $presupuesto,'proveedor' => $proveedor])->with('message',$inf);   
        }
        else{
            $inf = 'No cuentas con el permiso de acceso';
            return redirect()->route('presupuestos')->with('error',$inf);
        }
     }

    public function updatecostos(Request $request, $id)
    {
        $user = Auth::user()->id;

        $acceso = 5;

        $permiso = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->select('users.name','roles.name as role','roles.id as role_id','permissions.name as permission','permissions.id as permission_id')
            ->where('users.id','=', $user)
            ->where('permissions.id','=', $acceso)
            ->first();
        
        if ($permiso){
            $presupuesto = Presupuesto::where('id','=', $id)->first();
            
            $lineas =DB::table('proyecto_lineas')
                ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
                ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
                ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
                ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
                ->leftJoin('productos', 'proyecto_lineas.producto_id', '=', 'productos.id')
                ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
                ->select('productos.id as producto_id','proyecto_lineas.id as linea_id','proyecto_lineas.costo','')
                ->where('proyecto_lineas.presupuesto_id','=',$id)
                ->where('proyecto_lineas.proveedor_id','=',$presupuesto->presupuesto_id)
                ->orderBy('productos.nombre')
                ->get();

            $posicion =DB::table('presupuestos')
            ->join('fiscal_positions', 'fiscal_positions.id', '=', 'presupuestos.fiscal_position_id')
            ->select('fiscal_positions.*')
            ->where('presupuestos.id','=',$id)
            ->get();

            foreach ($posicion as $pos){
                $posicion_id = $pos->id;
                $iva_t = $pos->iva_t;
                $isr_r = $pos->isr_r;
                $iva_r = $pos->iva_r;
                $imp_c = $pos->imp_c;
            }
            
            $subtotal = 0;
            $total = 0; 

            foreach ($lineas as $row){
                $input = "costo".$row->producto_id;
                $cant = "cant".$row->producto_id;
                if ($request->$input > 0){
                    
                    
                    $subtotal_linea = $row->cant * $request->input;
                    $iva_t_linea = $subtotal_linea * ($iva_t / 100);
                    $isr_r_linea = $subtotal_linea * ($isr_r / 100);
                    $iva_r_linea = $subtotal_linea * ($iva_r / 100);
                    $imp_c_linea = $subtotal_linea * ($imp_c / 100);
                    $total_linea = $subtotal_linea + $iva_t_linea - $isr_r_linea - $iva_r_linea - $imp_c_linea;

                    $subtotal += $subtotal_linea;
                    $total += $total_linea;

                    $line = DB::table('proyecto_lineas')
                        ->where('id','=', $row->linea_id)
                        ->update([
                        'costo'=> $request->$input,
                        'subtotal_c'=> $subtotal_linea,
                        'iva_t_c'=> $iva_t_linea,
                        'isr_r_c'=> $isr_r_linea,
                        'iva_r_c'=> $iva_r_linea,
                        'imp_c_c'=> $imp_c_linea,
                        'total_c'=> $total_linea,
                    ]);
                }
                else{
                    if($row->costo > 0){
                        $subtotal += $row->subtotal_c;
                        $total += $row->total_c;
                    }
                }
            }

            $iva_t_p = $subtotal * ($iva_t / 100);
            $isr_r_p = $subtotal * ($isr_r / 100);
            $iva_r_p = $subtotal * ($iva_r / 100);
            $imp_c_p = $subtotal * ($imp_c / 100);
            $total_p = $subtotal + $iva_t_p - $isr_r_p - $iva_r_p - $imp_c_p;
            
            $data = [
                'importe' => $total_p,
                'subtotal' => $total_p,
                'iva_t' => $total_p,
                'isr_r' => $total_p,
                'iva_r' => $total_p,
                'imp_c' => $total_p,
            ];
            
            $pres = DB::table('presupuestos')
                ->where('id','=', $id)
                ->update($data);

            $inf = 'Los costos se modificarón con éxito...';
            session()->flash('Exito',$inf);
            return redirect()->route('presupuestos')->with('message',$inf);   
        }
        else{
            $inf = 'No cuentas con el permiso de acceso';
            return redirect()->route('presupuestos')->with('error',$inf);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $user = Auth::user()->id;

        $acceso = 5;

        $permiso = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->select('users.name','roles.name as role','roles.id as role_id','permissions.name as permission','permissions.id as permission_id')
            ->where('users.id','=', $user)
            ->where('permissions.id','=', $acceso)
            ->first();
        
        if ($permiso){
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
        ->where('proveedors.id','=',1)
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
        ->where('proveedors.id','=',1)
        ->orderBy('sucursals.nombre')
        ->get();    
        }
        else{
            $inf = 'No cuentas con el permiso de acceso';
            return redirect()->route('presupuestos')->with('error',$inf);
        }
        
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
        $user = Auth::user()->id;

        $acceso = 5;

        $permiso = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->select('users.name','roles.name as role','roles.id as role_id','permissions.name as permission','permissions.id as permission_id')
            ->where('users.id','=', $user)
            ->where('permissions.id','=', $acceso)
            ->first();
        
        if ($permiso){
            $presupuesto = Presupuesto::where('id','=', $id)->first();
            
            $linea = ProyectoLinea::where('id','=',$request->id)->first();

            $producto = Producto::where('id','=',$linea->producto_id)->first();

            /*$terminos =DB::table('terminos_pago_clientes')
                ->Join('movimientos_pago_clientes', function (JoinClause $join) {
                    $join->on('terminos_pago_clientes.id', '=', 'movimientos_pago_clientes.terminos_pago_cliente_id')
                    ->where('movimientos_pago_clientes.secuencia', '=', 1);
                })
                ->select('terminos_pago_clientes.*','movimientos_pago_clientes.estatus_linea_cliente_id as estatus')
                ->where('terminos_pago_clientes.id','=',$request->termino)
                ->first();*/

            $posicion =DB::table('presupuestos')
            ->join('fiscal_positions', 'fiscal_positions.id', '=', 'presupuestos.fiscal_position_id')
            ->select('fiscal_positions.*')
            ->where('presupuestos.id','=',$id)
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
            
            $subtotal_linea = $request->cant * $request->costo;
            $iva_t_linea = $subtotal_linea * ($iva_t / 100);
            $isr_r_linea = $subtotal_linea * ($isr_r / 100);
            $iva_r_linea = $subtotal_linea * ($iva_r / 100);
            $imp_c_linea = $subtotal_linea * ($imp_c / 100);
            $total_linea = $subtotal_linea + $iva_t_linea - $isr_r_linea - $iva_r_linea - $imp_c_linea;

            $difs = $subtotal_linea - $linea->subtotal_c;
            $difit = $iva_t_linea - $linea->iva_t_c;
            $difir = $isr_r_linea - $linea->isr_r_c;
            $difvr = $iva_r_linea - $linea->iva_r_c;
            $dific = $imp_c_linea - $linea->imp_c_c;
            $dift = $total_linea - $linea->total_c;
            $saldo = $linea->saldoproveedor + $dift;
            
            $lineas = DB::table('proyecto_lineas')
                    ->where('id','=',$request->id)
                    ->update([
                        /*'cantidad'=> $request->cant,*/
                        'costo'=> $request->costo,
                        'subtotal_c'=> $subtotal_linea,
                        'iva_t_c'=> $iva_t_linea,
                        'isr_r_c'=> $isr_r_linea,
                        'iva_r_c'=> $iva_r_linea,
                        'imp_c_c'=> $imp_c_linea,
                        'total_c'=> $total_linea,
                        'saldoproveedor'=> $saldo,
                        /*'terminos_pago_cliente_id'=> $request->termino,
                        'estatus_linea_cliente_id'=> $terminos->estatus,
                        'producto_id'=> $request->producto,
                        'obs_c'=> $request->obs,*/
                    ]
                );
            
            $data = [
                'subtotal' => $presupuesto->subtotal + $difs,
                'iva_t' => $presupuesto->iva_t + $difit,
                'isr_r' => $presupuesto->isr_r + $difir,
                'iva_r' => $presupuesto->iva_r + $difvr,
                'imp_c' => $presupuesto->imp_c + $dific,
                'importe' => $presupuesto->importe + $dift,
                'saldo' => $presupuesto->saldo + $dift,
            ];
            
            $pres = DB::table('presupuestos')
                ->where('id','=', $id)
                ->update($data);

            $inf = 'El costo se modificó con éxito...'.$producto->iva;
            session()->flash('Exito',$inf);
            return redirect()->route('presupuestos.lineas', ['id' => $id])->with('message',$inf);   
        }
        else{
            $inf = 'No cuentas con el permiso de acceso';
            return redirect()->route('presupuestos.lineas', ['id' => $id])->with('error',$inf);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function destroyline($idp,$idl)
    {
        $user = Auth::user()->id;

        $acceso = 5;

        $permiso = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->select('users.name','roles.name as role','roles.id as role_id','permissions.name as permission','permissions.id as permission_id')
            ->where('users.id','=', $user)
            ->where('permissions.id','=', $acceso)
            ->first();
        
        if ($permiso){
            $presupuesto = Presupuesto::find($idp);

            if($presupuesto->autorizar == 0){
                $data = [
                    'costo' => 0,
                    'subtotal_c' => 0,
                    'iva_t_c' => 0,
                    'isr_r_c' => 0,
                    'iva_r_c' => 0,
                    'imp_c_c' => 0,
                    'total_c' => 0,
                    'saldoproveedor' => 0,
                    'proveedor_id' => NULL,
                    'presupuesto_id' => NULL,
                ];
                
                $linea = DB::table('proyecto_lineas')
                    ->where('id','=',$idl)
                    ->update($data);
                
                $presupuesto = DB::table('proyecto_lineas')
                ->select(DB::raw('SUM(costo) AS `costototal`'))
                ->where('proyecto_lineas.presupuesto_id','=',$idp)
                ->first();
    
                $data = [
                    'importe' => $presupuesto->costototal,
                    'saldo' => $presupuesto->costototal,
                    'autorizar' => 0,
                ];
                
                $presupuesto = DB::table('presupuestos')
                    ->where('id','=',$idp)
                    ->update($data);
    
                $inf = 1;
                session()->flash('Exito','La línea fue eliminada del presupuesto con éxito...');
                return redirect()->route('presupuestos')->with('info',$inf);
            }
            else{
                $inf = 'El presupuesto esta autorizado, no es posible la acción solicitada';
                return redirect()->route('presupuestos')->with('error',$inf);
            }
               
        }
        else{
            $inf = 'No cuentas con el permiso de acceso';
            return redirect()->route('presupuestos')->with('error',$inf);
        }
    }

    public function cancelar($id)
    {
        $user = Auth::user()->id;

        $acceso = 15;

        $permiso = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->select('users.name','roles.name as role','roles.id as role_id','permissions.name as permission','permissions.id as permission_id')
            ->where('users.id','=', $user)
            ->where('permissions.id','=', $acceso)
            ->first();
        
        if ($permiso){
            $presupuesto = Presupuesto::find($id);

            if($presupuesto->autorizar == 0){
                $data = [
                    'costo' => 0,
                    'subtotal_c' => 0,
                    'iva_t_c' => 0,
                    'isr_r_c' => 0,
                    'iva_r_c' => 0,
                    'imp_c_c' => 0,
                    'total_c' => 0,
                    'saldoproveedor' => 0,
                    'proveedor_id' => NULL,
                    'presupuesto_id' => NULL,
                ];
                
                $linea = DB::table('proyecto_lineas')
                    ->where('presupuesto_id','=',$id)
                    ->update($data);
                
                $data = [
                    'importe' => 0,
                    'saldo' => 0,
                    'autorizar' => 0,
                    'estados_presupuesto_id' => 6,
                    'fecha_cancelacion' => now(),
                ];
                
                $presupuesto = DB::table('presupuestos')
                    ->where('id','=',$id)
                    ->update($data);
    
                $inf = 1;
                session()->flash('Exito','El presupuesto fue cancelado con éxito...');
                return redirect()->route('presupuestos')->with('info',$inf);
            }
            else{
                $inf = 'El presupuesto esta autorizado, no es posible la acción solicitada';
                return redirect()->route('presupuestos')->with('error',$inf);
            }   
        }
        else{
            $inf = 'No cuentas con el permiso de acceso';
            return redirect()->route('presupuestos')->with('error',$inf);
        }
    }

    /**
     * Mass price assignation to buget products.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        $user = Auth::user()->id;

        $acceso = 15;

        $permiso = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->select('users.name','roles.name as role','roles.id as role_id','permissions.name as permission','permissions.id as permission_id')
            ->where('users.id','=', $user)
            ->where('permissions.id','=', $acceso)
            ->first();
        
        if ($permiso){
            $presupuesto = Presupuesto::find($id);

            if($presupuesto->estados_presupuesto_id == 6){
                
                $lineas = DB::table('presupuestos')
                ->where('id','=',$id)
                ->delete();
    
                $inf = 1;
                session()->flash('Exito','El presupuesto fue eliminado con éxito...');
                return redirect()->route('presupuestos')->with('info',$inf);
            }
            else{
                $inf = 'El presupuesto debe cancelarse primero';
                return redirect()->route('presupuestos')->with('error',$inf);
            }   
        }
        else{
            $inf = 'No cuentas con el permiso de acceso';
            return redirect()->route('presupuestos')->with('error',$inf);
        }
    }

    /**
     * Mass price assignation to buget products.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function auth($id)
    {
        $user = Auth::user()->id;

        $acceso = 6;

        $permiso = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->select('users.name','roles.name as role','roles.id as role_id','permissions.name as permission','permissions.id as permission_id')
            ->where('users.id','=', $user)
            ->where('permissions.id','=', $acceso)
            ->first();
        
        if ($permiso){
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
                        ->select('proyecto_sucursal_lineas.id as mov_id','movimientos_pago_clientes.valor_proveedor as mov_porc','proyecto_lineas.id as linea_id',
                        'proyecto_lineas.costo','movimientos_pago_clientes.valor_proveedor')
                        ->where('proyecto_lineas.presupuesto_id','=',$id)
                        ->where('proyecto_lineas.id','=',$row->linea_id)
                        ->where('proyecto_lineas.proveedor_id','=',$presupuesto->proveedor_id)
                        ->get();
                    $cxp = 0;
                    $saldo = $row->costo;
                    foreach($movs as $m){
                        $importe = $row->costo * ($m->mov_porc/100);
                        $cxp += $importe;
                        $saldo = $saldo - $importe;
                        if($importe > 0){
                            $data = [
                                'importe_proveedor'=> $importe,
                                'saldo_proveedor'=> $saldo,
                                'es_facturable'=> 1,
                                'proveedor_id'=> $presupuesto->proveedor_id,
                            ];
                        }
                        else{
                            $data = [
                                'importe_proveedor'=> $importe,
                                'saldo_proveedor'=> $saldo,
                                'proveedor_id'=> $presupuesto->proveedor_id,
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
                    'fecha_autorizacion' => now(),
                    'autorizar' => 1,
                ];
                
                $pres = DB::table('presupuestos')
                    ->where('id','=', $id)
                    ->update($data);
        
                $inf = 'El presupuesto se autorizó con éxito...';
                session()->flash('Exito',$inf);
                return redirect()->route('presupuestos')->with('message',$inf);
            }   
        }
        else{
            $inf = 'No cuentas con el permiso de acceso';
            return redirect()->route('presupuestos')->with('error',$inf);
        }
    }

    public function updatePrice(Request $request,$id)
    {
        $user = Auth::user()->id;

        $acceso = 5;

        $permiso = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->select('users.name','roles.name as role','roles.id as role_id','permissions.name as permission','permissions.id as permission_id')
            ->where('users.id','=', $user)
            ->where('permissions.id','=', $acceso)
            ->first();
        
        if ($permiso){
            $lineas =DB::table('proyecto_lineas')
                ->leftJoin('productos', 'proyecto_lineas.producto_id', '=', 'productos.id')
                ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
                ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
                ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
                ->select('productos.id as producto_id', 'productos.nombre as producto','tipos_productos.nombre as tipo')
                ->where('proyecto_lineas.id','=',$request->id)
                ->orderBy('productos.id')
                ->orderBy('productos.nombre')
                ->orderBy('tipos_productos.nombre')
                ->first();


            $data = [
                'costo' => $request->costo,
                'saldoproveedor' => $request->costo,
            ];
            
            $linea = DB::table('proyecto_lineas')
                ->where('id','=',$request->id)
                ->update($data);
            
            $presupuesto = DB::table('proyecto_lineas')
            ->select(DB::raw('SUM(costo) AS `costototal`'))
            ->where('proyecto_lineas.presupuesto_id','=',$id)
            ->first();

            $data = [
                'importe' => $presupuesto->costototal,
                'saldo' => $presupuesto->costototal,
                'autorizar' => 0,
            ];
            
            $presupuesto = DB::table('presupuestos')
                ->where('id','=',$id)
                ->update($data);

            $inf = 1;
            session()->flash('Exito','El prresupuesto fue autorizado con éxito...');
            return redirect()->route('presupuestos')->with('info',$inf);   
        }
        else{
            $inf = 'No cuentas con el permiso de acceso';
            return redirect()->route('presupuestos')->with('error',$inf);
        }
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
            ->join('proyectos', 'proyectos.id', '=', 'proyecto_lineas.proyecto_id')
            ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
            ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
            ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
            ->leftJoin('proveedors', 'proyecto_lineas.proveedor_id', '=', 'proveedors.id')
            ->leftjoin('clientes', 'clientes.id', '=', 'sucursals.cliente_id')
            ->leftJoin('productos', 'proyecto_lineas.producto_id', '=', 'productos.id')
            ->leftJoin('estatus_linea_clientes', 'estatus_linea_clientes.id', '=', 'proyecto_lineas.estatus_linea_cliente_id')
            ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
            ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.domicilio as domicilio',
            'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais','estatus_linea_clientes.nombre as estatus',
            'proveedors.id as proveedor_id','proveedors.nombre as proveedor','productos.id as producto_id', 'productos.nombre as producto','tipos_productos.nombre as tipo',
            'sucursals.marca','proyectos.nombre as proyecto')
            ->where('proyecto_lineas.presupuesto_id','=',$id)
            ->orderBy('sucursals.nombre')
            ->get();

        

        $inf = 1;
        return view('presupuesto.linea.lineas', ['id' => $id,'lineas' => $lineas,'presupuesto' => $presupuesto,'proveedor' => $proveedor])->with('info',$inf);

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
            $sel = "sel".$row->producto_id;
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

        $inf = 'El presupuesto fue creado con éxito...';
        session()->flash('Exito',$inf);
        return redirect()->route('presupuestos')->with('message',$inf);

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

        $inf = 'No existen más acciones que agregar...';

        if($next == null){
            session()->flash('Error',$inf);
            return redirect()->route('proyectos.lineas', ['id' => $idp])->with('error',$inf);
        }
        else{
            return view('presu´puesto.movimiento.create', ['idp' => $idp,'idl' => $idl,'presupuesto' => $presupuesto,'proveedor' => $proveedor,
            'linea' => $linea,'next' => $next]);
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
            $inf = 'La actualización se agregó con éxito...';
            session()->flash('Exito',$inf);
            return redirect()->route('presupuesto.lineas', ['id' => $idp])->with('message',$inf);
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

    public function matriz($id){
        
        $presupuesto = DB::table('presupuestos')
        ->leftjoin('estados_presupuestos', 'estados_presupuestos.id', '=', 'presupuestos.estados_presupuesto_id')
        ->select('presupuestos.*','estados_presupuestos.nombre as estado')
        ->where('presupuestos.id','=',$id)->first();

        $proveedor = Proveedor::where('id','=',$presupuesto->proveedor_id)->first();
        
        $productos =DB::table('proyecto_lineas')
        ->join('presupuestos', 'presupuestos.id', '=', 'proyecto_lineas.presupuesto_id')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->leftjoin('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
        ->leftjoin('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('productos.id as producto_id', 'productos.nombre as producto','tipos_productos.nombre as tipo')
        ->where('presupuestos.id','=',$id)
        ->groupBy('productos.id', 'productos.nombre','tipos_productos.nombre')
        ->orderBy('productos.nombre','asc')
        ->get();

        $lineas =DB::table('proyecto_lineas')
        ->join('presupuestos', 'presupuestos.id', '=', 'proyecto_lineas.presupuesto_id')
        ->join('proyectos', 'proyectos.id', '=', 'proyecto_lineas.proyecto_id')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
        ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
        ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
        ->leftjoin('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
        ->leftJoin('estatus_linea_clientes', 'estatus_linea_clientes.id', '=', 'proyecto_lineas.estatus_linea_cliente_id')
        ->leftjoin('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.id as sucursal_id','sucursals.domicilio as domicilio','sucursals.superficie','sucursals.marca',
        'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais','presupuestos.id as presupuesto_id',
        'productos.id as producto_id', 'productos.nombre as producto','estatus_linea_clientes.nombre as estatus',
        'tipos_productos.nombre as tipo','proyectos.nombre as proyecto','proyectos.id as proyecto_id')
        ->where('presupuestos.id','=',$id)
        ->orderBy('proyectos.id','asc')
        ->orderBy('sucursals.marca','asc')
        ->orderBy('sucursals.id','asc')
        ->orderBy('productos.nombre','asc')
        ->get();

        return view('presupuesto.linea.matriz', ['lineas' => $lineas,'proveedor' => $proveedor,'presupuesto' => $presupuesto,'id' => $id, 'productos' => $productos]);
    }

    public function matrizcxp($id){
        
        $presupuesto = DB::table('presupuestos')
        ->leftjoin('estados_presupuestos', 'estados_presupuestos.id', '=', 'presupuestos.estados_presupuesto_id')
        ->select('presupuestos.*','estados_presupuestos.nombre as estado')
        ->where('presupuestos.id','=',$id)->first();

        $proveedor = Proveedor::where('id','=',$presupuesto->proveedor_id)->first();
        
        $productos =DB::table('proyecto_lineas')
        ->join('presupuestos', 'presupuestos.id', '=', 'proyecto_lineas.presupuesto_id')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->leftjoin('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
        ->leftjoin('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('productos.id as producto_id', 'productos.nombre as producto','tipos_productos.nombre as tipo')
        ->where('presupuestos.id','=',$id)
        ->groupBy('productos.id', 'productos.nombre','tipos_productos.nombre')
        ->orderBy('productos.nombre','asc')
        ->get();

        $lineas =DB::table('proyecto_lineas')
        ->join('presupuestos', 'presupuestos.id', '=', 'proyecto_lineas.presupuesto_id')
        ->join('proyectos', 'proyectos.id', '=', 'proyecto_lineas.proyecto_id')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
        ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
        ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
        ->leftjoin('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
        ->leftJoin('estatus_linea_clientes', 'estatus_linea_clientes.id', '=', 'proyecto_lineas.estatus_linea_cliente_id')
        ->leftjoin('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.id as sucursal_id','sucursals.domicilio as domicilio','sucursals.superficie','sucursals.marca',
        'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais','presupuestos.id as presupuesto_id',
        'productos.id as producto_id', 'productos.nombre as producto','estatus_linea_clientes.nombre as estatus',
        'tipos_productos.nombre as tipo','proyectos.nombre as proyecto','proyectos.id as proyecto_id')
        ->where('presupuestos.id','=',$id)
        ->orderBy('proyectos.id','asc')
        ->orderBy('sucursals.marca','asc')
        ->orderBy('sucursals.id','asc')
        ->orderBy('productos.nombre','asc')
        ->get();

        return view('presupuesto.linea.matrizcxp', ['lineas' => $lineas,'proveedor' => $proveedor,'presupuesto' => $presupuesto,'id' => $id, 'productos' => $productos]);
    }

    public function matrizsaldos($id){
        
        $presupuesto = DB::table('presupuestos')
        ->leftjoin('estados_presupuestos', 'estados_presupuestos.id', '=', 'presupuestos.estados_presupuesto_id')
        ->select('presupuestos.*','estados_presupuestos.nombre as estado')
        ->where('presupuestos.id','=',$id)->first();

        $proveedor = Proveedor::where('id','=',$presupuesto->proveedor_id)->first();
        
        $productos =DB::table('proyecto_lineas')
        ->join('presupuestos', 'presupuestos.id', '=', 'proyecto_lineas.presupuesto_id')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->leftjoin('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
        ->leftjoin('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('productos.id as producto_id', 'productos.nombre as producto','tipos_productos.nombre as tipo')
        ->where('presupuestos.id','=',$id)
        ->groupBy('productos.id', 'productos.nombre','tipos_productos.nombre')
        ->orderBy('productos.nombre','asc')
        ->get();

        $lineas =DB::table('proyecto_lineas')
        ->join('presupuestos', 'presupuestos.id', '=', 'proyecto_lineas.presupuesto_id')
        ->join('proyectos', 'proyectos.id', '=', 'proyecto_lineas.proyecto_id')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
        ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
        ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
        ->leftjoin('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
        ->leftJoin('estatus_linea_clientes', 'estatus_linea_clientes.id', '=', 'proyecto_lineas.estatus_linea_cliente_id')
        ->leftjoin('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.id as sucursal_id','sucursals.domicilio as domicilio','sucursals.superficie','sucursals.marca',
        'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais','presupuestos.id as presupuesto_id',
        'productos.id as producto_id', 'productos.nombre as producto','estatus_linea_clientes.nombre as estatus',
        'tipos_productos.nombre as tipo','proyectos.nombre as proyecto','proyectos.id as proyecto_id')
        ->where('presupuestos.id','=',$id)
        ->orderBy('proyectos.id','asc')
        ->orderBy('sucursals.marca','asc')
        ->orderBy('sucursals.id','asc')
        ->orderBy('productos.nombre','asc')
        ->get();

        return view('presupuesto.linea.matrizsaldos', ['lineas' => $lineas,'proveedor' => $proveedor,'presupuesto' => $presupuesto,'id' => $id, 'productos' => $productos]);
    }
}
