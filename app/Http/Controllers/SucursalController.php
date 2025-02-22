<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SucursalController extends Controller
{
    public function indexcliente($id){
        
        $sucursales = DB::table('sucursals')
            ->join('clientes', 'clientes.id', '=', 'sucursals.cliente_id')
            ->join('ciudad_contactos', 'ciudad_contactos.id', '=', 'sucursals.ciudad_contacto_id')
            ->join('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
            ->select('sucursals.*','sucursals.id','sucursals.nombre', 'ciudad_contactos.nombre as ciudad','municipio_contactos.nombre as municipio','estado_contactos.alias as estado','pais_contactos.alias as pais')
            ->where('clientes.id','=',$id)
            ->get();

            $cliente =Cliente::select('id', 'clave', 'nombre', 'rfc','email','telefono')
            ->find($id);

            return view('cliente.sucursal.index', ['sucursales' => $sucursales, 'cliente' => $cliente]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $municipios = DB::table('municipio_contactos')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'municipio_contactos.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'estado_contactos.pais_contacto_id')
            ->join('ciudad_contactos', 'municipio_contactos.id', '=', 'ciudad_contactos.municipio_contacto_id')
            ->select('municipio_contactos.id','municipio_contactos.nombre','ciudad_contactos.nombre as ciudad','estado_contactos.alias as estado','pais_contactos.alias as pais')
            ->get();

        $cliente =Cliente::select('id', 'clave', 'nombre', 'rfc','email','telefono')
            ->find($id);

        return view('cliente.sucursal.create', ['municipios' => $municipios, 'cliente' => $cliente]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $municipios = DB::table('municipio_contactos')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'municipio_contactos.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'estado_contactos.pais_contacto_id')
            ->join('ciudad_contactos', 'municipio_contactos.id', '=', 'ciudad_contactos.municipio_contacto_id')
            ->select('municipio_contactos.id','municipio_contactos.nombre','ciudad_contactos.id as ciudad','estado_contactos.id as estado','pais_contactos.id as pais')
            ->where('municipio_contactos.id', $request->municipio)
            ->get();
        
        foreach ($municipios as $data) {
            $municipio = $data->id;
            $estado = $data->estado;
            $pais = $data->pais;
            $ciudad = $data->ciudad;
        }
        
        $sucursal = new Sucursal();

        $sucursal->cliente_id = $id;
        $sucursal->marca = $request->marca;
        $sucursal->id_interno = $request->idinterno;
        $sucursal->nombre = $request->nombre;
        $sucursal->domicilio = $request->domicilio;
        $sucursal->colonia = $request->colonia;
        $sucursal->ciudad_contacto_id = $ciudad;
        $sucursal->municipio_contacto_id = $request->municipio;
        $sucursal->estado_contacto_id = $estado;
        $sucursal->pais_contacto_id = $pais;
        $sucursal->cp = $request->cp;
        $sucursal->telefono = $request->telefono;
        $sucursal->email = $request->email;
        $sucursal->superficie = $request->superficie;

        $sucursal->save();

        $inf = 1;
        session()->flash('Exito','La Sucursal se agregó con éxito...');
        return redirect()->route('sucursalesclientes', ['id' => $id])->with('info',$inf);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sucursal  $sucursal
     * @return \Illuminate\Http\Response
     */
    public function show($idc,$ids)
    {
        $municipios = DB::table('municipio_contactos')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'municipio_contactos.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'estado_contactos.pais_contacto_id')
            ->join('ciudad_contactos', 'municipio_contactos.id', '=', 'ciudad_contactos.municipio_contacto_id')
            ->select('municipio_contactos.id','municipio_contactos.nombre','ciudad_contactos.nombre as ciudad','estado_contactos.alias as estado','pais_contactos.alias as pais')
            ->get();
        $sucursal = DB::table('sucursals')
            ->join('ciudad_contactos', 'ciudad_contactos.id', '=', 'sucursals.ciudad_contacto_id')
            ->join('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
            ->select('sucursals.*')
            ->where('sucursals.id','=',$ids)
            ->get();

        $cliente =Cliente::select('id', 'clave', 'nombre', 'rfc','email','telefono')
            ->find($idc);

        return view('cliente.sucursal.show', ['sucursal' => $sucursal,'cliente' => $cliente,'municipios' => $municipios]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sucursal  $sucursal
     * @return \Illuminate\Http\Response
     */
    public function edit($idc,$ids)
    {
        $municipios = DB::table('municipio_contactos')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'municipio_contactos.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'estado_contactos.pais_contacto_id')
            ->join('ciudad_contactos', 'municipio_contactos.id', '=', 'ciudad_contactos.municipio_contacto_id')
            ->select('municipio_contactos.id','municipio_contactos.nombre','ciudad_contactos.nombre as ciudad','estado_contactos.alias as estado','pais_contactos.alias as pais')
            ->get();
        
        $sucursal = DB::table('sucursals')
            ->join('ciudad_contactos', 'ciudad_contactos.id', '=', 'sucursals.ciudad_contacto_id')
            ->join('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
            ->select('sucursals.*')
            ->where('sucursals.id','=',$ids)
            ->get();

        $cliente =Cliente::select('id', 'clave', 'nombre', 'rfc','email','telefono')
            ->find($idc);

        return view('cliente.sucursal.edit', ['sucursal' => $sucursal,'cliente' => $cliente,'municipios' => $municipios]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sucursal  $sucursal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idc,$ids)
    {
        $municipios = DB::table('municipio_contactos')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'municipio_contactos.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'estado_contactos.pais_contacto_id')
            ->join('ciudad_contactos', 'municipio_contactos.id', '=', 'ciudad_contactos.municipio_contacto_id')
            ->select('municipio_contactos.id','municipio_contactos.nombre','ciudad_contactos.id as ciudad','estado_contactos.id as estado','pais_contactos.id as pais')
            ->where('ciudad_contactos.id', $request->municipio)
            ->get();
        
        foreach ($municipios as $data) {
            $municipio = $data->id;
            $estado = $data->estado;
            $pais = $data->pais;
            $ciudad = $data->ciudad;
        }
        
        $sucursal = DB::table('sucursals')
            ->where('sucursals.id','=',$ids)
            ->update([
            'cliente_id'=> $idc,
            'marca' => $request->marca,
            'id_interno' => $request->idinterno,
            'nombre'=> $request->nombre,
            'domicilio'=> $request->domicilio,
            'colonia'=> $request->colonia,
            'ciudad_contacto_id'=> $ciudad,
            'municipio_contacto_id'=> $request->municipio,
            'estado_contacto_id'=> $estado,
            'pais_contacto_id'=> $pais,
            'cp'=> $request->cp,
            'telefono'=> $request->telefono,
            'email'=> $request->email,
            'superficie'=> $request->superficie,
        ]
        );

        $inf = 1;
        session()->flash('Exito','La Sucursal se modificó con éxito...');
        return redirect()->route('sucursalesclientes', ['id' => $idc])->with('info',$inf);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sucursal  $sucursal
     * @return \Illuminate\Http\Response
     */
    public function destroy($idc,$ids)
    {
        $lineas = DB::table('proyecto_lineas')
        ->where('sucursal_id', '=',$ids)
        ->first();

        if ($lineas){
            $inf = 'La sucursal está vinculada a proyectos existentes...';
            session()->flash('Error',$inf);
            return redirect()->route('sucursalesclientes',['id'=> $idc])->with('error',$inf);    
        }
        else{
            $sucursal = Sucursal::find($ids);
            $sucursal->delete();
            
            $inf = 'La sucursal se eliminó con éxito...';
            session()->flash('Exito',$inf);
            return redirect()->route('sucursalesclientes',['id'=> $idc])->with('message',$inf);
        }  
    }
    
}
