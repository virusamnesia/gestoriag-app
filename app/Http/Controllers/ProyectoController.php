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
                ->select('proyectos.*','clientes.nombre as cliente','clientes.id as cliente_id','estados_proyectos.nombre as estado',
                'estados_proyectos.id as estados_proyecto_id')
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
            'anio' => 'required',
            'cliente_id' => 'required',
            'es_agrupado' => 'required',
        ]);
        
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
                        $importe_proveedor = $linea->costo * ($movimiento->valor_proveedor / 100);
                        $saldo_proveedor = $saldo_proveedor - $importe_proveedor;

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
                
                $inf = 'La autorización se realizó con éxito...';
                session()->flash('Exito',$inf);
                return redirect()->route('proyectos')->with('message',$inf);

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
