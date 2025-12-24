<?php

namespace App\Http\Controllers;

use App\Models\Banco;
use App\Models\EstadoContacto;
use App\Models\FiscalPosition;
use App\Models\MunicipioContacto;
use App\Models\PaisContacto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ProveedorController extends Controller
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
            $proveedores = DB::table('proveedors')
            ->join('ciudad_contactos', 'ciudad_contactos.id', '=', 'proveedors.ciudad_contacto_id')
            ->join('municipio_contactos', 'municipio_contactos.id', '=', 'proveedors.municipio_contacto_id')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'proveedors.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'proveedors.pais_contacto_id')
            ->leftjoin('fiscal_positions', 'fiscal_positions.id', '=', 'proveedors.fiscal_position_id')
            ->select('proveedors.*','fiscal_positions.id as posicion_id','fiscal_positions.nombre as posicion')
            ->get();

            return view('Proveedor.index', ['proveedores' => $proveedores]);
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
        $bancos = Banco::all();
        
        $municipios = DB::table('municipio_contactos')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'municipio_contactos.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'estado_contactos.pais_contacto_id')
            ->join('ciudad_contactos', 'municipio_contactos.id', '=', 'ciudad_contactos.municipio_contacto_id')
            ->select('municipio_contactos.id','municipio_contactos.nombre','ciudad_contactos.nombre as ciudad','estado_contactos.alias as estado','pais_contactos.alias as pais')
            ->orderBy('municipio_contactos.nombre')
            ->get();

        $posiciones = FiscalPosition::all();

        return view('Proveedor.create', ['municipios' => $municipios, 'bancos' => $bancos,'posiciones' => $posiciones]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $municipios = DB::table('municipio_contactos')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'municipio_contactos.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'estado_contactos.pais_contacto_id')
            ->join('ciudad_contactos', 'municipio_contactos.id', '=', 'ciudad_contactos.municipio_contacto_id')
            ->select('municipio_contactos.id','municipio_contactos.nombre','ciudad_contactos.id as ciudad','estado_contactos.id as estado','pais_contactos.id as pais')
            ->where('municipio_contactos.id', $request->municipio)
            ->orderBy('municipio_contactos.nombre')
            ->get();
    
        foreach ($municipios as $data) {
            $municipio = $data->id;
            $estado = $data->estado;
            $pais = $data->pais;
            $ciudad = $data->ciudad;
        }

        $banco = Banco::select('id', 'nombre')
        ->where('id', $request->banco)
        ->first();
        
        $proveedor = new Proveedor();

        $proveedor->clave = $request->clave;
        $proveedor->nombre = $request->nombre;
        $proveedor->rfc = $request->rfc;
        $proveedor->domicilio = $request->domicilio;
        $proveedor->colonia = $request->colonia;
        $proveedor->ciudad_contacto_id = $ciudad;
        $proveedor->municipio_contacto_id = $request->municipio;
        $proveedor->estado_contacto_id = $estado;
        $proveedor->pais_contacto_id = $pais;
        $proveedor->cp = $request->cp;
        $proveedor->telefono = $request->telefono;
        $proveedor->email = $request->email;
        $proveedor->banco_id = $banco->id;
        $proveedor->cuenta = $request->cuenta;
        $proveedor->fiscal_position_id = $request->posicion;

        $proveedor->save();

        $inf = 1;
        session()->flash('Exito','El proveedor se agregó con éxito...');
        return redirect()->route('proveedores')->with('info',$inf);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\proveedor  $proveedor
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $municipios = DB::table('municipio_contactos')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'municipio_contactos.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'estado_contactos.pais_contacto_id')
            ->join('ciudad_contactos', 'municipio_contactos.id', '=', 'ciudad_contactos.municipio_contacto_id')
            ->select('municipio_contactos.id','municipio_contactos.nombre','ciudad_contactos.nombre as ciudad','estado_contactos.alias as estado','pais_contactos.alias as pais')
            ->orderBy('municipio_contactos.nombre')
            ->get();

        $bancos = Banco::all();

        $posiciones = FiscalPosition::all();
        
        $proveedor = DB::table('proveedors')
            ->join('ciudad_contactos', 'ciudad_contactos.id', '=', 'proveedors.ciudad_contacto_id')
            ->join('municipio_contactos', 'municipio_contactos.id', '=', 'proveedors.municipio_contacto_id')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'proveedors.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'proveedors.pais_contacto_id')
            ->select('proveedors.*')
            ->where('proveedors.id','=',$id)
            ->get();

        return view('Proveedor.show', ['proveedor' => $proveedor,'municipios' => $municipios, 'bancos' => $bancos,'posiciones' => $posiciones]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\proveedor  $proveedor
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $municipios = DB::table('municipio_contactos')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'municipio_contactos.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'estado_contactos.pais_contacto_id')
            ->join('ciudad_contactos', 'municipio_contactos.id', '=', 'ciudad_contactos.municipio_contacto_id')
            ->select('municipio_contactos.id','municipio_contactos.nombre','ciudad_contactos.nombre as ciudad','estado_contactos.alias as estado','pais_contactos.alias as pais')
            ->orderBy('municipio_contactos.nombre')
            ->get();
        
        $bancos = Banco::all();
        
        $proveedor = DB::table('proveedors')
            ->join('ciudad_contactos', 'ciudad_contactos.id', '=', 'proveedors.ciudad_contacto_id')
            ->join('municipio_contactos', 'municipio_contactos.id', '=', 'proveedors.municipio_contacto_id')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'proveedors.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'proveedors.pais_contacto_id')
            ->select('proveedors.*')
            ->where('proveedors.id','=',$id)
            ->get();

        $posiciones = FiscalPosition::all();

        return view('Proveedor.edit', ['proveedor' => $proveedor,'municipios' => $municipios, 'bancos' => $bancos,'posiciones' => $posiciones]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\proveedor  $proveedor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $municipios = DB::table('municipio_contactos')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'municipio_contactos.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'estado_contactos.pais_contacto_id')
            ->join('ciudad_contactos', 'municipio_contactos.id', '=', 'ciudad_contactos.municipio_contacto_id')
            ->select('municipio_contactos.id','municipio_contactos.nombre','ciudad_contactos.id as ciudad','estado_contactos.id as estado','pais_contactos.id as pais')
            ->where('municipio_contactos.id', $request->municipio)
            ->orderBy('municipio_contactos.nombre')
            ->get();
    
        foreach ($municipios as $data) {
            $municipio = $data->id;
            $estado = $data->estado;
            $pais = $data->pais;
            $ciudad = $data->ciudad;
        }

        $banco = Banco::select('id', 'nombre')
        ->where('id', $request->banco)
        ->first();
        
        $proveedor = DB::table('proveedors')
            ->where('proveedors.id','=',$id)
            ->update([
            'clave'=> $request->clave,
            'nombre'=> $request->nombre,
            'rfc'=> $request->rfc,
            'domicilio'=> $request->domicilio,
            'colonia'=> $request->colonia,
            'ciudad_contacto_id'=> $ciudad,
            'municipio_contacto_id'=> $request->municipio,
            'estado_contacto_id'=> $estado,
            'pais_contacto_id'=> $pais,
            'cp'=> $request->cp,
            'telefono'=> $request->telefono,
            'email'=> $request->email,
            'banco_id' => $banco->id,
            'cuenta' => $request->cuenta,
            'fiscal_position_id' => $request->posicion,
        ]
        );

        $inf = 1;
        session()->flash('Exito','El proveedor se modificó con éxito...');
        return redirect()->route('proveedores')->with('info',$inf);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\proveedor  $proveedor
     * @return \Illuminate\Http\Response
     */
    public function destroy(proveedor $proveedor)
    {
        //
    }

    public function municipios($id)
    {
        $municipios = DB::table('municipio_contactos')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'municipio_contactos.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'estado_contactos.pais_contacto_id')
            ->join('ciudad_contactos', 'municipio_contactos.id', '=', 'ciudad_contactos.municipio_contacto_id')
            ->select('municipio_contactos.id','municipio_contactos.nombre','ciudad_contactos.nombre as ciudad','estado_contactos.alias as estado','pais_contactos.alias as pais')
            ->orderBy('municipio_contactos.nombre')
            ->get();

        $proveedor = DB::table('proveedors')
            ->select('proveedors.*')
            ->where('proveedors.id','=',$id)
            ->first();

        return view('Proveedor.municipios', ['proveedor' => $proveedor,'municipios' => $municipios]);
    }

    public function store_municipios(Request $request,$id)
    {
        
        $municipios = DB::table('municipio_contactos')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'municipio_contactos.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'estado_contactos.pais_contacto_id')
            ->join('ciudad_contactos', 'municipio_contactos.id', '=', 'ciudad_contactos.municipio_contacto_id')
            ->select('municipio_contactos.id','municipio_contactos.nombre','ciudad_contactos.id as ciudad','estado_contactos.id as estado','pais_contactos.id as pais')
            ->where('municipio_contactos.id', $request->municipio)
            ->orderBy('municipio_contactos.nombre')
            ->get();
    
        foreach ($municipios as $data) {
            $municipio = $data->id;
            $estado = $data->estado;
            $pais = $data->pais;
            $ciudad = $data->ciudad;
        }

        $banco = Banco::select('id', 'nombre')
        ->where('id', $request->banco)
        ->first();
        
        $proveedor = new Proveedor();

        $proveedor->clave = $request->clave;
        $proveedor->nombre = $request->nombre;
        $proveedor->rfc = $request->rfc;
        $proveedor->domicilio = $request->domicilio;
        $proveedor->colonia = $request->colonia;
        $proveedor->ciudad_contacto_id = $ciudad;
        $proveedor->municipio_contacto_id = $request->municipio;
        $proveedor->estado_contacto_id = $estado;
        $proveedor->pais_contacto_id = $pais;
        $proveedor->cp = $request->cp;
        $proveedor->telefono = $request->telefono;
        $proveedor->email = $request->email;
        $proveedor->banco_id = $banco->id;
        $proveedor->cuenta = $request->cuenta;

        $proveedor->save();

        $inf = 1;
        session()->flash('Exito','El proveedor se agregó con éxito...');
        return redirect()->route('proveedores')->with('info',$inf);
    }

    /**
     * Obtener la posición fiscal de un proveedor vía AJAX.
     */
    public function getFiscalPosition($id)
    {
        // Buscamos el proveedor. Asumimos que la columna foránea se llama fiscal_position_id
        // Si tu columna tiene otro nombre (ej. position_id), cámbialo aquí.
        $proveedor = Proveedor::find($id);

        if ($proveedor) {
            return response()->json([
                'success' => true,
                'fiscal_position_id' => $proveedor->fiscal_position_id
            ]);
        }

        return response()->json(['success' => false], 404);
    }
    
}
