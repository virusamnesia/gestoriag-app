<?php

namespace App\Http\Controllers;

use App\Models\CiudadContacto;
use App\Models\EstadoContacto;
use App\Models\MunicipioContacto;
use App\Models\PaisContacto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MunicipioContactoController extends Controller
{
    public function index($idc,$ids){
        
        $estados = EstadoContacto::all();
        $paises = PaisContacto::all();

        $municipios = DB::table('municipio_contactos')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'municipio_contactos.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'estado_contactos.pais_contacto_id')
            ->select('municipio_contactos.*', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais')
            ->get();
        return view('municipio.index', ['municipios' => $municipios,'estados' => $estados, 'paises' => $paises, 'idc' => $idc, 'ids' => $ids]);

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
