<?php

namespace App\Http\Controllers;

use App\Models\CiudadContacto;
use App\Models\EstadoContacto;
use App\Models\MunicipioContacto;
use App\Models\PaisContacto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MunicipioContactoController extends Controller
{
    public function index($idc,$ids){
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
            $estados = EstadoContacto::all();
            $paises = PaisContacto::all();

            $municipios = DB::table('municipio_contactos')
                ->join('estado_contactos', 'estado_contactos.id', '=', 'municipio_contactos.estado_contacto_id')
                ->join('pais_contactos', 'pais_contactos.id', '=', 'estado_contactos.pais_contacto_id')
                ->select('municipio_contactos.*', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais')
                ->get();
            return view('municipio.index', ['municipios' => $municipios,'estados' => $estados, 'paises' => $paises, 'idc' => $idc, 'ids' => $ids]);
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $idc, $ids)
    {
        $MunicipioContacto = new MunicipioContacto();

        $MunicipioContacto->nombre = $request->nombre;
        $MunicipioContacto->estado_contacto_id = $request->estado;

        $MunicipioContacto->save();

        $municipio = MunicipioContacto::select('id', 'nombre', 'estado_contacto_id')
            ->where('nombre', $request->nombre)
            ->first();

        $ciudad = new CiudadContacto();

        $ciudad->nombre = $request->nombre;
        $ciudad->municipio_contacto_id = $municipio->id;

        $ciudad->save();

        $inf = 1;
        session()->flash('Exito','El municipio se agregó con éxito...');
        return redirect()->route('municipios', ['idc' => $idc,'ids' => $ids])->with('info',$inf);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MunicipioContacto  $MunicipioContacto
     * @return \Illuminate\Http\Response
     */
    public function show(MunicipioContacto $MunicipioContacto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MunicipioContacto  $MunicipioContacto
     * @return \Illuminate\Http\Response
     */
    public function edit(MunicipioContacto $MunicipioContacto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MunicipioContacto  $MunicipioContacto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MunicipioContacto $MunicipioContacto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MunicipioContacto  $MunicipioContacto
     * @return \Illuminate\Http\Response
     */
    public function destroy(MunicipioContacto $MunicipioContacto)
    {
        //
    }
    
}
