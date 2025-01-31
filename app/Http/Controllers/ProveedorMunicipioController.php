<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\ProveedorMunicipio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProveedorMunicipioController extends Controller
{
    public function index($id){
        
        $municipios_all = DB::table('municipio_contactos')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'municipio_contactos.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'estado_contactos.pais_contacto_id')
            ->join('ciudad_contactos', 'municipio_contactos.id', '=', 'ciudad_contactos.municipio_contacto_id')
            ->select('municipio_contactos.id','municipio_contactos.nombre','ciudad_contactos.nombre as ciudad','estado_contactos.alias as estado','pais_contactos.alias as pais')
            ->orderBy('municipio_contactos.nombre')
            ->get();
        
        $proveedor = Proveedor::where('id',$id)->first();

        $municipios = DB::table('proveedor_municipios')
            ->join('municipio_contactos', 'municipio_contactos.id', '=', 'proveedor_municipios.municipio_contacto_id')
            ->join('ciudad_contactos', 'municipio_contactos.id', '=', 'ciudad_contatos.municipio_contacto_id')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'municipio_contactos.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'estado_contactos.pais_contacto_id')
            ->join('proveedors', 'proveedors.id', '=', 'proveedor_municipios.proveedor_id')
            ->select('proveedor_municipios.*', 'municipio_contactos.nombre as municipio','estado_contactos.alias as estado')
            ->where('proveedors.id','=',$id)
            ->get();

        return view('Proveedor.municipios.index', ['municipios_all' => $municipios_all,'municipios' => $municipios,'proveedor' => $proveedor]);

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
    public function store(Request $request, $id)
    {
        $inf = 1;
        
        $municipios = ProveedorMunicipio::where('proveedor_id',$id)
            ->where('municipio_contacto_id', $request->municipio)
            ->first();
        
        if ($municipios){
            session()->flash('Error','Municipio ya registrado para el proveedor...');
            return redirect()->route('munproveedor',['id' => $id])->with('info',$inf);
        } 
            
        $municipio = new ProveedorMunicipio();

        $municipio->municipio_contacto_id = $request->municipio;
        $municipio->proveedor_id = $id;

        $municipio->save();

        session()->flash('Exito','Municipio agregado al proveedor...');
        return redirect()->route('munproveedor',['id' => $id])->with('info',$inf);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProveedorMunicipio  $ProveedorMunicipio
     * @return \Illuminate\Http\Response
     */
    public function show(ProveedorMunicipio $ProveedorMunicipio)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProveedorMunicipio  $ProveedorMunicipio
     * @return \Illuminate\Http\Response
     */
    public function edit(ProveedorMunicipio $ProveedorMunicipio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProveedorMunicipio  $ProveedorMunicipio
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProveedorMunicipio $ProveedorMunicipio)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProveedorMunicipio  $ProveedorMunicipio
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProveedorMunicipio $ProveedorMunicipio)
    {
        //
    }
}
