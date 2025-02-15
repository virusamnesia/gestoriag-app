<?php

namespace App\Http\Controllers;

use App\Models\CiudadContacto;
use App\Models\Cliente;
use App\Models\EstadoContacto;
use App\Models\MunicipioContacto;
use App\Models\PaisContacto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
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
            $clientes =Cliente::select('id', 'clave', 'nombre', 'rfc','email','telefono')
            ->orderBy('nombre')
            ->get();
            return view('cliente.index', ['clientes' => $clientes]);
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
        $municipios = DB::table('municipio_contactos')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'municipio_contactos.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'estado_contactos.pais_contacto_id')
            ->join('ciudad_contactos', 'municipio_contactos.id', '=', 'ciudad_contactos.municipio_contacto_id')
            ->select('municipio_contactos.id','municipio_contactos.nombre','ciudad_contactos.nombre as ciudad','estado_contactos.alias as estado','pais_contactos.alias as pais')
            ->orderBy('municipio_contactos.nombre')
            ->get();

        return view('cliente.create', ['municipios' => $municipios]);
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
        
        $cliente = new Cliente();

        $cliente->clave = $request->clave;
        $cliente->nombre = $request->nombre;
        $cliente->rfc = $request->rfc;
        $cliente->domicilio = $request->domicilio;
        $cliente->colonia = $request->colonia;
        $cliente->ciudad_contacto_id = $ciudad;
        $cliente->municipio_contacto_id = $request->$municipio;
        $cliente->estado_contacto_id = $estado;
        $cliente->pais_contacto_id = $pais;
        $cliente->cp = $request->cp;
        $cliente->telefono = $request->telefono;
        $cliente->email = $request->email;

        $cliente->save();
        $inf = 1;
        session()->flash('Exito','El cliente se agregó con éxito...');
        return redirect()->route('clientes')->with('info',$inf);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cliente  $cliente
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
        
            $cliente = DB::table('clientes')
            ->join('ciudad_contactos', 'ciudad_contactos.id', '=', 'clientes.ciudad_contacto_id')
            ->join('municipio_contactos', 'municipio_contactos.id', '=', 'clientes.municipio_contacto_id')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'clientes.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'clientes.pais_contacto_id')
            ->select('clientes.*')
            ->where('clientes.id','=',$id)
            ->get();

        return view('cliente.show', ['cliente' => $cliente,'municipios' => $municipios]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cliente  $cliente
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

        $cliente = DB::table('clientes')
            ->join('ciudad_contactos', 'ciudad_contactos.id', '=', 'clientes.ciudad_contacto_id')
            ->join('municipio_contactos', 'municipio_contactos.id', '=', 'clientes.municipio_contacto_id')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'clientes.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'clientes.pais_contacto_id')
            ->select('clientes.*')
            ->where('clientes.id','=',$id)
            ->get();

        return view('cliente.edit', ['cliente' => $cliente,'municipios' => $municipios]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $municipios = DB::table('municipio_contactos')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'municipio_contactos.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'estado_contactos.pais_contacto_id')
            ->join('ciudad_contactos', 'municipio_contactos.id', '=', 'ciudad_contactos.municipio_contacto_id')
            ->select('municipio_contactos.id','municipio_contactos.nombre','ciudad_contactos.id as ciudad','estado_contactos.id as estado','pais_contactos.id as pais')
            ->where('ciudad_contactos.id', $request->municipio)
            ->orderBy('municipio_contactos.nombre')
            ->get();
        
        foreach ($municipios as $data) {
            $municipio = $data->id;
            $estado = $data->estado;
            $pais = $data->pais;
            $ciudad = $data->ciudad;
        }
        
        $cliente = DB::table('clientes')
            ->where('clientes.id','=',$id)
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
        ]);

        $inf = 1;
        session()->flash('Exito','El cliente se modificó con éxito...');
        return redirect()->route('clientes')->with('info',$inf);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cliente $cliente)
    {
        //
    }

}
