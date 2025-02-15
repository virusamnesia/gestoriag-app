<?php

namespace App\Http\Controllers;

use App\Models\MovimientosPagoCliente;
use App\Models\Producto;
use App\Models\TerminosPagoCliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TerminosPagoClienteController extends Controller
{
    public function index(){
        $user = Auth::user()->id;

        $acceso = 12;

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
            $terminos = DB::table('terminos_pago_clientes')
            ->select('terminos_pago_clientes.*')
                ->get();
        
            return view('terminoscliente.index', ['terminos' => $terminos]);
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
    public function create($idp)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $termino = new TerminosPagoCliente();

        $termino->nombre = $request->nombre;
        $termino->es_agrupado = $request->agrupado;

        $termino->save();
        $inf = 1;

        $data = TerminosPagoCliente::latest('id')->first();
        $id = $data->id;

        session()->flash('Exito','Agrega movimientos al termino de pago...');
        return redirect()->route('termclie.movimientos',['id' => $id])->with('info',$inf);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function show($idc, $idl)
    {
        $termino = terminosPrecio::where('id',$idl)
            ->first();

        $cliente = Cliente::where('id',$idc)
            ->first();

        $productos = DB::table('terminos_precio_lineas')
            ->leftJoin('terminos_pago_productos', 'terminos_precio_lineas.terminos_precio_id', '=', 'terminos_pago_productos.id')
            ->leftJoin('productos', 'productos.id', '=', 'terminos_pago_productos.producto_id')
            ->leftJoin('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
            ->lefjoin('municipio_contactos', 'municipio_contactos.id', '=', 'terminos_pago_productos.municipio_contacto_id')
            ->lefjoin('estado_contactos', 'estado_contactos.id', '=', 'municipio_contactos.estado_contacto_id')
            ->select('terminos_precio_lineas.*','productos.nombre as producto','municipio_contactos.nombre as municipio', 
            'estado_contactos.nombre as estado', 'tipos_productos.nombre as tipo')
            ->where('terminos_pago_productos.id',$idl)
            ->orderBy('productos.nombre')
            ->get();
        
        return view('terminosprecio.show', ['termino' => $termino, 'idc' => $idc, 'idl' => $idl, 'productos' => $productos, 'cliente' => $cliente]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
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
