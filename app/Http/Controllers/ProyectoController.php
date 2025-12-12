<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\EstadosProyecto;
use App\Models\ListasPrecio;
use App\Models\MovimientosPagoCliente;
use App\Models\Proyecto;
use App\Models\ProyectoLinea;
use App\Models\ProyectoSucursalLinea;
use App\Models\Sucursal;
use App\Models\sucursales_proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProyectoController extends Controller
{
    public function index(){
        
        $user = Auth::user()->id;

        $acceso = 2;

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
            ->where('permissions.id','=', 4)
            ->first();
        
        $permisom = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->select('users.name','roles.name as role','roles.id as role_id','permissions.name as permission','permissions.id as permission_id')
            ->where('users.id','=', $user)
            ->where('permissions.id','=', 1)
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
            $proyectos =DB::table('proyectos')
                ->join('clientes', 'clientes.id', '=', 'proyectos.cliente_id')
                ->leftjoin('estados_proyectos', 'estados_proyectos.id', '=', 'proyectos.estados_proyecto_id')
                ->leftjoin('fiscal_positions', 'fiscal_positions.id', '=', 'proyectos.fiscal_position_id')
                ->select('proyectos.*','clientes.nombre as cliente','clientes.id as cliente_id','estados_proyectos.nombre as estado',
                'estados_proyectos.id as estados_proyecto_id','fiscal_positions.id as posicion_id','fiscal_positions.nombre as posicion')
                ->orderBy('proyectos.id', 'desc')
                ->get();

            return view('proyecto.index', ['proyectos' => $proyectos, 'user' => $user,'permisos' => $permisos,'permisoa' => $permisoa,'permisom' => $permisom]);
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

        $acceso = 2;

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
            $estados = EstadosProyecto::all();
            $clientes = Cliente::all();

            return view('proyecto.create', ['clientes' => $clientes, 'estados' => $estados]);    
        }
        else{
            $inf = 'No cuentas con el permiso de acceso';
            return redirect()->route('proyectos')->with('error',$inf);
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
        $validacion = $request->validate([
            'nombre' => 'required|string|max:225',
            'año' => 'required',
            'cliente' => 'required',
            'agrupado' => 'required',
        ]);

        $posicion =DB::table('clientes')
        ->join('fiscal_positions', 'fiscal_positions.id', '=', 'clientes.fiscal_position_id')
        ->select('fiscal_positions.*')
        ->where('clientes.id','=',$request->cliente)
        ->get();

        foreach ($posicion as $pos){
            $posicion_id = $pos->id;
            $iva_t = $pos->iva_t;
            $isr_r = $pos->isr_r;
            $iva_r = $pos->iva_r;
            $imp_c = $pos->imp_c;
        }
        
        $proyecto = new Proyecto();

        $proyecto->nombre = $request->nombre;
        $proyecto->anio = $request->año;
        $proyecto->cliente_id = $request->cliente;
        $proyecto->subtotal = 0;
        $proyecto->iva_t = 0;
        $proyecto->isr_r = 0;
        $proyecto->iva_r = 0;
        $proyecto->imp_c = 0;
        $proyecto->importe = 0;
        $proyecto->saldo = 0;
        $proyecto->cxc = 0;
        $proyecto->estados_proyecto_id = 1;
        $proyecto->fecha_cotizacion = now();
        $proyecto->es_agrupado = $request->agrupado;
        $proyecto->autorizar = 0;
        $proyecto->fiscal_position_id = $posicion_id;
        $proyecto->observaciones = "";

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

        $inf = 'El proyecto se agregó con éxito...';
        session()->flash('Exito',$inf);
        return redirect()->route('proyectos.sucursales', ['idp' => $id,'idc' => $request->cliente])->with('message',$inf);
    }

    /**
     * Cambia el estatus de autorizado a un proyecto especifico
     * Establece los primeros movimientos a las lineas del proyecto
     * @param  \App\Models\proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function auth($id)
    {
        $user = Auth::user()->id;

        $acceso = 4;

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
            $proyecto = DB::table('proyectos')
                ->where('id','=',$id)
                ->first();

            if($proyecto->autorizar == 0){
                $lineas = ProyectoLinea::where('costo','<',0)->get();

                if($lineas){
                    $inf = 'Proyecto con partidas no asignadas a algún presupeusto...';
                    session()->flash('Error',$inf);
                    return redirect()->route('proyectos')->with('error',$inf);
                }
                else{
                    
                    $proy = DB::table('proyectos')
                        ->where('id','=',$id)
                        ->update([
                        'estados_proyecto_id' => 2,
                        'fecha_autorizacion' => now(),
                        ]);

                    $posicion =DB::table('proyectos')
                    ->join('clientes', 'clientes.id', '=', 'proyectos.cliente_id')
                    ->join('fiscal_positions', 'fiscal_positions.id', '=', 'clientes.fiscal_position_id')
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
                    $subtotal_cliente = 0;
                    $iva_t_cliente = 0;
                    $isr_r_cliente = 0;
                    $iva_r_cliente = 0;
                    $imp_c_cliente = 0;
                    $total_proveedor = 0;
                    $subtotal_proveedor = 0;
                    $iva_t_proveedor = 0;
                    $isr_r_proveedor = 0;
                    $iva_r_proveedor = 0;
                    $imp_c_proveedor = 0;

                    foreach($lineas as $linea){
                        
                        $movimiento = MovimientosPagoCliente::where('terminos_pago_cliente_id','=',$linea->terminos)
                            ->where('secuencia','=',1)
                            ->first();

                        if($movimiento == null){
                            $inf = 1;
                            session()->flash('Error','No existen acciones que agregar: '.$linea->sucursal." . ".$linea->producto);
                        }
                        else{
                            $posicionp =DB::table('presupuestos')
                            ->join('fiscal_positions', 'fiscal_positions.id', '=', 'presupuestos.fiscal_position_id')
                            ->select('fiscal_positions.*')
                            ->where('presupuestos.id','=',$linea->presupuesto_id)
                            ->get();

                            foreach ($posicionp as $posp){
                                $posicionp_id = $posp->id;
                                $iva_tp = $posp->iva_t;
                                $isr_rp = $posp->isr_r;
                                $iva_rp = $posp->iva_r;
                                $imp_cp = $posp->imp_c;
                            }

                            $importe_cliente = 0;
                            $subtotal_v = 0;
                            $saldo_cliente = $linea->saldocliente;
                            $importe_proveedor = 0;
                            $subtotal_c = 0;
                            $saldo_proveedor = $linea->saldoproveedor;

                            $subtotal_v = $linea->subtotal_v * ($movimiento->valor_cliente / 100);
                            $iva_t_v = $subtotal_v * ($iva_t / 100);
                            $isr_r_v = $subtotal_v * ($isr_r / 100);
                            $iva_r_v = $subtotal_v * ($iva_r / 100);
                            $imp_c_v = $subtotal_v * ($imp_c / 100);
                            $importe_cliente = $subtotal_v  + $iva_t_v - $isr_r_v - $iva_r_v -$imp_c_v;
                            $saldo_cliente = $saldo_cliente - $importe_cliente;
                            /** revisar los impuestos del proveedor en el proyecto */
                            $subtotal_c = $linea->subtotal_c * ($movimiento->valor_proveedor / 100);
                            $iva_t_c = $subtotal_c * ($iva_tp / 100);
                            $isr_r_c = $subtotal_c * ($isr_rp / 100);
                            $iva_r_c = $subtotal_c * ($iva_rp / 100);
                            $imp_c_c = $subtotal_c * ($imp_cp / 100);
                            $importe_proveedor = $subtotal_c  + $iva_t_c - $isr_r_c - $iva_r_c -$imp_c_c;
                            $saldo_proveedor = $saldo_proveedor - $importe_proveedor;

                            $total_cliente = $total_cliente + $importe_cliente;
                            $subtotal_cliente = $subtotal_cliente + $subtotal_v;
                            $iva_t_cliente = $iva_t_cliente + $iva_t_v;
                            $isr_r_cliente = $isr_r_cliente + $isr_r_v;
                            $iva_r_cliente = $iva_r_cliente + $iva_r_v;
                            $imp_c_cliente = $imp_c_cliente + $imp_c_v;

                            $total_proveedor = $total_proveedor + $importe_proveedor;
                            $subtotal_proveedor = $subtotal_proveedor + $subtotal_c;
                            $iva_t_proveedor = $iva_t_proveedor + $iva_t_c;
                            $isr_r_proveedor = $isr_r_proveedor + $isr_r_c;
                            $iva_r_proveedor = $iva_r_proveedor + $iva_r_c;
                            $imp_c_proveedor = $imp_c_proveedor + $imp_c_c;

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
                            $mov->subtotal_cliente = $subtotal_cliente;
                            $mov->saldo_cliente = $saldo_cliente;
                            $mov->importe_proveedor = $importe_proveedor;
                            $mov->subtotal_proveedor = $subtotal_proveedor;
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
                    
                    $inf = 'La autorización se realizó con éxito...';
                    session()->flash('Exito',$inf);
                    return redirect()->route('proyectos')->with('message',$inf);
                }
            }
            else{
                $inf = 'Proyecto previamente autorizado...';
                session()->flash('Error',$inf);
                return redirect()->route('proyectos')->with('error',$inf);
            }   
        }
        else{
            $inf = 'No cuentas con el permiso de acceso';
            return redirect()->route('proyectos')->with('error',$inf);
        }
        
    }

    /**
     * Presenta los productos involucrados en el proyectos.
     *  Permite cambiar los terminos de pago de los productos del proyecto
     * @param  \App\Models\proyecto  $proyecto
     */
    public function terminos($id)
    {
        $user = Auth::user()->id;

        $acceso = 4;

        $permisos = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->select('users.name','roles.name as role','roles.id as role_id','permissions.name as permission','permissions.id as permission_id')
            ->where('users.id','=', $user)
            ->get();
        
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
            $proyecto = DB::table('proyectos')
                ->where('id','=',$id)
                ->first();

            $cliente = Cliente::where('id','=',$proyecto->cliente_id)->first();

            if($proyecto->autorizar == 0){
                
                $productos =DB::table('proyecto_lineas')
                    ->leftJoin('proyectos', 'proyectos.id', '=', 'proyecto_lineas.proyecto_id')
                    ->leftjoin('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
                    ->leftJoin('terminos_pago_clientes', 'proyecto_lineas.terminos_pago_cliente_id', '=', 'terminos_pago_clientes.id')
                    ->leftJoin('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
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
                $inf = 'Proyecto previamente autorizado...';
                session()->flash('Error',$inf);
                return redirect()->route('proyectos')->with('error',$inf);
            }   
        }
        else{
            $inf = 'No cuentas con el permiso de acceso';
            return redirect()->route('proyectos')->with('error',$inf);
        }
    }

    /**
     * Actualza los terminos de pago de los productos del proyecto
     *  
     * @param  \App\Models\proyecto  $proyecto
     */
    public function termupdate($id,$idp,$term)
    {

        $user = Auth::user()->id;

        $acceso = 4;

        $permisos = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->select('users.name','roles.name as role','roles.id as role_id','permissions.name as permission','permissions.id as permission_id')
            ->where('users.id','=', $user)
            ->get();
        
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
                
                $inf = 'La actualización se realizó con éxito...';
                session()->flash('Exito', $inf);
                return redirect()->route('terminos.proyectos',['id' => $id])->with('message',$inf);

            }
            else{
                $inf = 'Actualización no realizada...';
                session()->flash('Error',$inf);
                return redirect()->route('terminos.proyectos',['id' => $id])->with('error',$inf);
            }   
        }
        else{
            $inf = 'No cuentas con el permiso de acceso';
            return redirect()->route('proyectos')->with('error',$inf);
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
        $user = Auth::user()->id;

        $acceso = 2;

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
            ->where('permissions.id','=', 4)
            ->first();
        
        $permisom = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->select('users.name','roles.name as role','roles.id as role_id','permissions.name as permission','permissions.id as permission_id')
            ->where('users.id','=', $user)
            ->where('permissions.id','=', 1)
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
            $proyecto =DB::table('proyectos')
                ->join('clientes', 'clientes.id', '=', 'proyectos.cliente_id')
                ->leftjoin('estados_proyectos', 'estados_proyectos.id', '=', 'proyectos.estados_proyecto_id')
                ->leftjoin('fiscal_positions', 'fiscal_positions.id', '=', 'proyectos.fiscal_position_id')
                ->select('proyectos.*','clientes.nombre as cliente','clientes.id as cliente_id','estados_proyectos.nombre as estado',
                'estados_proyectos.id as estados_proyecto_id','fiscal_positions.id as posicion_id','fiscal_positions.nombre as posicion')
                ->where('proyectos.id', "=",$id)
                ->first();

            return view('proyecto.edit', ['proyecto' => $proyecto, 'user' => $user,'permisos' => $permisos,'permisoa' => $permisoa,'permisom' => $permisom]);
        }
        else{
            $inf = 'No cuentas con el permiso de acceso';
            return redirect()->route('proyectos')->with('error',$inf);
        }
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
        $proyecto = DB::table('proyectos')
            ->where('proyectos.id','=',$id)
            ->update([
            'anio'=> $request->año,
            'es_agrupado'=> $request->agrupado,
            'observaciones'=> $request->obs,
        ]);

        $inf = 1;
        session()->flash('Exito','El proyecto se modificó con éxito...');
        return redirect()->route('proyectos')->with('info',$inf);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */

    public function cancelar($id)
    {
        $user = Auth::user()->id;

        $acceso = 14;

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
            $proyecto = Proyecto::find($id);

            $presupuestos = DB::table('proyecto_lineas')
            ->join('presupuestos','presupuestos.id','=','proyecto_lineas.presupuesto_id')
            ->select('presupuestos.id','presupuestos.nombre','presupuestos.estados_presupuesto_id as estado','presupuestos.autorizar')
            ->where('proyecto_lineas.proyecto_id','=',$id)
            ->groupBy('presupuestos.id','presupuestos.nombre','presupuestos.estados_presupuesto_id','presupuestos.autorizar')
            ->get();

            $banpre = 0;
            $nomPres = '';
            if($presupuestos){
                foreach($presupuestos as $pre){
                    if($pre->autorizar == 1){
                        $banpre += 1;
                        if($banpre > 0){
                            $nomPres = $nomPres.',';
                        }
                        $nomPres = $nomPres.$pre->nombre;
                    }
                }
            }

            if($proyecto->autorizar == 0){
                if($banpre == 0){
                    $data = [
                        'costo' => 0,
                        'subtotal_v' => 0,
                        'iva_t_v' => 0,
                        'isr_r_v' => 0,
                        'iva_r_v' => 0,
                        'imp_c_v' => 0,
                        'total_v' => 0,
                        'saldocliente' => 0,
                    ];
                    
                    $linea = DB::table('proyecto_lineas')
                        ->where('proyecto_id','=',$id)
                        ->update($data);
                    
                    $data = [
                        'importe' => 0,
                        'saldo' => 0,
                        'autorizar' => 0,
                        'estados_proyecto_id' => 6,
                        'fecha_cancelacion' => now(),
                    ];
                    
                    $proyecto = DB::table('proyectos')
                        ->where('id','=',$id)
                        ->update($data);

                    foreach($presupuestos as $pre){
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
                            ->where('presupuesto_id','=',$pre->id)
                            ->update($data);
                        
                        $data = [
                            'importe' => 0,
                            'saldo' => 0,
                            'autorizar' => 0,
                            'estados_presupuesto_id' => 6,
                        ];
                        
                        $presupuesto = DB::table('presupuestos')
                            ->where('id','=',$pre->id)
                            ->update($data);
                    }
        
                    $inf = 1;
                    session()->flash('Exito','El proyecto fue cancelado con éxito...');
                    return redirect()->route('proyectos')->with('info',$inf);
                }
                else{
                    $inf = 'El proyecto tiene los siguientes presupuestos autorizados: '.$nomPres;
                    return redirect()->route('proyectos')->with('error',$inf);
                }
                
            }
            else{
                $inf = 'El proyecto esta autorizado, no es posible la acción solicitada';
                return redirect()->route('proyectos')->with('error',$inf);
            }   
        }
        else{
            $inf = 'No cuentas con el permiso de acceso';
            return redirect()->route('proyectos')->with('error',$inf);
        }
    }

    public function destroy($id)
    {
        $user = Auth::user()->id;

        $acceso = 14;

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
            $proyecto = Proyecto::find($id);

            $presupuestos = DB::table('proyecto_lineas')
            ->join('presupuestos','presupuestos.id','=','proyecto_lineas.presupuesto_id')
            ->select('presupuestos.id','presupuestos.nombre','presupuestos.estados_presupuesto_id as estado','presupuestos.autorizar')
            ->where('proyecto_lineas.proyecto_id','=',$id)
            ->groupBy('presupuestos.id','presupuestos.nombre','presupuestos.estados_presupuesto_id','presupuestos.autorizar')
            ->get();

            $banpre = 0;
            $nomPres = '';
            if($presupuestos){
                foreach($presupuestos as $pre){
                    if($pre->estado != 6){
                        $banpre += 1;
                        if($banpre > 0){
                            $nomPres = $nomPres.',';
                        }
                        $nomPres = $nomPres.$pre->nombre;
                    }
                }
            }

            if($proyecto->estados_proyecto_id == 6){
                if($banpre == 0){
                    $lineas = DB::table('proyectos')
                    ->where('id','=',$id)
                    ->delete();
        
                    $inf = 1;
                    session()->flash('Exito','El proyecto fue eliminado con éxito...');
                    return redirect()->route('proyectos')->with('info',$inf);
                }
                else{
                    $inf = 'El proyecto tiene los siguientes presupuestos no eliminados: '.$nomPres;
                    return redirect()->route('proyectos')->with('error',$inf);
                }
            }
            else{
                $inf = 'El proyecto debe cancelarse primero';
                return redirect()->route('proyectos')->with('error',$inf);
            }   
        }
        else{
            $inf = 'No cuentas con el permiso de acceso';
            return redirect()->route('proyectos')->with('error',$inf);
        }
    }
    
}
