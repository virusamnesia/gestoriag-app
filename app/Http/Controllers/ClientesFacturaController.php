<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientesFacturaController extends Controller
{
    public function index(){
        
        $facturas = DB::table('clientes_facturas')
            ->join('clientes','clientes.id','=','clientes_facturas.cliente_id')
            ->join('proyectos','proyectos.id','=','clientes_facturas.proyecto_id')
            ->select('clientes_facturas.*','clientes.nombre as cliente','clientes.rfc as rfc','proyectos.nombre as proyecto')
            ->orderBy('clientes_facturas.id')
            ->get();

       
        return view('factclientes.index', ['facturas' => $facturas]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $proyectos = DB::table('proyectos')
            ->join('clientes','clientes.id','=','proyectos.cliente_id')
            ->select('proyectos.*','clientes.nombre as cliente','clientes.rfc as rfc')
            ->where('proyectos.cxc', '>',0)
            ->get();
        
        return view('factclientes/nuevo', ['proyectos' => $proyectos]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function show($idc, $idl)
    {
        
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
