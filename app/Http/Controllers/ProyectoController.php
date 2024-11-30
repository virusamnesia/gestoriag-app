<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\EstadosProyecto;
use App\Models\ListasPrecio;
use App\Models\Proyecto;
use App\Models\Sucursal;
use App\Models\sucursales_proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProyectoController extends Controller
{
    public function index(){
        
        $proyectos =DB::table('proyectos')
        ->join('clientes', 'clientes.id', '=', 'proyectos.cliente_id')
        ->leftjoin('estados_proyectos', 'estados_proyectos.id', '=', 'proyectos.estados_proyecto_id')
        ->join('listas_precios', 'listas_precios.id', '=', 'proyectos.listas_precio_id')
        ->select('proyectos.*','clientes.nombre as cliente','clientes.id as cliente_id','estados_proyectos.nombre as estado',
        'estados_proyectos.id as estados_proyecto_id', 'listas_precios.nombre as lista', 'listas_precios.id as lista_id')
        ->orderBy('proyectos.id', 'desc')
        ->get();

        return view('proyecto.index', ['proyectos' => $proyectos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $listas = ListasPrecio::all();
        $estados = EstadosProyecto::all();
        $clientes = Cliente::all();

        return view('proyecto.create', ['clientes' => $clientes,'listas' => $listas, 'estados' => $estados]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $proyecto = new Proyecto();

        $proyecto->nombre = $request->nombre;
        $proyecto->anio = $request->año;
        $proyecto->cliente_id = $request->cliente;
        $proyecto->importe = 0;
        $proyecto->saldo = 0;
        $proyecto->estados_proyecto_id = 1;
        $proyecto->listas_precio_id = $request->lista;
        $proyecto->fecha_cotizacion = today();
        $proyecto->es_agrupado = $request->agrupado;

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

        $inf = 1;
        session()->flash('Exito','El proyecto se agregó con éxito...');
        return redirect()->route('proyectos.sucursales', ['idp' => $id,'idc' => $request->cliente])->with('info',$inf);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\proyecto  $proyecto
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
        
        $proyecto = DB::table('proyectos')
            ->join('ciudad_contactos', 'ciudad_contactos.id', '=', 'proyectos.ciudad_contacto_id')
            ->join('municipio_contactos', 'municipio_contactos.id', '=', 'proyectos.municipio_contacto_id')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'proyectos.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'proyectos.pais_contacto_id')
            ->select('proyectos.*')
            ->where('proyectos.id','=',$id)
            ->get();

        return view('proyecto.show', ['proyecto' => $proyecto,'municipios' => $municipios, 'bancos' => $bancos]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\proyecto  $proyecto
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
        
        $proyecto = DB::table('proyectos')
            ->join('ciudad_contactos', 'ciudad_contactos.id', '=', 'proyectos.ciudad_contacto_id')
            ->join('municipio_contactos', 'municipio_contactos.id', '=', 'proyectos.municipio_contacto_id')
            ->join('estado_contactos', 'estado_contactos.id', '=', 'proyectos.estado_contacto_id')
            ->join('pais_contactos', 'pais_contactos.id', '=', 'proyectos.pais_contacto_id')
            ->select('proyectos.*')
            ->where('proyectos.id','=',$id)
            ->get();

        return view('proyecto.edit', ['proyecto' => $proyecto,'municipios' => $municipios, 'bancos' => $bancos]);
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
        
        $proyecto = DB::table('proyectos')
            ->where('proyectos.id','=',$id)
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
        ]
        );

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
    public function destroy($id)
    {
        //
    }
    
}
