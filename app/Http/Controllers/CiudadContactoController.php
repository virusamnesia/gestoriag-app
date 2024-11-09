<?php

namespace App\Http\Controllers;

use App\Models\CiudadContacto;
use App\Models\EstadoContacto;
use App\Models\MunicipioContacto;
use App\Models\PaisContacto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CiudadContactoController extends Controller
{
    public function index(){
        
        $estados = EstadoContacto::all();
        $paises = PaisContacto::all();
        $municipios = MunicipioContacto::all();

        $ciudades = DB::table('ciudad_contactos')
            ->join('municipio_contactos', 'municipio_contactos.id', '=', 'ciudad_contactos.municipio_contacto_id')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'municipio_contactos.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'estado_contactos.pais_contacto_id')
            ->select('ciudad_contactos.*', 'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais')
            ->get();
        return view('ciudad.index', ['municipios' => $municipios,'ciudades' => $ciudades,'estados' => $estados, 'paises' => $paises]);

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
    public function store(Request $request)
    {
        $ciudad = new CiudadContacto();

        $ciudad->nombre = $request->nombre;
        $ciudad->municipio_contacto_id = $request->municipio;

        $ciudad->save();
        return redirect('/ciudades');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CiudadContacto  $CiudadContacto
     * @return \Illuminate\Http\Response
     */
    public function show(CiudadContacto $CiudadContacto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CiudadContacto  $CiudadContacto
     * @return \Illuminate\Http\Response
     */
    public function edit(CiudadContacto $CiudadContacto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CiudadContacto  $CiudadContacto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CiudadContacto $CiudadContacto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CiudadContacto  $CiudadContacto
     * @return \Illuminate\Http\Response
     */
    public function destroy(CiudadContacto $CiudadContacto)
    {
        //
    }
    
}
